@extends('admin.layout.base')

@section('sidebar')
    @parent

@endsection

@section('content')
    <blockquote class="layui-elem-quote layui-text">
        土地管理
    </blockquote>

    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
        <legend>添加土地</legend>
    </fieldset>
    <form class="layui-form" action="" id="addLandForm">
        <div class="layui-form-item">
            <label class="layui-form-label">摄像头设备号</label>
            <div class="layui-input-block">
                <input type="text" name="device_serial" autocomplete="off" lay-verify="required" lay-reqtext="摄像头设备号是必填项，岂能为空？"
                       placeholder="请输入 如J38620611" class="layui-input">
            </div>
            <div class="layui-input-block">
                <button type="button" onclick="getLiveAddress()">获取直播地址</button>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">摄像头地址</label>
            <div class="layui-input-block">
                <input type="text" name="monitor" autocomplete="off" lay-verify="required" lay-reqtext="摄像头地址是必填项，岂能为空？"
                       placeholder="请输入" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">可种植的蔬菜量</label>
            <div class="layui-input-block">
                <input type="number" name="v_num" lay-verify="required" lay-reqtext="可种植的蔬菜量是必填项，岂能为空？"
                       placeholder="请输入" autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">状态</label>
            <div class="layui-input-block">
                <input type="radio" name="l_status" value="0" title="未使用" checked="">
                <input type="radio" name="l_status" value="1" title="已使用">
                <input type="radio" name="l_status" value="2" title="其他">
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
        function getLiveAddress() {
            console.log(44)
            //接口返回数据
            let rs = {"code":200,"data":[
                    {"id":"436468143050493952",
                        "url":"https://open.ys7.com/v3/openlive/J38620611_1_2.m3u8?expire=1650420514&id=436468143050493952&t=aa745d5593c96dff1a7e60c22bc81175706ae5a1d59557690c92dbdc0cf19ce5&ev=100",
                        "expireTime":"2022-04-20 10:08:34",
                    }
                ]};

            console.log(rs,$)
        }

        //JS
        layui.use(['form'], function () {
            var form = layui.form
                , layer = layui.layer
                , $ = layui.$ //重点处;

            //监听提交
            form.on('submit(demo1)', function (data) {
                // console.log(data.field)
                $.ajax({
                    url: "{{url("admin/land/add/submit")}}"
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
