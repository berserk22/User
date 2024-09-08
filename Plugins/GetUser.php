<?php

/**
 * @author Sergey Tevs
 * @email sergey@tevs.org
 */

namespace Modules\User\Plugins;

use DI\DependencyException;
use DI\NotFoundException;
use Modules\User\Db\Models\User;
use Modules\User\Manager\UserAuthModel;
use Modules\User\Manager\UserManager;
use Modules\User\UserTrait;
use Modules\View\AbstractPlugin;

class GetUser extends AbstractPlugin {

    use UserTrait;

    /**
     * @param int $user_id
     * @return User|false
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function process(int $user_id = 0): User|false {
        if ($user_id !== 0){
            return $this->getUserManager()->getUserEntity()::find($user_id);
        }
        else {
            if ($this->getAuth()->getStatus() === false){
                return false;
            }
            return $this->getUserManager()->getUserEntity()::find($this->getAuth()->getUserId());
        }
    }

}
