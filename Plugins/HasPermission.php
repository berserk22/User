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

class HasPermission extends AbstractPlugin {

    use UserTrait;

    /**
     * @param string|null $permission
     * @return bool
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function process(string $permission = null): bool {
        return $this->getUserModel()->isUserHasPermission($permission);
    }

}
