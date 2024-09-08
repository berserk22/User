<?php

/**
 * @author Sergey Tevs
 * @email sergey@tevs.org
 */

namespace Modules\User\Db\Models;

use Illuminate\Database\Eloquent\Collection;
use Modules\Database\Model;

class Role extends Model {

    protected $table = 'role';

    private string $rolePermission = "Modules\User\Db\Models\RolePermission";


    /**
     * @return Collection
     */
    public function getGroupPermission(): Collection {
        return $this->hasMany($this->rolePermission)
            ->select('permission_group_id')->groupBy('permission_group_id')->get();
    }

    /**
     * @return object|null
     */
    public function getPermissions(): object|null {
        return $this->hasOne($this->rolePermission)->first();
    }

    public function getPermissionsArray(): array {
        $permissions = $this->hasOne($this->rolePermission)->first();
        return !is_null($permissions)?json_decode($permissions->permissions, true):[];
    }

}
