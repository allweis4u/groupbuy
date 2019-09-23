<?php
//echo "<pre>"; print_r($orderDatas); echo "</pre>";
?>

@extends('admin.layouts.default')

@section('script')
<style>
    body {
        background-color: #EEEEEE;
    }
    input {
        border-radius: 5px;
        border: 1px solid #CCCCCC;
    }
    .searchWrapper {
        margin: 5px;
    }
</style>

<script>
    $(function() {
        $('[name="start_date"], [name="end_date"]').datepicker({ 
            dateFormat: 'yy-mm-dd'
        });
        
        $('.search').on("click", function() {
            $('#form1').submit();
        })
    })
</script>
@stop

@section('content')
<div class="container-fluid">
    <form id="form1" action="{{ action('ReportController@search') }}" method="POST">
        @csrf
        
        <div class="searchWrapper">
            <input type="text" name="start_date" value="{{ $startDate }}" style="width: 20%;" />
            &nbsp;-&nbsp;
            <input type="text" name="end_date" value="{{ $endDate }}" style="width: 20%;" />
            <button class="btn btn-primary search" type="button">查詢</button>
        </div>
    </form>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>NO</th>
                <th>姓名</th>
                <th>商品</th>
                <th>種類</th>
                <th>金額</th>
                <th>領取日期</th>
            </tr>
        </thead>
        <tbody>
            @if (count($orderDatas) === 0)
            <tr align="center">
                <td colspan="6">沒有資料</td>
            </tr>
            @endif
            
            @foreach ($orderDatas as $key => $orderData)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $orderData->member->name }}</td>
                <td>{{ $orderData->productType->product->name }}</td>
                <td>{{ $orderData->productType->name }}</td>
                <td>{{ $orderData->price * $orderData->quantity }}</td>
                <td>
                    @if (count($orderData->receiveReports) > 0)
                    {{ $orderData->receiveReports[0]->created_at->format('Y-m-d') }}
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@stop
