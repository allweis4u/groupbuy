<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductType extends Model
{
    protected $table = 'product_types';
    
    use SoftDeletes; // 使用軟刪除
    
    public function orders() {
        return $this->hasMany('App\Order', 'product_types_id');
    }
    
    public function stockReports() {
        return $this->hasMany('App\StockReport', 'product_types_id');
    }
    
    /**
     * 取得product資料
     * @return type
     */
    public function product()
    {
        return $this->belongsTo('App\Product', "products_id");
    }
}
