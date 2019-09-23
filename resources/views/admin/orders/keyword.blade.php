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
    $(function() {
        $('.multiReceive').on('click', function() {
            if (!confirm('確定要多筆領取嗎?')) {
                return;
            }
            var htmls = [];
            $('.clkSelect').each(function() {
                if ( $(this).prop('checked') ) {
                    htmls.push('<input type="hidden" name="orders_ids[]" value="' + $(this).val() + '" />');
                }
            })
            $('#multipleForm').append( htmls.join('') ).submit();
        });
        
        $('.orderDel').on('click', function() {
            var index = $('.orderDel').index(this);
            
            if (confirm('確定要刪除嗎?')) {
                $('.formDelete').eq(index).submit();
            }
        });
    })
</script>
@stop

@section('content')
    @php ($amount = 0)
    @foreach ($orderDatas as $orderData)
        @php ($amount += $orderData->price)
    @endforeach

<div class="container-fluid">
    <div>
        <font style="font-size: 28px; font-weight: bold;">
            @if ($action == 'exactSearch')
            精準搜尋成員：{{ $keyName }}
            @else
            關鍵字搜尋成員：{{ $keyName }}
            @endif
        </font>
    </div>
    <div class="">
        <form action="{{ action('OrderController@exactSearch') }}" method="POST">
            @csrf
            <input type="hidden" name="key_name" value="{{ $keyName }}" />
            <input type="submit" class="btn btn-primary" value="精準搜尋成員" />
            總金額：{{ $amount }}&emsp;
            <a class="btn btn-primary multiReceive" href="#">多筆領取</a>
        </form>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>NO</th>
                <th>姓名</th>
                <th>商品</th>
                <th>種類</th>
                <th>數量</th>
                <th>價格</th>
                <th>備註</th>
                <th>到貨否</th>
                <th>領取日</th>
                <th>領取選取</th>
                <th>訂購日</th>
                <th>刪除</th>
            </tr>
        </thead>
        <tbody>
                
            @php ($count = 1)
            @foreach ($orderDatas as $orderData)
                <?php 
                $stockNum = 0;
                $orderNum = 0;
                $receiveNum = 0;
                if (isset($stats[ $orderData->product_types_id ]["stock"])) {
                    $stockNum = $stats[ $orderData->product_types_id ]["stock"];
                }
                if (isset($stats[ $orderData->product_types_id ]["order"])) {
                    $orderNum = $stats[ $orderData->product_types_id ]["order"];
                }
                if (isset($stats[ $orderData->product_types_id ]["receive"])) {
                    $receiveNum = $stats[ $orderData->product_types_id ]["receive"];
                }
                ?>
            <tr>
                <td>{{ $count }}</td>
                <td>{{ $orderData->member->name }}</td>
                <td>{{ $orderData->productType->product->name }}</td>
                <td>{{ $orderData->productType->name }}</td>
                <td>{{ $orderData->quantity }}</td>
                <td>{{ $orderData->price }}</td>
                <td>{{ $orderData->memo }}</td>
                <td>
                    @if ($orderData->status === 1)
                        <span style="color: blue;">已到貨</span>
                    @else
                        @if ($stockNum >= $receiveNum + $orderNum)
                        <span style="color: blue;">已到貨</span>
                        @else
                        <span style="color: red;">未到貨</span>
                        @endif
                    @endif
                </td>
                @if ($orderData->status === 0)
                <td>
                    <span style="color: red;">未領取</span>
                </td>
                <td align="center">
                    @if ($stockNum >= $receiveNum + $orderNum)
                    <input type="checkbox" class="clkSelect" value="{{ $orderData->id }}" />
                    @endif
                </td>
                @else
                <td>
                    <span style="color: blue;">已領取</span>
                </td>
                <td></td>
                @endif
                <td>
                    {{ $orderData->created_at->format('Y-m-d') }}
                </td>
                <td>
                    @if ($orderData->status === 0)
                    <form class="formDelete" action="{{ action('OrderController@deleteSearch') }}" method="POST">
                        @csrf
                        {{method_field('DELETE')}}
                        <input type="hidden" name="orders_id" value="{{ $orderData->id }}" />
                        <input type="hidden" name="action" value="{{ $action }}" />
                        <input type="hidden" name="key_name" value="{{ $keyName }}" />
                        <input type="button" class="orderDel" value="刪除" />
                    </form>
                    @endif
                </td>
            </tr>
                @php ($count += 1)
            @endforeach
        </tbody>
    </table>
</div>

<form id="multipleForm" action="{{ action('OrderController@multipleReceive') }}" method="POST">
    @csrf
    <input type="hidden" name="action" value="{{ $action }}" />
    <input type="hidden" name="key_name" value="{{ $keyName }}" />
</form>
@stop

