<?php

/**
 * @author Sergey Tevs
 * @email sergey@tevs.org
 */

namespace Modules\User\Controller;

use Core\Module\Controller;
use DI\DependencyException;
use DI\NotFoundException;
use Modules\Main\MainTrait;
use Modules\Payment\Manager\PaymentModel;
use Modules\User\UserTrait;
use Slim\Http\ServerRequest as Request;
use Slim\Http\Response;

class IndexController extends Controller {

    use UserTrait, MainTrait;

    /**
     * @var string
     */
    protected string $nofollow = "noindex, nofollow";

    /**
     * @var string[][]
     */
    protected array $modalTitle = [
        'edit' => [
            'phone' => 'Telefonnummer ändern',
            'password' => 'Passwort ändern',
            'email' => 'E-Mail-Adresse ändern'
        ]
    ];

    /**
     * @var string
     */
    protected string $newCryptMail = "Neues Passwort";

    protected string $errorMsg = "E-Mail Adresse oder Passwort falsch.";

    /**
     * @var string
     */
    protected string $passChar = "0123456789qwertzuiopasdfghjklyxcvbnm!?()[]";

    private string $contentType = "text/html";

    /**
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    protected function registerFunctions(): void {
        $this->getUserRouter()->getMapBuilder($this);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function profile(Request $request, Response $response): Response {
        if (!$this->getAuth()->getStatus()){
            return $response->withRedirect($this->getUserRouter()->getUrl('user_login'));
        }

        $user = $this->getUserManager()->getUserEntity()::find($this->getAuth()->getUserId());
        $this->getView()->setVariables([
            'seo'=>[
                'title' => 'Meine Konto',
                'robots'=>$this->nofollow
            ],
            'breadcrumbs' => [
                'Home' => ['main_home'],
                'Meine Konto' => ''
            ],
            'user'=>$user
        ]);
        return $this->getView()->render($response, 'user/profile');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function login(Request $request, Response $response): Response {
        if ($this->getAuth()->getStatus()){
            return $response->withRedirect($this->getUserRouter()->getUrl('user_profile'));
        }

        $formData = $request->getParsedBody();
        $data['success'] = false;
        if (!empty($formData)){
            if (isset($formData['email']) && filter_var($formData['email'], FILTER_VALIDATE_EMAIL)){
                $data['success'] = false;
                $user = $this->getUserManager()->getUserEntity()::where('email', '=', $formData['email'])->first();
                $data = $this->checkUser($user, $formData);
            }
            else {
                $data["errorMessage"] = $this->errorMsg;
            }
            return $this->getView()->renderJson($response, $data);
        }
        else {
            $this->getView()->setVariables([
                'seo'=>[
                    'title' => 'Anmelden',
                    'robots'=>$this->nofollow
                ],
                'breadcrumbs' => [
                    'Home' => ['main_home'],
                    'Anmelden' => ''
                ],
            ]);
            return $this->getView()->render($response, 'user/login');
        }
    }

    /**
     * @param $user
     * @param array $formData
     * @return array
     * @throws DependencyException
     * @throws NotFoundException
     */
    protected function checkUser($user, array $formData = []): array {
        $data = [];
        if ($user!==null){
            $this->getAuth()->setHash($formData['password']);
            if ($this->getAuth()->getHash() === $user->password){
                $this->getAuth()->setUserId($user->id);
                $data = [
                    'success' => true,
                    'redirect' => $this->getUserRouter()->getUrl('user_profile')
                ];
            }
            else {
                $data["errorMessage"] = $this->errorMsg;
            }
        }
        else {
            $data["errorMessage"] = $this->errorMsg;
        }
        return $data;
    }

