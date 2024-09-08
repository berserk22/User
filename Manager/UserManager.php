<?php

/**
 * @author Sergey Tevs
 * @email sergey@tevs.org
 */

namespace Modules\User\Manager;

use Core\Traits\App;
use DI\DependencyException;
use DI\NotFoundException;

class UserManager {

    use App;

    /**
     * @var string
     */
    protected string $user = "User\User";

    /**
     * @var string
     */
    protected string $role = "User\Role";

    /**
     * @var string
     */
    protected string $rolePermission = "User\RolePermission";

    /**
     * @var string
     */
    protected string $permissionGroup = "User\PermissionGroup";

    /**
     * @var string
     */
    protected string $permission = "User\Permission";

    /**
     * @var string
     */
    protected string $userPermission = "User\UserPermission";

    /**
     * @var string
     */
    protected string $address = "User\Address";

    /**
     * @var string
     */
    protected string $addressType = "User\AddressType";

    /**
     * @return $this
     */
    public function initEntity(): static {
        if (!$this->getContainer()->has($this->user)){
            $this->getContainer()->set($this->user, function(){
                return 'Modules\User\Db\Models\User';
            });
        }

        if (!$this->getContainer()->has($this->role)){
            $this->getContainer()->set($this->role, function () {
                return "Modules\User\Db\Models\Role";
            });
        }

        if (!$this->getContainer()->has($this->rolePermission)){
            $this->getContainer()->set($this->rolePermission, function () {
                return "Modules\User\Db\Models\RolePermission";
            });
        }

        if (!$this->getContainer()->has($this->permissionGroup)){
            $this->getContainer()->set($this->permissionGroup, function () {
                return "Modules\User\Db\Models\PermissionGroup";
            });
        }

        if (!$this->getContainer()->has($this->permission)){
            $this->getContainer()->set($this->permission, function () {
                return "Modules\User\Db\Models\Permission";
            });
        }

        if (!$this->getContainer()->has($this->userPermission)){
            $this->getContainer()->set($this->userPermission, function () {
                return "Modules\User\Db\Models\UserPermission";
            });
        }

        if (!$this->getContainer()->has($this->address)){
            $this->getContainer()->set($this->address, function () {
                return "Modules\User\Db\Models\Address";
            });
        }

        if (!$this->getContainer()->has($this->addressType)){
            $this->getContainer()->set($this->addressType, function () {
                return "Modules\User\Db\Models\AddressType";
            });
        }

        return $this;
    }

    /**
     * @return string
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getUserEntity(): string {
        return $this->getContainer()->get($this->user);
    }

    /**
     * @return string
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getRoleEntity(): string {
        return $this->getContainer()->get($this->role);
    }

    /**
     * @return string
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getRolePermissionEntity(): string {
        return $this->getContainer()->get($this->rolePermission);
    }

    /**
     * @return string
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getPermissionGroupEntity(): string {
        return $this->getContainer()->get($this->permissionGroup);
    }

    /**
     * @return string
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getPermissionEntity(): string {
        return $this->getContainer()->get($this->permission);
    }

    /**
     * @return string
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getUserPermissionEntity(): string {
        return $this->getContainer()->get($this->userPermission);
    }

    /**
     * @return string
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getAddressEntity(): string {
        return $this->getContainer()->get($this->address);
    }

    /**
     * @return string
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getAddressTypeEntity(): string {
        return $this->getContainer()->get($this->addressType);
    }
}
