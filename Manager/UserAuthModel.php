<?php

/**
 * @author Sergey Tevs
 * @email sergey@tevs.org
 */

namespace Modules\User\Manager;

use DI\DependencyException;
use DI\NotFoundException;
use Modules\Session\SessionManager;
use Modules\User\UserTrait;

class UserAuthModel {

    use UserTrait;

    /**
     * @var string
     */
    private string $secret="Secret";

    /**
     * @var string
     */
    private string $hash = "";

    /**
     * @return SessionManager|null
     * @throws DependencyException
     * @throws NotFoundException
     */
    private function getSession(): ?SessionManager{
        if ($this->getContainer()->has('Session\Manager')){
            return $this->getContainer()->get('Session\Manager');
        }
        else {
            return null;
        }
    }

    /**
     * @return bool
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getStatus(): bool {
        return $this->getSession()->hasUserId();
    }

    /**
     * @param int $id
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function setUserId(int $id): void {
        $this->getSession()->setUserId($id);
    }

    /**
     * @return int
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getUserId(): int {
        return $this->getSession()->getUserId();
    }

    /**
     * @param $str
     */
    public function setHash($str): void {
        $hash = hash("sha512", $str);
        $this->hash=hash("sha512", $this->secret.strrev($this->secret.$hash));
    }

    /**
     * @return string
     */
    public function getHash(): string {
        return $this->hash;
    }

    /**
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function removeUserId(): void {
        $this->getSession()->remove('user_id');
    }

}
