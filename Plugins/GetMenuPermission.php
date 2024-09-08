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

class GetMenuPermission extends AbstractPlugin {

    use UserTrait;

    /**
     * @return mixed
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function process(): mixed {
        return $this->getUserModel()->getSidebarNavigation();
    }

}
