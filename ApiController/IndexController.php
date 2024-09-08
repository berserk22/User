<?php

/**
 * @author Sergey Tevs
 * @email sergey@tevs.org
 */

namespace Modules\User\ApiController;

use DI\DependencyException;
use DI\NotFoundException;
use Modules\Rest\Manager\AbstractManager;
use Modules\User\UserTrait;
use OpenApi\Annotations as OA;
use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;

class IndexController extends AbstractManager {

    use UserTrait;

    /**
     * @return array
     */
    public function options(): array {
        return [];
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    protected function registerFunctions(): void {
        $this->getUserApiRouter()->getMapBuilder($this);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws DependencyException
     * @throws NotFoundException
     * @OA\Get(
     *      path="/user/list",
     *      summary="Users Liste",
     *      tags={"User"},
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          content={
     *              @OA\MediaType(
     *                  mediaType="application/json"
     *              )
     *          }
     *      ),
     *      security={
     *          {"oauth2": {"read"}}
     *      }
     * )
     */
    public function getUsers(Request $request, Response $response): Response {
        $request->getParsedBody();
        $user = $this->getUserManager()
            ->getUserEntity()::select('id', 'firstname', 'lastname', 'email', 'valid', 'newsletter', 'ban')->get();
        $this->getView()->setVariables($user->toArray());
        return $this->getView()->renderJson($response);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws DependencyException
     * @throws NotFoundException
     * @OA\Get(
     *   path="/user/{user_id}",
     *   summary="User Info",
     *   tags={"User"},
     *   @OA\Parameter(
     *      name="user_id",
     *      in="path",
     *      description="Customer ID",
     *      required=true,
     *      style="simple",
     *      @OA\Schema(
     *          type="integer",
     *          format="int64"
     *      )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     content={
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     }
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="No Auth",
     *     content={
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     }
     *   ),
     *   security={
     *     {"oauth2": {"read"}}
     *   }
     * )
     */
    public function getUser(Request $request, Response $response): Response {
        $user_id = $request->getAttribute('userId');
        $user = $this->getUserManager()
            ->getUserEntity()::select('id', 'firstname', 'lastname', 'email', 'valid', 'newsletter', 'ban')
            ->find($user_id);
        $this->getView()->setVariables($user->toArray());
        return $this->getView()->renderJson($response);
    }

}
