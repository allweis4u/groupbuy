<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>

@extends('admin.layouts.default')

@section('script')
<style>
    body {
        background-color: #EEEEEE;
    }
    .titleWrapper {
        font-size: 28px;
        font-weight: bold;
    }
</style>
<script>
var productTypes = <?php echo $productData->productTypes->toJson();?>;
var members = <?php echo $memberDatas->toJson();?>;
    
$(function() {
    // 按下新增一列
    $('#addRow').on('click', function() {
        var num = $('.contentWrapper>tr').length + 1;
        var htmls = [];
        htmls.push('<tr>');
        htmls.push('<td>' + num + '</td>');
        if (num > 1) {
            htmls.push('<td><input type="checkbox" class="copy" /></td>');
        } else {
            htmls.push('<td></td>');
        }
        
        // 成員姓名
        htmls.push('<td>');
        htmls.push('<div class="input-group">');
        htmls.push('<input type="text" class="form-control member_names" name="member_names[]" style="width: 100%;" />');
        htmls.push('</div>');
        htmls.push('</td>');
        
        // 顯示成員
        htmls.push('<td><select class="custom-select members_select" style="width: 100%;">');
        htmls.push('<option value="">選擇成員</option>');
        for (var i=0; i < members.length; i ++) {
            htmls.push('<option value="' + members[i]["id"] + '">' + members[i]["name"] + '</option>');
        }
        htmls.push('</select></td>');
        
        // 顯示類別
        htmls.push('<td><select class=" custom-select product_types_ids" name="product_types_ids[]" style="width: 100%;">');
        for (var i=0; i < productTypes.length; i ++) {
            htmls.push('<option value="' + productTypes[i]["id"] + '">' + productTypes[i]["name"] + '</option>');
        }
        htmls.push('</select></td>');
        
        // 數量
        htmls.push('<td>');
        htmls.push('<div class="input-group">');
        htmls.push('<input type="number" class="form-control quantities" name="quantities[]" style="width: 100%;" />');
        htmls.push('</div>');
        htmls.push('</td>');
        
        // 金額
        htmls.push('<td>');
        htmls.push('<div class="input-group">');
        htmls.push('<input type="number" class="form-control totals" readonly="readonly" placeholder="會自動計算。" style="width: 100%;" />');
        htmls.push('</div>');
        htmls.push('</td>');
        
        // 備註
        htmls.push('<td>');
        htmls.push('<div class="input-group">');
        htmls.push('<input type="text" class="form-control memos" name="memos[]" style="width: 100%;" />');
        htmls.push('</div>');
        htmls.push('</td>');
        
        htmls.push('</tr>');
        $('.contentWrapper').append(htmls.join(''));
        
        // 與上方姓名相同
        $('.copy').unbind().on('click', function () {
            if ($(this).prop("checked")) {
                var index = $('.copy').index(this);
                var name = $('.member_names').eq(index).val();
                $('.member_names').eq(index + 1).val(name);
            }
        })
        
        // 選擇成員
        $('.members_select').on('change', function() {
            var index = $('.members_select').index(this);
            if ($(this)[0].selectedIndex > 0) {
                var name = $(this).find("option:selected").text();
                $('.member_names').eq(index).val(name);
            }
        })
        
        // 修改類別
        $('.product_types_ids').unbind().on('change', function() {
            var index = $('.product_types_ids').index(this);
            countPrice(index);
        });
        // 修改數量
        $('.quantities').unbind().on('change', function() {
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
    });
    
    // 按下刪除一列
    $('#delRow').on('click', function() {
        var $trObj = $('.contentWrapper>tr');
        // 避免刪除預設種類
        if ($trObj.length > 1) {
            $trObj.last().remove();
        }
    });
    
    // 初始化產生選項
    $('#addRow').trigger('click');
})
</script>
@stop

@section('content')
<div class="container-fluid">
    <form action="{{ action('OrderController@store') }}" method="POST">
        @csrf
        
        <input type="hidden" name="products_id" value="{{ $productData->id }}" />
        <div class="titleWrapper">{{ $productData->name }}-訂購表單</div>
                <a id='addRow' class="btn btn-primary" href="#">新增一列</a>
                <a id='delRow' class="btn btn-primary" href="#">刪除一列</a>

        <div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th width="10%">與上方相同姓名</th>
                        <th width="15%">姓名</th>
                        <th width="10%"></th>
                        <th>類別</th>
                        <th width="15%">數量</th>
                        <th width="15%">總金額</th>
                        <th width="15%">備註</th>
                    </tr>
                </thead>
                <tbody class='contentWrapper'>
                </tbody>
            </table>
        </div>
        <input type='submit' class="btn btn-primary col-12" value='新增一列' />
        <div style='margin-top: 10px;'>
            <a class="btn btn-secondary col-12" href="/products">返回全部商品</a>
        </div>
    </form>
</div>
@stop
