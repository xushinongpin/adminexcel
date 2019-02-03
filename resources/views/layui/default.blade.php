<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>万能统计后台</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/layui/src/layuiadmin/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/layui/src/layuiadmin/style/admin.css" media="all">
    <style>
        .layui-layout-body{width: 100%;height: 100%;overflow: scroll;}
    </style>
</head>
<body class="layui-layout-body">
@yield('content')
<script src="/layui/src/layuiadmin/layui/layui.js"></script>
@yield('layuijs')
</body>
<script>
    //用于判断对象是否为空
    function isEmpty(obj) {
        for(var prop in obj) {
            if(obj.hasOwnProperty(prop))
                return false;
        }

        return true;
    }
</script>
</html>