<?php

/**
 * @author Sergey Tevs
 * @email sergey@tevs.org
 */

namespace Modules\User\Plugins;

use DI\DependencyException;
use DI\NotFoundException;
use Modules\User\UserTrait;
use Modules\View\AbstractPlugin;

class GetPermission extends AbstractPlugin {

    use UserTrait;

    /**
     * @param string $permission
     * @return mixed
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function process(string $permission): mixed {
        return $this->getUserManager()->getPermissionEntity()::where("name", "=", $permission)->firstOrFail();
    }

}
