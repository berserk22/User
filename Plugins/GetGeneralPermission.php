<?php

/**
 * @author Sergey Tevs
 * @email sergey@tevs.org
 */

namespace Modules\User\Plugins;

use DI\DependencyException;
use DI\NotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Modules\User\UserTrait;
use Modules\View\AbstractPlugin;

class GetGeneralPermission extends AbstractPlugin {

    use UserTrait;

    /**
     * @return array|Collection
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function process(): Collection|array {
        return $this->getUserModel()->getGeneralPermission();
    }
}
