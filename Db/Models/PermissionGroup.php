<?php

/**
 * @author Sergey Tevs
 * @email sergey@tevs.org
 */

namespace Modules\User\Db\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Database\Model;

class PermissionGroup extends Model {

    protected $table = 'permission_group';

    /**
     * @return Collection
     */
    public function getPermission(): Collection {
        return $this->hasMany('Modules\User\Db\Models\Permission')->where("active", "=", 1)->get();
    }
}
