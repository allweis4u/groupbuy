<?php
//echo "<pre>"; print_r($stats); echo "</pre>";

?>

@extends('admin.layouts.default')


@section('script')
<style>
    .funcWrapper {
        margin-bottom: 5px;
        height: 100%;
    }
    .funcInnerWrapper {
        border: 1px solid #DDDDDD;
        border-radius: 5px;
        padding: 5px;
    }
    .itemName {
        color: #0066FF;
        font-size: 22px;
        font-weight: bold;
    }
    .item0 {
        color: #FF0000;
        font-size: 22px;
        font-weight: bold;
    }
    .item1 {
        color: blue;
        font-size: 22px;
        font-weight: bold;
    }
</style>
<script>
$(function() {
    
    
})
</script>
@stop

@section('content')
<div class="container-fluid">
    @php ($count = 0)
    @foreach ($productDatas as $product)
    @if ($count % 3 == 0)
    <div class="row">
    @endif
        <?php
        $orderNum = 0;
        $stockFlag = 0;
        foreach($stats[ $product->id ] as $datas) {
            $orderNum += $datas["order"];
            if ($datas["stock"] >= $datas["order"] && $datas["stock"] > 0) {
                $stockFlag = true;
            }
        }
        ?>

        <div class="col-sm-4 col-md-4">

            <div class="funcWrapper">
                <div class="funcInnerWrapper">
                    <div>
                        <span class="itemName">{{ $product->name }}</span>
                        @if ($stockFlag)
                        <span class="item1">已到貨</span>
                        @else
                        <span class="item0">未到貨</span>
                        @endif
                    </div>
                    <div>
                        登入日期：{{ $product->created_at->format('Y-m-d') }}<br/>
                        訂購數量：{{ $orderNum }}
                    </div>

                    @foreach ($product->productTypes as $key => $productType)
                    <div>
                        @if ($stats[ $product->id ][ $productType->id ]["order"] > 0)
                        {{ $stats[ $product->id ][ $productType->id ]["name"] }}：{{ $stats[ $product->id ][ $productType->id ]["order"] }}
                        @endif
                    </div>
                    @endforeach

                    <div>
                        <a class="btn btn-primary btn-sm" href="{{ route('orders.create') }}/{{ $product->id }}">登入訂購</a>
                        <a class="btn btn-info btn-sm" href="{{ route('orders.index') }}/{{ $product->id }}">訂購清單</a>
                        <a class="btn btn-danger btn-sm" href="{{ route('stockReports.create') }}/{{ $product->id }}">確認到貨</a>
                        <a class="btn btn-secondary btn-sm" href="{{ route('products.edit', $product->id) }}">設定</a>
                    </div>
                </div>
            </div>
        </div>
    @if ($count % 3 == 2)
    </div>
    @endif
    @php ($count += 1)
    @endforeach
    
    @if ($count % 3 != 0)
    </div>
    @endif
    
</div>
@stop
