<?php

/**
 * @author Sergey Tevs
 * @email sergey@tevs.org
 */

namespace Modules\User;

use Core\Traits\App;
use DI\DependencyException;
use DI\NotFoundException;
use Modules\Router\Manager\RouterManager;
use Modules\User\Manager\UserAuthModel;
use Modules\User\Manager\UserManager;
use Modules\User\Manager\UserModel;

trait UserTrait {

    use App;

    /**
     * @return Router|null
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getUserRouter(): ?Router {
        return $this->getContainer()->get('User\Router');
    }

    /**
     * @return Router|null
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getUserDashboardRouter(): ?DashboardRouter {
        return $this->getContainer()->get('User\DashboardRouter');
    }

    /**
     * @return Router|null
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getUserApiRouter(): ?ApiRouter {
        return $this->getContainer()->get('User\ApiRouter');
    }

    /**
     * @return UserManager
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getUserManager(): UserManager {
        return $this->getContainer()->get('User\Manager');
    }

    /**
     * @return UserModel
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getUserModel(): UserModel {
        return $this->getContainer()->get('User\Model');
    }

    /**
     * @return UserAuthModel|null
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getAuth(): UserAuthModel|null{
        return $this->getContainer()->get('User\Auth');
    }

    /**
     * @return RouterManager
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getRouterManager():RouterManager {
        return $this->getContainer()->get('Router\Manager');
    }
}
