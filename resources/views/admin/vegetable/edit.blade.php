@extends('admin.layout.base')

@section('sidebar')
    @parent

@endsection

@section('content')
    <blockquote class="layui-elem-quote layui-text">
        蔬菜管理
    </blockquote>

    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
        <legend>添加蔬菜</legend>
    </fieldset>
    <form class="layui-form" action="" id="addLandForm">
        <div class="layui-form-item">
            <label class="layui-form-label">名称</label>
            <div class="layui-input-block">
                <input type="text" name="v_type" value="{{$vegetableTyp->v_type}}" autocomplete="off" lay-verify="required" lay-reqtext="名称是必填项，岂能为空？"
                       placeholder="请输入" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">认领价格</label>
            <div class="layui-input-block">
                <input type="number" name="v_price" value="{{$vegetableTyp->v_price}}" lay-verify="required" lay-reqtext="认领价格是必填项，岂能为空？"
                       placeholder="请输入" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">蔬菜币</label>
            <div class="layui-input-block">
                <input type="number" name="f_price" value="{{$vegetableTyp->f_price}}"lay-verify="required" lay-reqtext="蔬菜币是必填项，岂能为空？"
                       placeholder="请输入" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">种子时期</label>
            <div class="layui-input-block">
                <input type="number" name="grow_1" value="{{$vegetableTyp->grow_1}}" lay-verify="required" lay-reqtext="种子时期是必填项，岂能为空？"
                       placeholder="请输入" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">幼苗时期</label>
            <div class="layui-input-block">
                <input type="text" name="grow_2" value="{{$vegetableTyp->grow_2}}" lay-verify="required" lay-reqtext="幼苗时期是必填项，岂能为空？"
                       placeholder="请输入" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">生长时期</label>
            <div class="layui-input-block">
                <input type="text" name="grow_3" value="{{$vegetableTyp->grow_3}}" lay-verify="required" lay-reqtext="生长时期是必填项，岂能为空？"
                       placeholder="请输入" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">成年期</label>
            <div class="layui-input-block">
                <input type="text" name="grow_4" value="{{$vegetableTyp->grow_4}}" lay-verify="required" lay-reqtext="成年期是必填项，岂能为空？"
                       placeholder="请输入" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">成熟期</label>
            <div class="layui-input-block">
                <input type="text" name="grow_5" value="{{$vegetableTyp->grow_4}}" lay-verify="required" lay-reqtext="成熟期是必填项，岂能为空？"
                       placeholder="请输入" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">可存放时期</label>
            <div class="layui-input-block">
                <input type="text" name="storage_time" value="{{$vegetableTyp->storage_time}}" lay-verify="required" lay-reqtext="可存放时期是必填项，岂能为空？"
                       placeholder="请输入" autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">状态</label>
            <div class="layui-input-block">
                <input type="radio" name="status" value="1" title="可种植" checked="">
                <input type="radio" name="status" value="2" title="不可种植">
            </div>
        </div>
        <input name="id" type="hidden" value="{{$vegetableTyp->id}}">
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
            $("input[name=status][value={{$vegetableTyp->status}}]").prop("checked","false");
            //监听提交
            form.on('submit(demo1)', function (data) {
                // console.log(data.field)
                $.ajax({
                    url: "{{url("admin/vegetable/edit")}}"
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
                                window.location = "{{url('admin/vegetable')}}"
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
