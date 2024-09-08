<?php

/**
 * @author Sergey Tevs
 * @email sergey@tevs.org
 */

namespace Modules\User;

class ApiRouter extends \Core\Module\ApiRouter {

    use UserTrait;

    /**
     * @var int
     */
    public int $version = 1;

    /**
     * @var string
     */
    public string $routerType = "user";

    /**
     * @var array|array[]
     */
    public array $mapForUriBuilder = [
        'list' => [
            'callback' => 'getUsers',
            'pattern' =>'/list',
            'method'=>['GET']
        ],
        'details' => [
            'callback' => 'getUser',
            'pattern' =>'/{userId:[0-9]+}',
            'method'=>['GET']
        ],
    ];

    public string $controller = ApiController\IndexController::class;

}