    /**
    * @param Request $request
    * @param Response $response
    * @return Response
    * @throws DependencyException
    * @throws NotFoundException
     */
    public function forgot(Request $request, Response $response): Response {
        if ($this->getAuth()->getStatus()){
            return $response->withRedirect($this->getUserRouter()->getUrl('user_profile'));
        }
        $formData = $request->getParsedBody();
        if (!empty($formData)){
            $data = [
                'success'=>false
            ];
            if (filter_var($formData['email'], FILTER_VALIDATE_EMAIL)){
                $user = $this->getUserManager()->getUserEntity()::where('email', '=', $formData['email'])->first();

                if ($user !== null){

                    $key = hash("sha512", time().$user->email);
                    $url = $this->getUserRouter()->getUrl("user_new_password", ["hash"=>$key]);

                    $routerEntity = $this->getRouterManager()->getRoutersEntity();
                    $router = new $routerEntity();
                    $router->name = "user_new_password";
                    $router->group = 0;
                    $router->method = json_encode(["GET", "POST"]);
                    $router->route = $url;
                    $router->class = IndexController::class;
                    $router->action = "newPassword";
                    $router->attr = json_encode(["email"=>$user->email, "key"=>$key]);
                    $router->status = 1;
                    $router->save();

                    $queueSetting = $this->getConfig("queue", "mail");
                    $domainSetting = $this->getConfig("domain");
                    $mail = new \Swift_Message();
                    $message = $this->getView()->getHtml('mail/forgot', [
                        'name'=>$user->firstname." ".$user->lastname,
                        'url'=>$domainSetting["protocol"].'://'.$domainSetting["name"].$url
                    ]);
                    $mail->addFrom($queueSetting["email"], $queueSetting["name"]);
                    $mail->addTo($user->email, $user->firstname." ".$user->lastname, "");
                    $mail->setSubject("Passwort zurücksetzen bei Selfsol!");
                    $mail->setBody($message, $this->contentType);
                    $this->setMessage('mail', $mail);

                }

                $data['success'] = true;
                $data['successMessage'] = "Wenn Sie mit dieser Adresse bereits registriert sind, erhalten ".
                    "Sie eine E-Mail. Doch noch nicht angemeldet? Dann registrieren Sie sich gerne direkt – hier ".
                    "können Sie Ihr Passwort festlegen.";
            }
            else {
                $data['form']['email'] = 'E-Mail-Adresse ist falsch.';
            }
            return $this->getView()->renderJson($response, $data);
        }
        else {
            $this->getView()->setVariables([
                'seo'=>[
                    'title' => 'Passwort vergessen?',
                    'robots'=>$this->nofollow
                ],
                'breadcrumbs' => [
                    'Home' => ['main_home'],
                    'Passwort vergessen?' => ''
                ],
            ]);
            return $this->getView()->render($response, 'user/forgot');
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function newPassword(Request $request, Response $response): Response {
        $email = "";
        if (!is_null($request->getAttribute("email"))){
            $email = $request->getAttribute("email");
            $key = $request->getAttribute("key");

            $formData = $request->getParsedBody();
            if (!empty($formData)){
                $this->getAuth()->setHash($formData["password"]);
                $user = $this->getUserManager()->getUserEntity()::where(["email"=>$email])->first();
                $user->password = $this->getAuth()->getHash();
                $user->save();
                $this->getRouterManager()->getRoutersEntity()::where(["route"=>$this->getUserRouter()
                    ->getUrl("user_new_password", ["hash"=>$key])])->delete();
                return $this->getView()->renderJson($response, [
                    "success"=>true,
                    "redirect"=>$this->getUserRouter()->getUrl("user_login")
                ]);
            }
            else {
                $this->getView()->setVariables([
                    "seo"=>[
                        "title"=>$this->newCryptMail
                    ],
                    "breadcrumbs"=>[
                        "Home"=>["main_home"],
                        $this->newCryptMail => ""
                    ],
                    "url"=>$this->getUserRouter()->getUrl("user_new_password", ["hash"=>$key])
                ]);
                return $this->getView()->render($response, "user/new_password");
            }
        }
        else {
            return $this->getView()->render($response, "error/404")->withStatus(404);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function logout(Request $request, Response $response): Response {
        $this->getSession()->remove("shipping_address");
        $this->getSession()->remove("billing_address");
        if (class_exists("PaymentModel")){
            $this->getSession()->remove(PaymentModel::SESSION_PAYMENTID);
            $this->getSession()->remove(PaymentModel::SESSION_TRANSACTION_ID);
            $this->getSession()->remove(PaymentModel::SESSION_REFERENCE_KEY);
            $this->getSession()->remove(PaymentModel::SESSION_REFERENCE_UUID);
            $this->getSession()->remove(PaymentModel::SESSION_PAYPAL_CANCEL);
        }
        $this->getAuth()->removeUserId();
        return $response->withRedirect($this->getUserRouter()->getUrl('user_login'));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function register(Request $request, Response $response): Response {
        if ($this->getAuth()->getStatus()){
            return $response->withRedirect($this->getUserRouter()->getUrl('user_profile'));
        }
        $formData = $request->getParsedBody();
        $data['success'] = false;
        if (!empty($formData)){
            $user_check = $this->getUserManager()->getUserEntity()::where('email', '=', $formData['email'])->first();
            if ($user_check === null){
                $this->getAuth()->setHash($formData["password"]);
                $userEntity = $this->getUserManager()->getUserEntity();
                $user = new $userEntity();
                $user->firstname = $formData["firstname"];
                $user->lastname = $formData["lastname"];
                $user->email = $formData["email"];
                $user->password = $this->getAuth()->getHash();
                $user->phone = $formData["phone"];
                $user->mobile = $formData["mobile"];
                $user->role_id = 1;
                $user->valid = 1;
                $user->ban = 0;
                $user->newsletter = 1;
                $user->save();

                $this->getAuth()->setUserId($user->id);

                $addressEntity = $this->getUserManager()->getAddressEntity();
                $address_type = $this->getUserManager()->getAddressTypeEntity()::all();
                foreach($address_type as $type){
                    $address = new $addressEntity();
                    $address->user_id = $user->id;
                    $address->firstname = $formData["firstname"];
                    $address->lastname = $formData["lastname"];
                    $address->company = $formData["company"];
                    $address->street = $formData["street"];
                    $address->number = $formData["number"];
                    $address->zip = $formData["zip"];
                    $address->city = $formData["city"];
                    $address->country = "DE";
                    $address->address_type = $type->type;
                    $address->active = 1;
                    $address->save();
                }
                $data['success']=true;
                $data['redirect'] = $this->getUserRouter()->getUrl("user_profile");

                $queueSetting = $this->getConfig("queue", "mail");
                $mail = new \Swift_Message();
                $message = $this->getView()->getHtml('mail/register', [
                    'name'=>$user->firstname." ".$user->lastname
                ]);
                $mail->addFrom($queueSetting["email"], $queueSetting["name"]);
                $mail->addTo($user->email, $user->firstname." ".$user->lastname, "");
                $mail->setSubject("Willkommen bei Selfsol!");
                $mail->setBody($message, $this->contentType);
                $this->setMessage('mail', $mail);

                $key = hash("sha512", time().$user->email);
                $url = $this->getUserRouter()->getUrl("user_email_confirm", ["hash"=>$key]);

                $routerEntity = $this->getRouterManager()->getRoutersEntity();
                $router = new $routerEntity();
                $router->name = "user_email_confirm";
                $router->group = 0;
                $router->method = json_encode(["GET"]);
                $router->route = $url;
                $router->class = IndexController::class;
                $router->action = "emailConfirm";
                $router->attr = json_encode(["email"=>$user->email, "key"=>$key]);
                $router->status = 1;
                $router->save();

                $domainSetting = $this->getConfig("domain");
                $mail = new \Swift_Message();
                $message = $this->getView()->getHtml('mail/email_confirm', [
                    'name'=>$user->firstname." ".$user->lastname,
                    'url'=>$domainSetting["protocol"].'://'.$domainSetting["name"].$url
                ]);
                $mail->addFrom($queueSetting["email"], $queueSetting["name"]);
                $mail->addTo($user->email, $user->firstname." ".$user->lastname, "");
                $mail->setSubject("Bitte bestätige deine E-Mail-Adresse bei Selfsol!");
                $mail->setBody($message, $this->contentType);
                $this->setMessage('mail', $mail);

            }
            else {
                $data['errorMessage']="Die angegebene E-Mail-Adresse existiert bereits.";
            }
            return $this->getView()->renderJson($response, $data);
        }
        else {
            $this->getView()->setVariables([
                'seo'=>[
                    'title' => 'Registrierung',
                    'robots' => $this->nofollow
                ],
                'breadcrumbs' => [
                    'Home' => ['main_home'],
                    'Registrierung' => ''
                ],
            ]);
            return $this->getView()->render($response, 'user/register');
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function emailConfirm(Request $request, Response $response): Response {
        $email = "";
        if (!is_null($request->getAttribute("email"))){
            $email = $request->getAttribute("email");

            $user = $this->getUserManager()->getUserEntity()::where(["email"=>$email])->first();
            $user->valid = 1;
            $user->save();
            return $this->getView()->render($response, "user/email_confirm");
        }
        else {
            return $this->getView()->render($response, "error/404")->withStatus(404);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function address(Request $request, Response $response): Response {
        $query = $request->getQueryParams();
        if (isset($query["action"]) && $query["action"]==="edit"){
            $formData = $request->getParsedBody();
            if (!empty($formData)){
                $address = $this->getUserManager()->getAddressEntity()::where([
                    ["address_type", "=", $query["type"]],
                    ["active", "=", 1],
                    ["user_id", "=", $this->getAuth()->getUserId()]
                ])->first();

                $address->firstname = $formData["firstname"];
                $address->lastname = $formData["lastname"];
                $address->company = $formData["company"];
                $address->street = $formData["street"];
                $address->number = $formData["number"];
                $address->zip = $formData["zip"];
                $address->city = $formData["city"];
                $address->save();

                $this->getView()->setVariables([
                    "success"=>true,
                ]);

                return $this->getView()->renderJson($response);
            }
            else {
                $address = $this->getUserManager()->getAddressEntity()::where([
                    ["address_type", "=", $query["type"]],
                    ["active", "=", 1],
                    ["user_id", "=", $this->getAuth()->getUserId()]
                ])->first();
                $this->getView()->setVariables([
                    "title" => "Adresse ändern",
                    "param" => "type=".$query["type"]."&action=".$query["action"],
                    "address" => $address
                ]);
                return $this->getView()->render($response, "user/modal/address-edit");
            }
        }
        else {
            $addresses = $this->getUserManager()->getAddressEntity()::where([
                ["user_id", "=", $this->getAuth()->getUserId()]
            ])->get();
            $this->getView()->setVariables([
                'seo'=>[
                    'title' => 'Meine Anschriften'
                ],
                'breadcrumbs' => [
                    'Home' => ['main_home'],
                    'Meine Anschriften' => ''
                ],
                'addresses'=>$addresses
            ]);
            return $this->getView()->render($response, 'user/address');
        }

    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function settings(Request $request, Response $response): Response {
        if (!$this->getAuth()->getStatus()){
            return $response->withRedirect($this->getUserRouter()->getUrl('user_login'));
        }

        $data_post = $request->getParsedBody();
        $data_get = $request->getQueryParams();

        if (isset($data_get['action']) && $data_get['action'] === 'edit') {
            return $this->settingsAction($response, $data_post, $data_get);
        }
        return $response->withStatus(404);
    }

    /**
     * @param Response $response
     * @param array $data_post
     * @param array $data_get
     * @return Response
     * @throws DependencyException
     * @throws NotFoundException
     */
    protected function settingsAction(Response $response, array $data_post, array $data_get): Response {
        $data['success'] = false;
        $user = $this->getUserManager()->getUserEntity()::find($this->getAuth()->getUserId());

        if (!empty($data_post)) {
            return $this->setSettings($response, $user, $data_get, $data_post);
        } else {
            $data = [
                'user' => $user,
                'title' => $this->modalTitle[$data_get['action']][$data_get['type']],
                'param' => 'type='.$data_get['type'].'&action='.$data_get['action']
            ];
            return $this->getView()
                ->fetch($response, 'user/modal/'.$data_get['type'].'-'.$data_get['action'], $data);
        }
    }

    /**
     * @param $response
     * @param $user
     * @param array $data_get
     * @param array $data_post
     * @return Response
     * @throws DependencyException
     * @throws NotFoundException
     */
    protected function setSettings($response, $user, array $data_get = [], array $data_post = []): Response {
        if ($data_get['type'] === 'password') {
            $this->getAuth()->setHash($data_post['current_password']);
            if ($user->password === $this->getAuth()->getHash()) {
                $this->getAuth()->setHash($data_post[$data_get['type']]);
                $data_post[$data_get['type']] = $this->getAuth()->getHash();
            } else {
                $data['errorMessage'] = "<strong>Achtung: </strong> Dein aktuelles Passwort ist nicht korrekt.";
                $data['form']['current_password'] = "";
                return $this->getView()->renderJson($response, $data);
            }
        }

        try {
            $type = $data_get['type'];
            $user->$type = $data_post[$data_get['type']];
            $user->save();
        } catch (\Exception $e) {
            $data['errorMessage'] = $e->getMessage();
            return $this->getView()->renderJson($response, $data);
        }

        $data = [
            'success' => true,
            'redirect' => $this->getUserRouter()->getUrl('user_profile')
        ];
        return $this->getView()->renderJson($response, $data);
    }

}
