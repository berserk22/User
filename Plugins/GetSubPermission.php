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

class GetSubPermission extends AbstractPlugin {

    use UserTrait;

    /**
     * @param int $parent
     * @return Collection|array
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function process(int $parent = 0): Collection|array {
        return $this->getUserModel()->getSubPermission($parent);
    }

}
