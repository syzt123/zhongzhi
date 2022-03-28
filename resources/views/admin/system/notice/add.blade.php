@extends('admin.layout.base')

@section('sidebar')
    @parent

@endsection

@section('content')
    <blockquote class="layui-elem-quote layui-text">
        系统公告
    </blockquote>

    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
        <legend>添加公告</legend>
    </fieldset>
    <form class="layui-form" action="" id="addLandForm">
        <div class="layui-form-item">
            <label class="layui-form-label">公告</label>
            <div class="layui-input-block">
                <input type="text" name="notice" autocomplete="off" lay-verify="required" lay-reqtext="摄像头地址是必填项，岂能为空？"
                       placeholder="请输入" class="layui-input">
            </div>
        </div>
{{--        <div class="layui-form-item">--}}
{{--            <label class="layui-form-label">可种植的蔬菜量</label>--}}
{{--            <div class="layui-input-block">--}}
{{--                <input type="number" name="v_num" lay-verify="required" lay-reqtext="可种植的蔬菜量是必填项，岂能为空？"--}}
{{--                       placeholder="请输入" autocomplete="off" class="layui-input">--}}
{{--            </div>--}}
{{--        </div>--}}

{{--        <div class="layui-form-item">--}}
{{--            <label class="layui-form-label">状态</label>--}}
{{--            <div class="layui-input-block">--}}
{{--                <input type="radio" name="l_status" value="0" title="未使用" checked="">--}}
{{--                <input type="radio" name="l_status" value="1" title="已使用">--}}
{{--                <input type="radio" name="l_status" value="2" title="其他">--}}
{{--            </div>--}}
{{--        </div>--}}
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="submit" class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
    </form>
@endsection
@section('js')


    //JS
    <script>
        //JS
        layui.use(['form'], function () {
            var form = layui.form
                , layer = layui.layer
                , $ = layui.$ //重点处;

            //监听提交
            form.on('submit(demo1)', function (data) {
                // console.log(data.field)
                $.ajax({
                    url: "{{url("admin/system/notice/add")}}"
                    , data: data.field
                    , type: "post"
                    , headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                    , success: function (res) {
                        if(res.code)
                        {
                            layer.confirm('操作成功', {
                                btn: ['返回','继续添加'] //按钮
                            }, function(){
                                window.location = "{{url('admin/system/notice')}}"
                            }, function(){
                                $("#addLandForm")[0].reset();
                                layui.form.render();
                                layer.close();
                            });
                        }else {
                            layer.msg(res.message)
                        }

                    },
                    error:function(data){
                        layer.alert(JSON.stringify(data), {
                            title: data
                        });
                    }
                })

                return false;
            });
        });
    </script>
@endsection
