<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Order extends Model
{
    protected $table = 'orders';
    
    use SoftDeletes; // 使用軟刪除
    
    /**
     * Get the receive_reports.
     */
    public function receiveReports() {
        return $this->hasMany('App\ReceiveReport', 'orders_id');
    }
    
    public function shipReports() {
        return $this->hasMany('App\ShipReport', 'orders_id');
    }
    
    /**
     * 取得productTypes資料
     * @return type
     */
    public function productType()
    {
        return $this->belongsTo('App\ProductType', "product_types_id", "id");
    }
    
    public function member()
    {
        return $this->belongsTo('App\Member', "members_id", "id");
    }
}

