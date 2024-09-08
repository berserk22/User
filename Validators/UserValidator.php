<?php

/**
 * @author Sergey Tevs
 * @email sergey@tevs.org
 */

namespace Modules\User\Validators;

use DI\DependencyException;
use DI\NotFoundException;
use Modules\Cart\Interfaces\ValidatorsInterface;
use Modules\User\UserTrait;

class UserValidator implements ValidatorsInterface {

    use UserTrait;

    /**
     * @var array
     */
    private array $errors = [];

    /**
     * @var array
     */
    protected array $formData = [];

    /**
     * @return bool
     */
    public function isValid(): bool {
        if (empty($this->errors)){
            return true;
        }
        return false;
    }

    /**
     * @param array $formData
     * @return void
     */
    public function setData(array $formData): void {
        $this->formData = $formData;
    }

    /**
     * @return array
     */
    public function getError(): array {
        return $this->errors;
    }

    /**
     * @param string $key
     * @param string $msg
     * @return void
     */
    public function addError(string $key, string $msg): void {
        $this->errors[$key] = $msg;
    }

    /**
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function registryValidator(): void {
        if (
            !empty($this->formData)&&
            (isset($this->formData['new_account'])&&
                $this->formData['new_account'] === 'yes')
        ){
            $data = $this->getUserModel()->register($this->formData);
            if ($data['success'] !== true){
                $this->addError('errorMessage', $data['errorMessage']);
            }
        }
    }

    /**
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function loginValidator(): void {
        $model = $this->getUserModel();
        if (!empty($this->formData)){
            $data = $model->login($this->formData);
            if ($data['success'] !== true){
                $this->addError('errorMessage', $data['errorMessage']);
            }
        }
        else {
            if (!$model->getAuth()->getStatus()){
                $this->addError('errorMessage', 'Sie sind nicht angemeldet.');
            }
        }
    }

}
