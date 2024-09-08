<?php

/**
 * @author Sergey Tevs
 * @email sergey@tevs.org
 */

namespace Modules\User\Db\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Database\Model;

class UserRole extends Model {

    protected $table = 'user_role';

    /**
     * @return \Illuminate\Database\Eloquent\Model|BelongsTo
     */
    public function getUser(): \Illuminate\Database\Eloquent\Model|BelongsTo {
        return $this->belongsTo('Modules\User\Db\Models\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|BelongsTo
     */
    public function getRole(): \Illuminate\Database\Eloquent\Model|BelongsTo {
        return $this->belongsTo('Modules\User\Db\Models\Role');
    }

}
