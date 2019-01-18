@extends('layui.default')
@section('content')

    <div class="layui-fluid demoTable">
        <div class="layui-card">
            <div class="layui-card-body">
                <div style="padding-bottom: 10px;">
                    <button class="layui-btn layuiadmin-btn-useradmin" data-type="add">添加</button>
                </div>
            </div>
        </div>
    </div>

    <table class="layui-hide" id="test" lay-filter="test"></table>
    {{--<script type="text/html" id="toolbarDemo">--}}
        {{--<div class="layui-btn-container">--}}
            {{--<button class="layui-btn layui-btn-sm" lay-event="getCheckData">获取选中行数据</button>--}}
            {{--<button class="layui-btn layui-btn-sm" lay-event="getCheckLength">获取选中数目</button>--}}
            {{--<button class="layui-btn layui-btn-sm" lay-event="isAll">验证是否全选</button>--}}
        {{--</div>--}}
    {{--</script>--}}

    <script type="text/html" id="barDemo">
        <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
    </script>

    //表单的id用于表单的选择，style是在本页隐藏，只有点击编辑才会弹出
    <div class="layui-row" id="popUpdateTest" style="display:none;">
        <div class="layui-col-md10">
            <form class="layui-form layui-from-pane" action="" style="margin-top:20px" >
                <div class="layui-form-item">
                    <label class="layui-form-label">客户状态</label>
                    <div class="layui-input-block">
                        <select name="status" lay-filter="eqptType">
                            <option value="1" selected="">启用</option>
                            <option value="2">停用</option>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">客户名称</label>
                    <div class="layui-input-block">
                        <input type="text" name="name" required  lay-verify="required" autocomplete="off" placeholder="请输入客户名称" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item" style="margin-top:40px">
                    <div class="layui-input-block">
                        <button class="layui-btn  layui-btn-submit " lay-submit="" lay-filter="addcustomer">确认添加</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@section('layuijs')
    <script>
        layui.use('table', function(){
            var table = layui.table,
            form = layui.form,
            $ = layui.$,
            url = '/customer';
            table.render({
                elem: '#test'
                ,url:url
                ,toolbar: '#toolbarDemo'
                ,title: '用户数据表'
                ,cols: [[
                    {type: 'checkbox', fixed: 'left'}
                    ,{field:'id', title:'ID', width:80, fixed: 'left', unresize: true, sort: true}
                    ,{field:'name', title:'用户名', width:120, edit: 'text'}
                    ,{field:'status', title:'状态【1使用 2停用】', width:200, edit: 'text', templet: function(res){
                        var statusObj = {'1':'启用','2':'停用'};
                            return '<em>'+ statusObj[res.status] +'</em>'
                        }}
                    ,{field:'created_at', title:'添加时间', width:120}
                    ,{field:'updated_at', title:'更改时间', width:120}
                    ,{fixed: 'right', title:'操作', toolbar: '#barDemo', width:150}
                ]]
                ,page: true
                ,limits: [10]
                ,even: true
            });

            //头工具栏事件
            table.on('toolbar(test)', function(obj){
                var checkStatus = table.checkStatus(obj.config.id);
                switch(obj.event){
                    case 'getCheckData':
                        var data = checkStatus.data;
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
                    layer.confirm('真的删除行么', function(index){
                        obj.del();
                        layer.close(index);
                    });
                } else if(obj.event === 'edit'){
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
                    oindex._token = $('meta[name="csrf-token"]').attr('content');
                    layer.confirm(JSON.stringify(oindex),function (index) {
                        $.ajax({
                            url:url,
                            type:'post',
                            data:oindex,
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
                                        time: 1000 //1秒关闭（如果不配置，默认是3秒）
                                    }, function(){
                                        location.reload();
                                    });
                                }
                            },
                            complete: function () {
                                layer.close(this.layerIndex);
                            },
                        });
                    })
                    return false;
                }
            };
            $('.demoTable .layui-btn').on('click', function(){
                var type = $(this).data('type');
                active[type] ? active[type].call(this) : '';
            });

            //监听提交添加操作
            form.on('submit(addcustomer)', function(data){
                active['ajax'] ? active['ajax'].call(this,data.field) : '';
                return false;
            });
        });
    </script>
@endsection