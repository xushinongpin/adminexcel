<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>layuiAdmin std - 通用后台管理模板系统（iframe标准版）</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/layui/dist/layuiadmin/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/layui/dist/layuiadmin/style/admin.css" media="all">
</head>
<body class="layui-layout-body">
@yield('content')
<script src="/layui/dist/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/layui/dist/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use('index');
</script>
</body>
</html>