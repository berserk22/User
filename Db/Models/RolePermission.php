<?php

/**
 * @author Sergey Tevs
 * @email sergey@tevs.org
 */

namespace Modules\User\Db\Models;

use Modules\Database\Model;

class RolePermission extends Model {

    protected $table = "role_permission";

    /**
     * @param string $permission_group_name
     * @param string $permission_name
     * @return bool
     */
    public function hasPermission(string $permission_group_name, string $permission_name): bool {
        if (!isset(json_decode($this->permissions, true)[$permission_group_name])) {
            return false;
        }
        elseif (!in_array($permission_name, json_decode($this->permissions, true)[$permission_group_name])) {
            return false;
        }
        else {
            return true;
        }
    }

    public function hasPermissionGroup(string $permission_group_name): bool {
        if (!isset(json_decode($this->permissions, true)[$permission_group_name])) {
            return false;
        }
        return true;
    }

    /**
     * @param array $permissions
     * @return void
     */
    public function setPermissions(array $permissions): void {
        $this->permissions = json_encode($permissions);
    }
}
