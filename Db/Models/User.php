<?php

/**
 * @author Sergey Tevs
 * @email sergey@tevs.org
 */

namespace Modules\User\Db\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Database\Model;

class User extends Model {

    protected $table = "user";

    /**
     * @return \Illuminate\Database\Eloquent\Model|HasOne
     */
    public function getShippingAddress(): \Illuminate\Database\Eloquent\Model|HasOne {
        return $this->hasOne('Modules\User\Db\Models\Address')->where([
            ['address_type', '=', 'shipping'],
            ['active', '=', 1]
        ])->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|HasOne
     */
    public function getBillingAddress(): \Illuminate\Database\Eloquent\Model|HasOne {
        return $this->hasOne('Modules\User\Db\Models\Address')->where([
            ['address_type', '=', 'billing'],
            ['active', '=', 1]
        ])->first();
    }

    public function getRole(): BelongsTo|\Illuminate\Database\Eloquent\Model|null {
        return $this->belongsTo('Modules\User\Db\Models\Role')->first();
    }

    /**
     * @return HasMany
     */
    public function getUserPermission(): HasMany {
        return $this->hasMany('Modules\User\Db\Models\UserPermission');
    }

}
