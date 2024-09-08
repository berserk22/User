<?php

/**
 * @author Sergey Tevs
 * @email sergey@tevs.org
 */

namespace Modules\User;

use Modules\User\Controller\IndexController;

class Router extends \Core\Module\Router {

    use UserTrait;

    /**
     * @var string
     */
    public string $routerType = "user";

    /**
     * @var string
     */
    public string $router = "/user";

    /**
     * @var array|string[][]
     */
    public array $mapForUriBuilder = [
        'profile' => [
            'callback' => 'profile',
            'pattern' => '/profile',
            'method'=>['GET', 'POST']
        ],
        'address' => [
            'callback' => 'address',
            'pattern' => '/address',
            'method'=>['GET', 'POST']
        ],
        'login' => [
            'callback' => 'login',
            'pattern' => '/login',
            'method'=>['GET', 'POST']
        ],
        'register' => [
            'callback' => 'register',
            'pattern' => '/register',
            'method'=>['GET', 'POST']
        ],
        'logout' => [
            'callback' => 'logout',
            'pattern' => '/logout',
            'method'=>['GET']
        ],
        'password_forgot' => [
            'callback' => 'forgot',
            'pattern' => '/forgot',
            'method'=>['GET']
        ],
        'new_password' => [
            'callback' => 'newPassword',
            'pattern' => '/new_pwd/{hash:[a-z0-9]+}',
            'method'=>['GET']
        ],
        'settings' => [
            'callback' => 'settings',
            'pattern' => '/settings',
            'method'=>['GET', 'POST']
        ],
        'email_confirm' => [
            'callback' => 'emailConfirm',
            'pattern' => '/email_confirm/{hash:[a-z0-9]+}',
            'method'=>['GET']
        ],
    ];

    public string $controller = IndexController::class;

}
