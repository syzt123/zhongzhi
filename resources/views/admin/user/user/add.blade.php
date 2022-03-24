@extends('admin.layout.base')

@section('sidebar')
    @parent

@endsection

@section('content')
    <blockquote class="layui-elem-quote layui-text">
        用户管理
    </blockquote>

    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
        <legend>添加用户</legend>
    </fieldset>
    <form class="layui-form" action="" id="addLandForm">
        <div class="layui-form-item">
            <label class="layui-form-label">昵称</label>
            <div class="layui-input-block">
                <input type="text" name="nickname" autocomplete="off" lay-verify="required" lay-reqtext="摄像头地址是必填项，岂能为空？"
                       placeholder="请输入" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">电话</label>
            <div class="layui-input-block">
                <input type="number" name="tel" lay-verify="required" lay-reqtext="可种植的蔬菜量是必填项，岂能为空？"
                       placeholder="请输入" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">蔬菜币</label>
            <div class="layui-input-block">
                <input type="number" name="gold" lay-verify="required" lay-reqtext="可种植的蔬菜量是必填项，岂能为空？"
                       placeholder="请输入" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">蔬菜</label>
            <div class="layui-input-block">
                <input type="number" name="vegetable_num" lay-verify="required" lay-reqtext="可种植的蔬菜量是必填项，岂能为空？"
                       placeholder="请输入" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">物流地址</label>
            <div class="layui-input-block">
                <input type="text" name="v_address" lay-verify="required" lay-reqtext="可种植的蔬菜量是必填项，岂能为空？"
                       placeholder="请输入" autocomplete="off" class="layui-input">
            </div>
        </div>
        <input name="password" type="hidden" value="123456">
        <div class="layui-form-item">
            <label class="layui-form-label">状态</label>
            <div class="layui-input-block">
                <input type="radio" name="status" value="0" title="禁用" checked="">
                <input type="radio" name="status" value="1" title="正常">
            </div>
        </div>
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
                    url: "{{url("admin/user/user/add/submit")}}"
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
                                window.location = "{{url('admin/land')}}"
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
