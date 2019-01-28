@extends('layui.default')
@section('content')
    <style>
        .order-box{display: block;padding: 20px 0 0 20px;}
        .order-box p{height: 40px;line-height: 40px;font-size: 20px;margin-top: 10px}
        .order-box p span{padding: 0 20px 0 20px}
        .order-box p .requirement-cname{border: 2px solid #0C0C0C}
        .requirement-name{border: 2px solid #ff9800}
        .requirement-pname{color: #e91e63}
        .requirement-time{border: 2px solid #3f9ae5}
        .requirement-total{border: 2px solid #3F51B5}
    </style>
    <blockquote class="layui-elem-quote layui-text">
        顺序 ： 时间 || 用户 || 商品名称 / 商品数量 / 商品单价 || 总价
    </blockquote>
    <div class="order-box">
        <form class="layui-form" action="">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">时间</label>
                    <div class="layui-input-inline">
                        <input type="text" name="time" id="date" lay-verify="date" placeholder="yyyy-MM-dd" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
                </div>
            </div>
        </form>
        @foreach($datas as $data)
            <p class="customer-p">
                <span class="requirement-time">{{ $data['time'] }}</span>
                <span class="requirement-cname">{{ $data['cname'] }}</span>
                @foreach($data['data'] as $post)
                    <span class="requirement-name"><span class="requirement-pname">{{ $post['pname']  }}</span>/<span class="requirement-num">{{ $post['requirement'] }}</span>/<span class="requirement-num">￥{{ $post['price'] }}</span></span>
                @endforeach
                <span class="requirement-total">总价： {{ $data['total'] }}</span>
            </p>
        @endforeach
    </div>
@section('layuijs')
    <script>
        layui.use(['form', 'layedit', 'laydate'], function(){
            var form = layui.form
            ,layer = layui.layer
            ,layedit = layui.layedit
            ,laydate = layui.laydate;
            //日期
            laydate.render({
                elem: '#date'
            });

            //监听提交
            form.on('submit(demo1)', function(data){
                location.href="?time="+data.field.time;
                return false;
            });
        });
    </script>
@endsection