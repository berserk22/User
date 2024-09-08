<?php

/**
 * @author Sergey Tevs
 * @email sergey@tevs.org
 */

namespace Modules\User;

use Core\Module\Provider;
use DI\DependencyException;
use DI\NotFoundException;
use Modules\Database\MigrationCollection;
use Modules\User\Console\GeneratePermission;
use Modules\User\Console\Hash;
use Modules\User\Db\Schema;
use Modules\User\Manager\UserAuthModel;
use Modules\User\Manager\UserManager;
use Modules\User\Manager\UserModel;
use Modules\User\Validators\UserValidator;
use Modules\View\PluginManager;
use Modules\View\ViewManager;

class ServiceProvider extends Provider {

    /**
     * @var string
     */
    protected string $router = "User\Router";

    /**
     * @var string
     */
    protected string $dashboardRouter = "User\DashboardRouter";

    /**
     * @var string
     */
    protected string $apiRouter = "User\ApiRouter";

    public function console(): array {
        return [
            Hash::class,
            GeneratePermission::class
        ];
    }

    /**
     * @var array|string[]
     */
    protected array $plugins = [
        'getUser' => '\Modules\User\Plugins\GetUser',
        'getRole' => '\Modules\User\Plugins\GetRole',
        'getGeneralPermission' => '\Modules\User\Plugins\GetGeneralPermission',
        'getSubPermission' => '\Modules\User\Plugins\GetSubPermission',
        'getPermission' => '\Modules\User\Plugins\GetPermission',
        'getMenuPermission' => '\Modules\User\Plugins\GetMenuPermission',
        'hasPermission' => '\Modules\User\Plugins\HasPermission'
    ];

    /**
     * @return void
     */
    public function init(): void {
        $container = $this->getContainer();

        if (!$container->has('User\Auth')){
            $container->set('User\Auth', function(){
                return new UserAuthModel($this);
            });
        }

        if (!$container->has($this->router)){
            $container->set($this->router, function(){
                return new Router($this);
            });
        }

        if (!$container->has($this->dashboardRouter)){
            $container->set($this->dashboardRouter, function(){
                return new DashboardRouter($this);
            });
        }

        if (!$container->has($this->apiRouter)){
            $container->set($this->apiRouter, function(){
                return new ApiRouter($this);
            });
        }

        if (!$container->has('Validators:UserValidator')){
            $container->set('Validators:UserValidator', function() {
                return new UserValidator($this);
            });
        }
    }

    /**
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function afterInit(): void {
        $container = $this->getContainer();
        if ($container->has('Modules\Database\ServiceProvider::Migration::Collection')) {
            /* @var $databaseMigration MigrationCollection  */
            $container->get('Modules\Database\ServiceProvider::Migration::Collection')->add(new Schema($this));
        }

        if (!$container->has('User\Manager')){
            $this->getContainer()->set('User\Manager', function(){
                $manager = new UserManager($this);
                return $manager->initEntity();
            });
        }

        if (!$container->has('User\Model')){
            $this->getContainer()->set('User\Model', function(){
                return new UserModel($this);
            });
        }

        if ($container->has('ViewManager::View')){
            /** @var $viewer ViewManager */
            $viewer = $container->get('ViewManager::View');
            $plugins = function(){
                $pluginManager = new PluginManager();
                $pluginManager->addPlugins($this->plugins);
                return $pluginManager->getPlugins();
            };
            $viewer->setPlugins($plugins());
        }
    }

    /**
     * @return void
     */
    public function boot(): void {
        $container = $this->getContainer();
        $container->set('Modules\User\Controller\IndexController', function(){
            return new Controller\IndexController($this);
        });

        $container->set('Modules\User\Controller\DashboardController', function(){
            return new Controller\DashboardController($this);
        });

        $container->set('Modules\User\ApiController\IndexController', function(){
            return new ApiController\IndexController($this);
        });
    }

    /**
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function register(): void {
        $container = $this->getContainer();

        if ($container->has($this->dashboardRouter)){
            $container->get($this->dashboardRouter)->init();
        }

        if ($container->has($this->router)){
            $container->get($this->router)->init();
        }

        if ($container->has($this->apiRouter)){
            $container->get($this->apiRouter)->init();
        }
    }

}
