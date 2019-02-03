@extends('layui.default')
@section('content')
    <style>
        .demoTable{padding: 30px 0 0 30px}
    </style>
    <div class="demoTable layui-form">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">用户订单</label>
                <div class="layui-input-inline">
                    <select name="uid" lay-verify="required" lay-search="" id="uid">
                        <option value="">直接选择或搜索选择</option>
                        @foreach($customerdatas as $customerdata)
                            <option value="{{ $customerdata['id'] }}">{{ $customerdata['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">下单日期</label>
                <div class="layui-input-inline">
                    <input type="text" name="date" id="date" lay-verify="date" placeholder="yyyy-MM-dd" autocomplete="off" class="layui-input">
                </div>
            </div>
            <button class="layui-btn" id="search" data-type="reload">搜索</button>
        </div>
    </div>
    <table class="layui-hide" id="test" lay-filter="test"></table>

    <script type="text/html" id="toolbarDemo">
        <div class="layui-btn-container">
            <button class="layui-btn layui-btn-sm" lay-event="getCheckData">选中删除</button>
        </div>
    </script>
@section('layuijs')
    <script>
        layui.use(['form', 'layedit', 'laydate','table'], function(){
            var form = layui.form
            ,layer = layui.layer
            ,layedit = layui.layedit
            ,laydate = layui.laydate
            ,table = layui.table;

            //日期
            laydate.render({
                elem: '#date'
            });

            //搜索功能
            var $ = layui.$, active = {
                reload: function(){
                    var uid = $("#uid").val(),time = $("#date").val();
                    if(!uid){
                        layer.msg('请选择用户');
                        return false;
                    }
                    table.render({
                        elem: '#test'
                        ,url:'?cid='+uid+'&time='+time
                        ,toolbar: '#toolbarDemo'
                        ,title: '订单删除表'
                        ,cols: [[
                            {type: 'checkbox', fixed: 'left'}
                            ,{field:'pname', title:'产品名称', width:120, edit: 'text'}
                        ]]
                        ,page: true
                        ,height: 315
                    });
                    return false;
                }
            };
            $('.demoTable .layui-btn').on('click', function(){
                var type = $(this).data('type');
                active[type] ? active[type].call(this) : '';
            });

            //头工具栏事件
            table.on('toolbar(test)', function(obj){
                var checkStatus = table.checkStatus(obj.config.id),data_field = {},oidArr = [],oidStr = '',titleArr = [];
                switch(obj.event){
                    case 'getCheckData':
                        var data = checkStatus.data;
                        layui.each(data,function (index,item) {
                            oidArr[index] = item.id;
                            titleArr[index] = item.pname;
                        });
                       oidStr = oidArr.toString('');
                        if(!oidStr){
                            layer.msg('请选择要删除的内容');
                            return false;
                        }
                        data_field.cid = $("#uid").val();
                        data_field.time = $("#date").val();
                        data_field.oid = oidStr;
                        data_field._token = $('meta[name="csrf-token"]').attr('content');
                        layer.confirm('确认删除的订单： '+titleArr.toString(''),function (index) {
                            $.ajax({
                                url:'',
                                type:'post',
                                data:data_field,
                                beforeSend:function () {
                                    this.layerIndex = layer.load(0, { shade: [0.5, '#393D49'] });
                                },
                                success:function(data){
                                    if(data === 0){
                                        layer.msg('操作失败',{icon: 5});//失败的表情
                                        o.removeClass('layui-btn-disabled');
                                        return;
                                    }else{
                                        layer.msg('操作成功', {
                                            icon: 6,//成功的表情
                                            time: 2000 //1秒关闭（如果不配置，默认是3秒）
                                        }, function(){
                                            $('#search').click();
                                        });
                                    }
                                },
                                complete: function () {
                                    layer.close(this.layerIndex);
                                },
                            });
                        });
                        break;
                };
            });
        });
    </script>
@endsection