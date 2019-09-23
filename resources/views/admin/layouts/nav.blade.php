
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
        <a class="navbar-brand" href="{{ action('ProductController@index') }}">團購系統</a>
        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
            <li class="nav-item active">
                <a class="nav-link" href="{{ action('ProductController@index') }}">全部商品 <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ action('ProductController@create') }}">登入商品</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ action('ReportController@index') }}">報表</a>
            </li>
            <li class="nav-item">
                <form id="search_form" class="form-inline my-2 my-lg-0" action="{{ action('OrderController@keyword') }}" method="POST">
                    @csrf
                    <input class="form-control mr-sm-2 search_input" name="key_name" type="search" placeholder="輸入姓名以搜尋訂單" aria-label="輸入姓名以搜尋訂單" />
                    <button type="button" class="btn btn-outline-success my-2 my-sm-0">查詢</button>
                </form>
            </li>
        </ul>
    </div>
</nav>