

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
$(function() {
    
    // 新增種類
    $('#addType').on('click', function() {
        var num = $('.typeGroup>div').length + 1;
        var htmls = [];
        htmls.push('<div class="typeWrapper">');
        htmls.push('種類' + num + '：<input type="text" class="" name="types[]" />&emsp;');
        htmls.push('價錢' + num + '：<input type="text" class="" name="prices[]" />');
        
        if (num > 1) {
            htmls.push('<input type="button" class="delete" value="刪除" />');
        }
        htmls.push('</div>')
        
        $('.typeGroup').append(htmls.join(''));
        
        // 刪除種類
        $('.delete').unbind().on('click', function() {
            var index = $('.delete').index(this);
            $('.typeGroup>div').eq(index + 1).remove();
        });
    });
    
    // 初始化產生種類
    $('#addType').trigger('click');
})
</script>
@stop


@section('content')
<div class="container">
    <form action="{{ action('ProductController@store') }}" method="POST">
        @csrf

        <div style="font-weight:bold;">登入商品</div>
        <div style="text-align: center;">
            <span class="input-group-addon">品名：</span>
            <input type="text" class="" name="name" />
        </div>
        <br />
        <div>種類名稱除了英文字母或是中文字外，請勿再多打空白或是標點符號!</div>
        <div>
            <input type="button" id="addType" value="新增種類" />
        </div>
        <div class="typeGroup "></div>
        <input type="submit" value="登入商品" />
    </form>
</div>
@stop
