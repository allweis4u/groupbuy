<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    protected $table = 'products';
    
    use SoftDeletes; // 使用軟刪除
    
    /**
     * Get the product typs.
     */
    public function productTypes() {
        return $this->hasMany('App\ProductType', 'products_id');
    }
}
