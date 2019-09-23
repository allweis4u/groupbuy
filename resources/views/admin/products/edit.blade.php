<?php
//echo "<pre>"; print_r($productData->productTypes); echo "</pre>";
?>

@extends('admin.layouts.default')


@section('script')
<style>
    .container {
        text-align:center;
    }
    .typeWrapper {
        margin: 5px;
    }
    input {
        border-radius: 5px;
        border: 1px solid #CCCCCC;
    }
</style>
<script>
    var productTypes = <?php echo $productData->productTypes->toJson();?>;
    
$(function() {
    
    // 新增種類
    $('#addType').on('click', function() {
        var htmls = getProductHtmls();
        
        $('.typeGroup').append(htmls.join(''));
        
        // 刪除種類
        $('.delete').unbind().on('click', function() {
            var index = $('.delete').index(this);
            $('.typeGroup>div').eq(index + 1).remove();
        });
    });
    
    // 刪除種類
    $("#delProduct").on('click', function() {
        if (confirm('確定要刪除嗎')) {
            $('#formDel').submit();
        }
    })
    
    // 產生原有種類
    for (var i = 0; i < productTypes.length; i ++) {
        var htmls = getProductHtmls();
        
        $('.typeGroup').append(htmls.join(''));
        
        $('.typeGroup [name="product_types_id[]"]').eq(i).val(productTypes[i]["id"]);
        $('.typeGroup [name="types[]"]').eq(i).val(productTypes[i]["name"]);
        $('.typeGroup [name="prices[]"]').eq(i).val(productTypes[i]["price"]);
        
        // 刪除種類
        $('.delete').unbind().on('click', function() {
            var index = $('.delete').index(this);
            $('.typeGroup>div').eq(index + 1).remove();
        });
    }
    
    // 取得html
    function getProductHtmls() {
        var num = $('.typeGroup>div').length + 1;
        var htmls = [];
        htmls.push('<div class="typeWrapper">');
        htmls.push('<input type="hidden" name="product_types_id[]" />');
        htmls.push('種類' + num + '：<input type="text" class="" name="types[]" />&emsp;');
        htmls.push('價錢' + num + '：<input type="text" class="" name="prices[]" />');
        
        if (num > 1) {
            htmls.push('<input type="button" class="delete" value="刪除" />');
        }
        
        htmls.push('</div>');
        return htmls;
    }
})
</script>
@stop


@section('content')
<div class="container">
    <form action="{{ url('products/' . $productData->id) }}" method="POST">
        @csrf
        {{ method_field('PATCH') }}

        <input type="hidden" name="id" value="{{ $productData->id }}" />
        
        <div style="font-weight:bold;">修改商品</div>
        <div style="text-align: center;">
            品名：<input type="text" name="name" value="{{ $productData->name }}" />
        </div>
        <br />
        <div>種類名稱除了英文字母或是中文字外，請勿再多打空白或是標點符號!</div>
        <div>
            <input type="button" id="addType" value="新增種類" />
        </div>
        <div class="typeGroup "></div>
        <input type="submit" value="修改商品" />
        <input id="delProduct" type="button" value="刪除商品" />
    </form>
</div>

<form id="formDel" action="{{ route('products.destroy', $productData->id) }}" method="POST">
    @csrf
    {{ method_field('DELETE') }}
</form>
@stop
