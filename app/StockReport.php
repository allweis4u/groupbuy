<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockReport extends Model
{
    protected $table = 'stock_reports';
    
    public function receiveReports() {
        return $this->hasMany('App\ReceiveReport', 'stock_reports_id');
    }
}
