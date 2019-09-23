<!doctype html>
<html >
<head>
    <meta charset="UTF-8">
    <title>團購系統</title>
</head>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="{{ URL::asset('css/bootstrap/bootstrap.min.css') }}" />
<link rel="stylesheet" href="{{ URL::asset('css/common.css') }}" />
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script type="text/javascript" src="{{ URL::asset('js/bootstrap/bootstrap.min.js') }}"></script>
<script>
$(function() {
    $('#search_form button').on('click', function(){
        var val = $('.search_input').val().trim();
        if ( val == "" ) {
            return;
        }
        $('#search_form').submit();
    })
})
</script>
@yield('script')
<body>
    @include('admin.layouts.nav')
    
    @yield('content')

    @include('admin.layouts.footer')
</body>
</html>