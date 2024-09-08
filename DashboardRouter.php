<?php

/**
 * @author Sergey Tevs
 * @email sergey@tevs.org
 */

namespace Modules\User;

use Modules\User\Controller\DashboardController;

class DashboardRouter extends \Core\Module\Router {

    use UserTrait;

    /**
     * @var string
     */
    public string $routerType = "dashboard_user";

    /**
     * @var string
     */
    public string $router = "/dashboard/user";

    /**
     * @var array
     */
    public array $config = [
        'title'=>'Benutzer',
        'icon'=>'ri-group-line',
        'items'=>[]
    ];

    /**
     * @var array|string[][]
     */
    public array $mapForUriBuilder = [
        'list' => [
            'callback' => 'list',
            'pattern' => '',
            'method'=>['GET', 'POST']
        ],
        'add' => [
            'callback' => 'userAdd',
            'pattern' => '/add',
            'method'=>['GET', 'POST']
        ],
        'edit' => [
            'callback' => 'userEdit',
            'pattern' => '/edit/{userId:[0-9]+}',
            'method'=>['GET', 'POST']
        ],
        'role' => [
            'callback' => 'role',
            'pattern' => '/role',
            'method'=>['GET', 'POST']
        ],
        'role_add' => [
            'callback' => 'roleAdd',
            'pattern' => '/role/add',
            'method'=>['GET', 'POST']
        ],
        'role_edit' => [
            'callback' => 'roleEdit',
            'pattern' => '/role/{roleId:[0-9]+}',
            'method'=>['GET', 'POST']
        ],
        'permission_group' => [
            'callback' => 'permissionGroup',
            'pattern' => '/permissions',
            'method'=>['GET', 'POST']
        ],
        'permission' => [
            'callback' => 'permission',
            'pattern' => '/permissions/{permission_group:[0-9]+}',
            'method'=>['GET', 'POST']
        ],
        'role_user' => [
            'callback' => 'roleUser',
            'pattern' => '/{user_id:[0-9]+}/role',
            'method'=>['GET', 'POST']
        ],
        'permission_user' => [
            'callback' => 'permissionUser',
            'pattern' => '/{user_id:[0-9]+}/permission',
            'method'=>['GET', 'POST']
        ],
    ];

    public string $controller = DashboardController::class;

}
