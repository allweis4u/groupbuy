<?php
//echo "<pre>"; print_r($stats); echo "</pre>";
?>

@extends('admin.layouts.default')

@section('script')
<style>
    body {
        background-color: #EEEEEE;
    }
</style>
<script>
    var productTypes = <?php echo $productData->productTypes->toJson();?>;
    
    /**
     * 單筆領取
     * @param {type} id
     * @returns {undefined}
     */
    function doReceive(id) {
        if (confirm("確定要領取嗎?")) {
            $('#form1').attr("action", "/orders/receive/" + id)
                    .submit();
        }
    }
    
    /**
     * 修改
     * @returns {undefined}
     */
    function doUpdate(obj, id) {
        var index = $('.edit').index(obj);
        var product_types_id = $('.product_types_ids').eq(index).val();
        var quantity = $('.quantities').eq(index).val();
        var memo = $('.memos').eq(index).val();
        
        $('[name="product_types_id"]').val(product_types_id);
        $('[name="quantity"]').val(quantity);
        $('[name="memo"]').val(memo);
        $('#form1').attr("action", "/orders/" + id)
                .submit();
    }
    
    /**
     * 刪除
     * @returns {undefined}
     */
    function doDel(id) {
        if (confirm("確定要刪除嗎?")) {
            $('#form2').attr("action", "/orders/" + id)
                    .submit();
        }
    }
    
    $(function() {
        // 修改類別
        $('.product_types_ids').on('change', function() {
            console.log(1)
            var index = $('.product_types_ids').index(this);
            countPrice(index);
        });
        // 修改數量
        $('.quantities').on('change', function() {
            var index = $('.quantities').index(this);
            countPrice(index);
        });
        
        // 計算金額
        function countPrice(index) {
            var typeId = $('.product_types_ids').eq(index).val();
            var price = 0;
            var quantity = $('.quantities').eq(index).val();
            
            for (var i = 0; i < productTypes.length; i ++) {
                var id = parseInt(productTypes[i]["id"]);
                if (typeId == id) {
                    price = parseInt(productTypes[i]["price"]);
                    break;
                }
            }
            
            $('.totals').eq(index).val(price * quantity);
        }
        
        // 初始化顯示金額
        $('.quantities').trigger("change");
    })
</script>
@stop

@section('content')
<div class="container-fluid">
    <div>
        <span style="font-size: 28px; font-weight: bold;">{{ $productData->name }}-訂購清單</span>
        <!--<span style="color: red; font-size: 36px; font-weight: bold;">未到貨</span>-->
    </div>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th width="6%">序號</th>
                <th width="8%">姓名</th>
                <th width="10%">種類</th>
                <th width="12%">數量</th>
                <th width="15%">價格</th>
                <th width="15%">備註</th>
                <th>領取否</th>
                <th>領取日</th>
                <th>登記日</th>
                <th>修改</th>
                <th>刪除</th>
            </tr>
        </thead>
        <tbody>
            @php ($count = 1)
            @php ($stats = array())
            @foreach ($productData->productTypes as $productType)
                <?php
                $stats[$productType->id] = array();
                $stats[$productType->id]["name"] = $productType->name;
                $stats[$productType->id]["receive"] = 0;
                $stats[$productType->id]["order"] = 0;
                $stats[$productType->id]["stock"] = 0;
                foreach ($productType->orders as $order) {
                    if ($order->status == 0) {
                        $stats[$productType->id]["order"] += $order->quantity;
                    } else {
                        $stats[$productType->id]["receive"] += $order->quantity;
                    }
                }
                foreach($productType->stockReports as $stockReport) {
                    $stats[$productType->id]["stock"] += $stockReport->quantity;
                }
                ?>
            
                @foreach ($productType->orders as $key => $order)
                <tr>
                    <td>{{ $count }}</td>
                    <td>{{ $order->member->name }}</td>
                    <td>
                        <select class="custom-select product_types_ids" style="width: 100%;">
                            @foreach ($productData->productTypes as $p)
                            <option 
                                @if ($p->id === $order->product_types_id)
                                selected="selected"
                                @endif 
                                value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <div class="input-group">
                            <input type="number" class="form-control quantities" value="{{ $order->quantity }}" style="width: 100%;" />
                        </div>
                    </td>
                    <td>
                        <div class="input-group">
                            <input class="form-control totals" readonly="readonly" style="width: 100%;" />
                        </div>
                    </td>
                    <td>
                        <div class="input-group">
                            <input class="form-control memos" value="{{ $order->memo }}" style="width: 100%;" />
                        </div>
                    </td>
                    @if ($order->status === 0)
                        <td>
                            <span style="color: red;">未領取</span>
                        </td>
                        <td>
                            @if ($order->status == 0 && $stats[ $order->product_types_id ]["stock"] - $stats[ $order->product_types_id ]["receive"] >= $order->quantity)
                            <input type="button" value="單筆領取" onclick="doReceive({{ $order->id }})" />
                            @endif
                        </td>
                    @else
                        <td>
                            <span style="color: blue;">已領取</span>
                        </td>
                        <td>
                        @if (count($order->receiveReports) > 0 && $order->receiveReports[0]->created_at != null)
                            {{ $order->receiveReports[0]->created_at->format('Y-m-d') }}
                        @endif
                        </td>
                    @endif
                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                    <td>
                        <input type="button" value="修改" class="edit" onclick="doUpdate(this, {{ $order->id }})" />
                    </td>
                    <td><input type="button" value="刪除" class="del" onclick="doDel({{ $order->id }})" /></td>
                </tr>
                @php ($count += 1)
                @endforeach
            @endforeach
            
            @if ($count === 1)
            <tr align="center">
                <td colspan="11">沒有資料</td>
            </tr>
            @endif
        </tbody>
    </table>
    
    
    <a class="btn btn-primary col-3" href="/products">返回全部商品</a>
    <a class="btn btn-primary col-3" href="javascript: location.reload();">重新整理</a>
</div>

<form id="form1" action="" method="POST">
    @csrf
    {{method_field('PUT')}}
    <input type="hidden" name="product_types_id" />
    <input type="hidden" name="quantity" />
    <input type="hidden" name="memo" />
</form>

<form id="form2" action="" method="POST">
    @csrf
    {{method_field('DELETE')}}
</form>
@stop
