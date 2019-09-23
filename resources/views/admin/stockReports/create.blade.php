<?php
//echo "<pre>"; print_r($product); echo "</pre>";
?>

@extends('admin.layouts.default')

@section('script')
<style>
    .orderWrapper {
        border: 1px solid #cccccc;
        margin: 10px;
        padding: 10px;
        border-radius: 5px;
    }
    .checkWrapper {
        border: 1px solid #cccccc;
        margin: 10px;
        padding: 10px;
        border-radius: 5px;
    }
    
</style>
<script>
    var product = <?php echo $product->toJson();?>;
    $(function() {
        var $dropObj = $('select[name="product_types_id"]');
        
        // 改變下拉選單
        $dropObj.on('change', function() {
            // 顯示清單
            var orderId = parseInt( $(this).val() );
            var productTypes = product.product_types;
            for (var i = 0; i < productTypes.length; i ++) {
                var productTypeData = productTypes[i];
                
                if (productTypeData.id !== orderId) {
                    continue;
                }
                
                // 計算訂單數量
                var orders = productTypeData["orders"];
                var orderNum = 0;
                var receiveNum = 0;
                for (var j = 0; j < orders.length; j ++) {
                    if (orders[j].status == 0) {
                        orderNum += parseInt( orders[j].quantity );
                    } else {
                        receiveNum += parseInt( orders[j].quantity );
                    }
                }
                var stockReports = productTypeData["stock_reports"];
                var stockNum = 0;
                if (stockReports.length > 0) {
                    stockNum += parseInt(stockReports[0].quantity);
                }
                
                var htmls = [];
                htmls.push('<div>序號：' + productTypeData.id + '</div>');
                htmls.push('<div>名稱：' + product.name + '</div>');
                htmls.push('<div>價格：' + productTypeData.price + '</div>');
                htmls.push('<div>種類：' + productTypeData.name + '</div>');
                htmls.push('<div>商品登入日期：' + productTypeData.created_at.split(' ')[0] + '</div>');
                htmls.push('<div>訂購總數量：' + orderNum + '</div>');
                htmls.push('<div>訂購數量：' + orderNum + '</div>');
                htmls.push('<div class="arrived_quantity">到達總數量：' + (stockNum - receiveNum) + '</div>');
                htmls.push('<div class="arrived_quantity">到達數量：' + (stockNum - receiveNum) + '</div>');
                htmls.push('<div>是否送達(0:否,1:是)：</div>');
                $('.orderWrapper').html(htmls.join(''));
            }
        });
        
        // 初始化下拉選單
        $dropObj.trigger('change');
    })
</script>
@stop

@section('content')
<div class="container-fluid">
    <form action="{{ action('StockReportController@store') }}" method="POST">
        @csrf

        <div class="input-group mb-3 col-6">
            <select class="custom-select" name="product_types_id">
                @foreach ($product->productTypes as $productType)
                    <option value="{{ $productType->id }}">{{ $product->name }} / {{ $productType->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="row">
            <div class="col-5">
                <div class="orderWrapper">
                </div>
            </div>
            <div class="col-5">
                <div class="checkWrapper">
                    <div>
                        請輸入總類到達數量
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">總數量：</span>
                        <input type="number" class="form-control" name="total" />
                    </div>
                    <br />
                    <div class="row">
                        <div class="btn-group col-12">
                            <input type="submit" class="btn btn-primary" value="提交" />
                        </div>
                        <div class="btn-group col-12" style="margin-top: 10px;">
                            <a id='back' class="btn btn-secondary" href="/products">返回</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@stop
