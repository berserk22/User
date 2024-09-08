<?php

/**
 * @author Sergey Tevs
 * @email sergey@tevs.org
 */

namespace Modules\User\Controller;

use Config\Modules;
use Core\Module\Dashboard;
use DI\DependencyException;
use DI\NotFoundException;
use Modules\Dashboard\DashboardTrait;
use Modules\User\UserTrait;
use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;

class DashboardController extends Dashboard {

    use DashboardTrait, UserTrait;

    /**
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    protected function registerFunctions(): void {
        $this->getUserDashboardRouter()->getMapBuilder($this);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function list(Request $request, Response $response): Response {
        if (!$this->getAuth()->getStatus()){
            return $response->withRedirect($this->getUserRouter()->getUrl('dashboard_login'));
        }

        $users = $this->getUserManager()->getUserEntity()::all();
        $this->getView()->setVariables([
            'seo'=>[
                'title'=>'Benutzer',
            ],
            'breadcrumbs'=>[
                'Dashboard'=>['dashboard_home'],
                'Benutzer'=>''
            ],
            'users'=>$users
        ]);
        return $this->getView()->render($response, 'user/list');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function userAdd(Request $request, Response $response): Response {
        if (!$this->getAuth()->getStatus()){
            return $response->withRedirect($this->getUserRouter()->getUrl('dashboard_login'));
        }

        $this->getView()->setVariables([
            'seo'=>[
                'title'=>'Neuer Benutzer',
            ],
            'breadcrumbs'=>[
                'Dashboard'=>['dashboard_home'],
                'Alle Benutzer'=>['dashboard_user_list'],
                'Neuer Benutzer' => ''
            ],
        ]);

        return $this->getView()->render($response, 'user/add');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function userEdit(Request $request, Response $response): Response {
        if (!$this->getAuth()->getStatus()){
            return $response->withRedirect($this->getUserRouter()->getUrl('dashboard_login'));
        }

        $user_id = $request->getAttribute('userId');
        $user = $this->getUserManager()->getUserEntity()::find($user_id);


        $this->getView()->setVariables([
            'seo'=>[
                'title'=>$user->firstname.' '.$user->lastname,
            ],
            'breadcrumbs'=>[
                'Dashboard'=>['dashboard_home'],
                'Alle Benutzer'=>['dashboard_user_list'],
                $user->firstname.' '.$user->lastname => ''
            ],
            'user'=>$user
        ]);

        return $this->getView()->render($response, 'user/edit');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function role(Request $request, Response $response): Response {
        $roles = $this->getUserManager()->getRoleEntity()::all();
        $this->getView()->setVariables([
            'seo'=>[
                'title'=>"Role",
            ],
            'breadcrumbs'=>[
                'Dashboard'=>['dashboard_home'],
                'Role'=>'',
            ],
            'roles'=>$roles
        ]);
        return $this->getView()->render($response, 'user/role');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function roleAdd(Request $request, Response $response): Response {
        if (!$this->getAuth()->getStatus()){
            return $response->withRedirect($this->getUserRouter()->getUrl('dashboard_login'));
        }

        $formData = $request->getParsedBody();

        if (!empty($formData)){
            $roleEntity = $this->getUserManager()->getRoleEntity();
            $rolePermissionEntity = $this->getUserManager()->getRolePermissionEntity();

            $permission = [];
            foreach ($formData as $key => $value){
                if ($key !== "title" && str_contains($key, '_')){
                    $permissionGroup = strstr($key, "_", true);
                    $permission[$permissionGroup][] = $key;
                }
            }

            $role = new $roleEntity();
            $rolePermission = new $rolePermissionEntity();

            $role->name = str_replace($this->chars, $this->replaceChars, strtolower($formData["title"]));
            $role->title = $formData["title"];
            $role->active = 1;
            $role->save();

            $rolePermission->role_id = $role->id;
            $rolePermission->setPermissions($permission);
            $rolePermission->save();

            return $this->getView()->renderJson($response, ["success"=>true]);
        }
        else {
            $permission_groups = $this->getUserManager()->getPermissionGroupEntity()::all();

            $this->getView()->setVariables([
                'seo'=>[
                    'title'=>"Neue Role",
                ],
                'breadcrumbs'=>[
                    'Dashboard'=>['dashboard_home'],
                    'Role'=>['dashboard_user_role'],
                    'Neue Role'=>''
                ],
                'permission_groups'=>$permission_groups
            ]);
            return $this->getView()->render($response, 'user/role_add');
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function roleEdit(Request $request, Response $response): Response {
        $roleId = $request->getAttribute('roleId');
        $role = $this->getUserManager()->getRoleEntity()::find($roleId);
        $permission_groups = $this->getUserManager()->getPermissionGroupEntity()::all();

        $formData = $request->getParsedBody();
        if (!empty($formData)){

            $permission = [];
            foreach ($formData as $key => $value){
                if ($key !== "title" && str_contains($key, '_')){
                    $permissionGroup = strstr($key, "_", true);
                    $permission[$permissionGroup][] = $key;
                }
            }

            if ($role->title !== $formData["title"]){
                $role->name = str_replace($this->chars, $this->replaceChars, strtolower($formData["title"]));
                $role->title = $formData["title"];
                $role->save();
            }

            $rolePermission = $role->getPermissions();
            $rolePermission->setPermissions($permission);
            $rolePermission->save();

            return $this->getView()->renderJson($response, ["success"=>true]);
        }
        else {
            $this->getView()->setVariables([
                'seo'=>[
                    'title'=>'Role: '.$role->title,
                ],
                'breadcrumbs'=>[
                    'Dashboard'=>['dashboard_home'],
                    'Role'=>['dashboard_user_role'],
                    $role->title=>'',
                ],
                'role'=>$role,
                'permission_groups'=>$permission_groups
            ]);
            return $this->getView()->render($response, 'user/role_edit');
        }
    }
}
