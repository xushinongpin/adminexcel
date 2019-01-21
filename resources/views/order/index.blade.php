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
@section('layuijs')
    <script>
        layui.use(['form', 'layedit', 'laydate','table'], function(){
            var table = layui.table,
            form = layui.form,
            $ = layui.$,
            url = '/order',
            laydate = layui.laydate,
            product = {!! $product !!};

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
                ,height: 600
                ,even: true
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
                add: function(){
                    layer.open({
                        //layer提供了5种层类型。可传入的值有：0（信息框，默认）1（页面层）2（iframe层）3（加载层）4（tips层）
                        type: 1,
                        title: "添加客户",
                        area: ['420px', '330px'],
                        content: $("#popUpdateTest")//引用的弹出层的页面层的方式加载修改界面表单
                    });
                },
                ajax: function (oindex) {
                    console.log();
                    if(oindex.length < 3){
                        layer.msg('您没有需要修改任何东西，请选中再提交');
                        return false;
                    }
                    var data_field = {};
                    data_field.data = oindex;
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
                                console.log(data);
                                // if(data.status == 'error'){
                                //     layer.msg(data.msg,{icon: 5});//失败的表情
                                //     o.removeClass('layui-btn-disabled');
                                //     return;
                                // }else{
                                //     layer.msg(data.msg, {
                                //         icon: 6,//成功的表情
                                //         time: 1000 //1秒关闭（如果不配置，默认是3秒）
                                //     }, function(){
                                //         location.reload();
                                //     });
                                // }
                            },
                            complete: function () {
                                layer.close(this.layerIndex);
                            },
                        });
                    })
                    return false;
                },
                reload: function(){
                    console.log(1);
                    // var demoReload = $('#demoReload');
                    //
                    // //执行重载
                    // table.reload('testReload', {
                    //     page: {
                    //         curr: 1 //重新从第 1 页开始
                    //     }
                    //     ,where: {
                    //         key: {
                    //             id: demoReload.val()
                    //         }
                    //     }
                    // });
                }
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
        });
    </script>
@endsection