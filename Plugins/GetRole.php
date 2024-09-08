<?php

/**
 * @author Sergey Tevs
 * @email sergey@tevs.org
 */

namespace Modules\User\Plugins;

use DI\DependencyException;
use DI\NotFoundException;
use Modules\User\Db\Models\Role;
use Modules\User\UserTrait;
use Modules\View\AbstractPlugin;

class GetRole extends AbstractPlugin {

    use UserTrait;

    /**
     * @param int|null $id
     * @return Role
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function process(int $id = null): Role {
        return $this->getUserModel()->getUserRole($id);
    }

}
