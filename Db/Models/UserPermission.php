<?php

/**
 * @author Sergey Tevs
 * @email sergey@tevs.org
 */

namespace Modules\User\Db\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Database\Model;

class UserPermission extends Model {

    protected $table = 'user_permission';

    /**
     * @return \Illuminate\Database\Eloquent\Model|BelongsTo
     */
    public function getUser(): \Illuminate\Database\Eloquent\Model|BelongsTo {
        return $this->belongsTo('Modules\User\Db\Models\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|BelongsTo
     */
    public function getPermission(): \Illuminate\Database\Eloquent\Model|BelongsTo {
        return $this->belongsTo('Modules\User\Db\Models\Permission');
    }

}
