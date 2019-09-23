<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = 'members';
    
    public function orders() {
        return $this->hasOne('App\Order', 'orders_id');
    }
}
