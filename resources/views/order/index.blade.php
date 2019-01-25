@extends('layui.default')
@section('content')
    <style>
        .demoTable{margin:20px 0 0 20px}
    </style>
    <div class="demoTable">
        <div class="layui-inline">
            <label class="layui-form-label">哪天数据：</label>
            <div class="layui-input-inline">
                <input type="text" name="date" id="date" lay-verify="date" placeholder="yyyy-MM-dd" autocomplete="off" class="layui-input">
            </div>
        </div>
        <button class="layui-btn" data-type="reload">搜索</button>
    </div>
    <table class="layui-hide" id="test" lay-filter="test"></table>
    <script type="text/html" id="toolbarDemo">
        <button class="layui-btn layui-btn-sm" lay-event="getCheckData">提交修改</button>【选中需要提交的】
    </script>
    <script type="text/html" id="barDemo">
        <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
    </script>
    <blockquote class="layui-elem-quote layui-text">
        点击用户名称即可查看该用户购买情况
    </blockquote>
    <table lay-filter="parse-table-demo" class="parse-table-demo">
    </table>
@section('layuijs')
    <script>
        layui.use(['form', 'layedit', 'laydate','table'], function(){
            var table = layui.table,
            form = layui.form,
            $ = layui.$,
            url = '/order',
            laydate = layui.laydate,
            strseparator = '{{$strseparator}}',
            product = {!! $product !!},
            titledata = {!! $titledata !!};

            //日期
            laydate.render({
                elem: '#date'
            });

            table.render({
                elem: '#test'
                ,url:url
                ,toolbar: '#toolbarDemo'
                ,title: '客户数据表'
                ,cols: product
                ,id: 'testReload'
                ,height: 600
                ,even: true
                ,skin:'row'
                ,totalRow: true
            });

            //监听行单击事件（单击事件为：rowDouble）
            table.on('row(test)', function(obj){
                var data = obj.data,thdata = '<table lay-filter="parse-table-demo"><thead><tr><th lay-data="{field:\'username\', width:200}">用户</th>',tddata = '<tbody><tr><td>'+data['username']+'</td>';
                layui.each(data,function (index,item) {
                    if(index.indexOf('requirement') > -1 && item > 0){
                        thdata += '<th lay-data="{field:\''+index+'\', width:100}">'+titledata[index]+'</th><th lay-data="{field:\'price\', width:100}">价格</th>';
                        tddata += '<td>'+item+'</td><td>'+data['price'+strseparator+index.split(strseparator)[1]]+'</td>';
                        //item * data['price'+strseparator+index.split(strseparator)[1]];
                    }
                });
                thdata += '<th lay-data="{field:\'totalmoney\', width:100}">总价</th></tr></thead> ';
                tddata += '<td>'+data['totalmoney']+'</td></tr></tbody>';
                $('.parse-table-demo').html(thdata+tddata);
                active['parseTable'] ? active['parseTable'].call(this) : '';
                console.log(data);
                // //标注选中样式
                obj.tr.addClass('layui-table-click').siblings().removeClass('layui-table-click');

            });

            //头工具栏事件
            table.on('toolbar(test)', function(obj){
                var checkStatus = table.checkStatus(obj.config.id);
                switch(obj.event){
                    case 'getCheckData':
                        var data = checkStatus.data;
                        active['ajax'] ? active['ajax'].call(this,JSON.stringify(data)) : '';
                        return false;
                        layer.alert(JSON.stringify(data));
                        break;
                    case 'getCheckLength':
                        var data = checkStatus.data;
                        layer.msg('选中了：'+ data.length + ' 个');
                        break;
                    case 'isAll':
                        layer.msg(checkStatus.isAll ? '全选': '未全选');
                        break;
                };
            });

            //监听行工具事件
            table.on('tool(test)', function(obj){
                var data = obj.data;
                if(obj.event === 'del'){
                    data.del = 1;
                    data.msg = '删除数据： ';
                    active['ajax'] ? active['ajax'].call(this,data) : '';
                } else if(obj.event === 'edit'){
                    data.msg = '修改数据： ';
                    active['ajax'] ? active['ajax'].call(this,data) : '';
                }
                return false;
            });

            //监听添加
            var active = {
                ajax: function (oindex) {
                    if(oindex.length < 3){
                        layer.msg('您没有需要修改任何东西，请选中再提交');
                        return false;
                    }
                    var data_field = {};
                    data_field.data = oindex;
                    data_field.time = $('#date').val();
                    data_field._token = $('meta[name="csrf-token"]').attr('content');
                    layer.confirm('添加修改内容： '+JSON.stringify(data_field),function (index) {
                        $.ajax({
                            url:url,
                            type:'post',
                            data:data_field,
                            beforeSend:function () {
                                this.layerIndex = layer.load(0, { shade: [0.5, '#393D49'] });
                            },
                            success:function(data){
                                if(data.status == 'error'){
                                    layer.msg(data.msg,{icon: 5});//失败的表情
                                    o.removeClass('layui-btn-disabled');
                                    return;
                                }else{
                                    layer.msg(data.msg, {
                                        icon: 6,//成功的表情
                                        time: 2000 //1秒关闭（如果不配置，默认是3秒）
                                    }, function(){
                                        table.reload('testReload', {
                                            page: {
                                                curr: 1 //重新从第 1 页开始
                                            }
                                        });
                                    });
                                }
                            },
                            complete: function () {
                                layer.close(this.layerIndex);
                            },
                        });
                    });
                    return false;
                },
                reload: function(){
                    var time = $('#date').val();
                    //执行重载
                    table.reload('testReload', {
                        page: {
                            curr: 1 //重新从第 1 页开始
                        }
                        ,where: {
                            time: time
                        }
                    });
                },
                parseTable: function(){
                    table.init('parse-table-demo', { //转化静态表格
                        //height: 'full-500'
                    });
                },
            };
            $('.demoTable .layui-btn').on('click', function(){
                var type = $(this).data('type');
                active[type] ? active[type].call(this) : '';
            });

            //监听提交添加操作
            form.on('submit(addcustomer)', function(data){
                data.field.msg = '添加数据： ';
                active['ajax'] ? active['ajax'].call(this,data.field) : '';
                return false;
            });

            function totalmoney(data) {
                var num = 0;
                layui.each(data,function (index,item) {
                    if(index.indexOf('price') > -1){
                        num += (item * data['requirement'+strseparator+index.split(strseparator)[1]]);
                    }
                });
                return num;
            }
        });
    </script>
@endsection