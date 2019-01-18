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
</head>
<body class="layui-layout-body">
@yield('content')
<script src="/layui/src/layuiadmin/layui/layui.js"></script>
@yield('layuijs')
</body>
</html>