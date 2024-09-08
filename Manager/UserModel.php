<?php

/**
 * @author Sergey Tevs
 * @email sergey@tevs.org
 */

namespace Modules\User\Manager;

use DI\DependencyException;
use DI\NotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Modules\User\Db\Models\Role;

class UserModel extends UserAuthModel {

    /**
     * @param int|null $id
     * @return Role|null
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getUserRole(int $id = null): Role|null {
        if ($this->getAuth()->getStatus() && $id === null){
            return $this->getUserManager()->getRoleEntity()::select('role.id', 'role.name', 'role.title')
                ->join('user', 'role.id', 'user.role_id')->where([
                ['user.id', '=', $this->getAuth()->getUserId()],
                ['role.active', '=', 1]
            ])->first();
        }
        elseif ($id !== null){
            return $this->getUserManager()->getRoleEntity()::select('role.id', 'role.name', 'role.title')
                ->join('user', 'role.id', 'user.role_id')->where([
                ['user.id', '=', $id],
                ['role.active', '=', 1]
            ])->first();
        }
        else {
            return null;
        }
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getUserPermission(){
        if ($this->getAuth()->getStatus()){
            return $this->getUserManager()
                ->getPermissionEntity()::select('permission.id', 'permission.name', 'permission.title')
                ->join('user_permission', 'permission.id', 'user_permission.permission_id')->where([
                ['user_permission.user_id', '=', $this->getAuth()->getUserId()],
                ['user_permission.active', '=', 1],
                ['permission.active', '=', 1]
            ])->get();
        }
        else {
            return null;
        }
    }

    /**
     * @param string|null $role
     * @return bool
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function isUserHasRole(string $role = null): bool {
        if ($this->getAuth()->getStatus() && $role !== null) {
            $role = $this->getUserManager()->getRoleEntity()::select('user_role.active')
                ->join('user_role', 'role.id', 'user_role.role_id')->where([
                ['role.name', '=', $role],
                ['user_role.user_id', '=', $this->getAuth()->getUserId()]
            ])->first();
            if ($role->active === 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string|null $permission
     * @return bool
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function isUserHasPermission(string $permission = null): bool {
        if ($this->getAuth()->getStatus() && $permission !== null) {
            $permissions = $this->getUserManager()->getRoleEntity()::select('role_permission.permissions')
                ->join('role_permission', 'role.id', 'role_permission.role_id')
                ->join('user', 'role.id', 'user.role_id')
                ->where('user.id', '=', $this->getAuth()->getUserId())
                ->first();

            $tmp = json_decode($permissions->permissions, true);

            foreach ($tmp as $tmpPermission) {
                if (in_array($permission, array_values($tmpPermission))) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @return Collection|array
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getGeneralPermission(): Collection|array {
        if ($this->getAuth()->getStatus()){
            return $this->getUserManager()->getPermissionGroupEntity()::select(
                'permission_group.name as group_name',
                'permission_group.title',
                'permission_group.icon',
                'permission_group.url_key',
            )
            ->where('permission_group.active', '=', 1)->get();
        }
        else {
            return [];
        }
    }


    /**
     * @return mixed
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getSidebarNavigation(): mixed {
        $general = $this->getGeneralPermission();
        $permission = $this->getUserRole()->getPermissionsArray();
        $sidebar = [];
        foreach($general as $gItem){
            if (isset($permission[$gItem->group_name])){
                $sidebar[$gItem->group_name]["group"] = $gItem;
                foreach($permission[$gItem->group_name] as $item){
                    if (!str_contains($item, "general")){
                        $tmpPermission = $this->getPermission($item);
                        if ($tmpPermission->menu_item === 1){
                            $sidebar[$gItem->group_name]["items"][] = $this->getPermission($item);
                        }
                    }
                }
            }
        }
        return $sidebar;
    }

    /**
     * @param string|null $name
     * @return mixed
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getPermission(string $name = null): mixed {
        return $this->getUserManager()->getPermissionEntity()::where("name", "=", $name)->first();
    }

    /**
     * @param int $parent
     * @return Collection|array
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getSubPermission(int $parent = 0): Collection|array {
        if ($this->getAuth()->getStatus()){
            $sub_permission =  $this->getUserManager()->getPermissionEntity()::select(
                'permission.id',
                'permission.name',
                'permission.title',
                'permission.url_key'
            )
            ->join('user_permission', 'permission.id', 'user_permission.permission_id')
            ->where([
                ['user_permission.user_id', '=', $this->getAuth()->getUserId()],
                ['permission.parent_id', '=', $parent],
                ['permission.active', '=', 1],
                ['user_permission.active', '=', 1]
            ])->get();
            if ($sub_permission->count() === 0) {
                return [];
            }
            else {
                return $sub_permission;
            }
        }
        else {
            return [];
        }
    }

    /**
     * @param array $formData
     * @return array
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function register(array $formData): array {
        $data['success'] = false;
        $user_check = $this->getUserManager()->getUserEntity()::where('email', '=', $formData['email'])->first();
        if ($user_check === null){
            $userEntity = $this->getUserManager()->getUserEntity();
            $user = new $userEntity();

            $this->getAuth()->setHash($formData["password"]);

            $user->firstname = $formData["firstname"];
            $user->lastname = $formData["lastname"];
            $user->email = $formData["email"];
            $user->password = $this->getAuth()->getHash();
            $user->save();

            $data["success"]=true;
            $this->setUserId($user->id);
        }
        else {
            $data['errorMessage']="Die angegebene E-Mail-Adresse existiert bereits.";
        }

        return $data;
    }

    /**
     * @param array $formData
     * @return array
     */
    public function login(array $formData = []): array {
        return $formData;
    }

}
