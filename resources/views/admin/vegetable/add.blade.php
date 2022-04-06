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
                <input type="text" name="v_type" autocomplete="off" lay-verify="required" lay-reqtext="名称是必填项，岂能为空？"
                       placeholder="请输入" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">认领价格</label>
            <div class="layui-input-block">
                <input type="number" name="v_price" lay-verify="required" lay-reqtext="认领价格是必填项，岂能为空？"
                       placeholder="请输入" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">蔬菜币</label>
            <div class="layui-input-block">
                <input type="number" name="f_price" lay-verify="required" lay-reqtext="蔬菜币是必填项，岂能为空？"
                       placeholder="请输入" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">种子时期</label>
            <div class="layui-input-inline">
                <input type="number" name="grow_1" lay-verify="required" lay-reqtext="种子时期是必填项，岂能为空？"
                       placeholder="请输入" autocomplete="off" class="layui-input">
                <input type="hidden" name="img_grow_1" lay-verify="required" lay-reqtext="请上传种子时期图片">
            </div>
            <div class="layui-input-inline" style="width: 110px">
                <button type="button" class="layui-btn upload" id="grow_1" onclick="importWaypointModel('grow_1')">
                    <i class="layui-icon">&#xe67c;</i>选择图片
                </button>
            </div>
            <input type="hidden" id="upload">
            <div class="layui-input-inline">
                <img style="width: 40px;height: auto;" id="img_grow_1"
                     er="https://img1.baidu.com/it/u=1030265100,1120065809&fm=253&fmt=auto&app=138&f=JPEG?w=350&h=350">
                <div style="width: 40px">
                    <div class="layui-progress" style="height: 2px ;border-radius:unset" lay-showpercent="yes"
                         lay-filter="grow_1">
                        <div class="layui-progress-bar" style="height: 2px ;border-radius:unset" lay-percent=""></div>
                    </div>
                </div>
            </div>
            <label class="layui-form-label">幼苗时期</label>
            <div class="layui-input-inline">
                <input type="text" name="grow_2" lay-verify="required" lay-reqtext="幼苗时期是必填项，岂能为空？"
                       placeholder="请输入" autocomplete="off" class="layui-input">
                <input type="hidden" name="img_grow_2" lay-verify="required" lay-reqtext="请上传幼苗时期图片">
            </div>
            <div class="layui-input-inline" style="width: 110px">
                <button type="button" class="layui-btn upload" id="grow_2" onclick="importWaypointModel('grow_2')">
                    <i class="layui-icon">&#xe67c;</i>选择图片

                </button>
            </div>
            <div class="layui-input-inline">
                <img style="width: 40px;height: auto;" id="img_grow_2"
                     src="https://img1.baidu.com/it/u=1030265100,1120065809&fm=253&fmt=auto&app=138&f=JPEG?w=350&h=350">
                <div style="width: 40px">
                    <div class="layui-progress" style="height: 2px ;border-radius:unset" lay-showpercent="yes"
                         lay-filter="grow_2">
                        <div class="layui-progress-bar" style="height: 2px ;border-radius:unset" lay-percent=""></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">成长期</label>
            <div class="layui-input-inline">
                <input type="text" name="grow_3" lay-verify="required" lay-reqtext="成长期是必填项，岂能为空？"
                       placeholder="请输入" autocomplete="off" class="layui-input">
                <input type="hidden" name="img_grow_3" lay-verify="required" lay-reqtext="请上传成长时期图片">
            </div>
            <div class="layui-input-inline" style="width: 110px">
                <button type="button" class="layui-btn upload" id="grow_3" onclick="importWaypointModel('grow_3')">
                    <i class="layui-icon">&#xe67c;</i>选择图片
                </button>
            </div>
            <div class="layui-input-inline">
                <img style="width: 40px;height: auto;" id="img_grow_3"
                     src="https://img1.baidu.com/it/u=1030265100,1120065809&fm=253&fmt=auto&app=138&f=JPEG?w=350&h=350">
                <div style="width: 40px">
                    <div class="layui-progress" style="height: 2px ;border-radius:unset" lay-showpercent="yes"
                         lay-filter="grow_3">
                        <div class="layui-progress-bar" style="height: 2px ;border-radius:unset" lay-percent=""></div>
                    </div>
                </div>
            </div>
            <label class="layui-form-label">成年期</label>
            <div class="layui-input-inline">
                <input type="text" name="grow_4" lay-verify="required" lay-reqtext="幼苗时期是必填项，岂能为空？"
                       placeholder="请输入" autocomplete="off" class="layui-input">
                <input type="hidden" name="img_grow_4" lay-verify="required" lay-reqtext="请上传成年时期图片">
            </div>
            <div class="layui-input-inline" style="width: 110px">
                <button type="button" class="layui-btn upload" id="grow_4" onclick="importWaypointModel('grow_4')">
                    <i class="layui-icon">&#xe67c;</i>选择图片
                </button>
            </div>
            <div class="layui-input-inline">
                <img style="width: 40px;height: auto;" id="img_grow_4"
                     src="https://img1.baidu.com/it/u=1030265100,1120065809&fm=253&fmt=auto&app=138&f=JPEG?w=350&h=350">
                <div style="width: 40px">
                    <div class="layui-progress" style="height: 2px ;border-radius:unset" lay-showpercent="yes"
                         lay-filter="grow_4">
                        <div class="layui-progress-bar" style="height: 2px ;border-radius:unset" lay-percent=""></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">成熟期</label>
            <div class="layui-input-inline">
                <input type="text" name="grow_5" lay-verify="required" lay-reqtext="幼苗时期是必填项，岂能为空？"
                       placeholder="请输入" autocomplete="off" class="layui-input">
                <input type="hidden" name="img_grow_5" lay-verify="required" lay-reqtext="请上传成熟时期图片">
            </div>
            <div class="layui-input-inline" style="width: 110px">
                <button type="button" class="layui-btn upload" id="grow_5" onclick="importWaypointModel('grow_5')">
                    <i class="layui-icon">&#xe67c;</i>选择图片
                </button>
            </div>
            <div class="layui-input-inline">
                <img style="width: 40px;height: auto;" id="img_grow_5"
                     src="https://img1.baidu.com/it/u=1030265100,1120065809&fm=253&fmt=auto&app=138&f=JPEG?w=350&h=350">
                <div style="width: 40px">
                    <div class="layui-progress" style="height: 2px ;border-radius:unset" lay-showpercent="yes"
                         lay-filter="grow_5">
                        <div class="layui-progress-bar" style="height: 2px ;border-radius:unset" lay-percent=""></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">可存放时期</label>
            <div class="layui-input-block">
                <input type="text" name="storage_time" lay-verify="required" lay-reqtext="可存放时期是必填项，岂能为空？"
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
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="submit" id="submit" class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
    </form>
@endsection
@section('js')
    //JS
    <script>
        //JS
        var uploadInst, $;
        layui.use(['form', 'upload', 'element', 'layer'], function () {
            var form = layui.form
                , upload = layui.upload
                , element = layui.element
                , layer = layui.layer
                , up_item;//重点处;
            $ = layui.jquery

            //常规使用 - 普通图片上传
            uploadInst = upload.render({
                elem: '#upload'
                , url: '{{url('admin/vegetable/add/upload')}}' //此处用的是第三方的 http 请求演示，实际使用时改成您自己的上传接口即可。
                // , auto: false
                // , bindAction:"#submit"
                , headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                }
                , choose: function (obj) {
                    //预读本地文件，如果是多文件，则会遍历。(不支持ie8/9)
                    obj.preview(function (index, file, result) {
                        console.log(index); //得到文件索引
                        console.log(file); //得到文件对象
                        console.log(result); //得到文件base64编码，比如图片

                        //obj.resetFile(index, file, '123.jpg'); //重命名文件名，layui 2.3.0 开始新增

                        //这里还可以做一些 append 文件列表 DOM 的操作

                        //obj.upload(index, file); //对上传失败的单个文件重新上传，一般在某个事件中使用
                        //delete files[index]; //删除列表中对应的文件，一般在某个事件中使用
                    });
                }
                , before: function (obj) {
                    console.log(obj)
                    up_item = this.data.grow;
                    console.log(up_item);
                    //预读本地文件示例，不支持ie8
                    obj.preview(function (index, file, result) {
                        $('#img_' + up_item).attr('src', result); //图片链接（base64）
                    });
                    element.progress(up_item, '0%'); //进度条复位
                    layer.msg('上传中', {icon: 16, time: 0});
                }
                , done: function (res) {
                    console.log(res)
                    //如果上传失败
                    if (res.code === 0) {
                        return layer.msg('上传失败');
                    } else {
                        $("input[name=img_" + up_item + "]").val(res.data)
                    }
                }
                , error: function () {
                    layer.msg('上传失败，请重试！', {icon: 1});
                }
                //进度条
                , progress: function (n, elem, e) {

                    element.progress(up_item, n + '%'); //可配合 layui 进度条元素使用
                    if (n == 100) {
                        layer.msg('上传完毕', {icon: 1});
                    }
                }
            });

            //监听提交
            form.on('submit(demo1)', function (data) {
                // console.log(data.field)
                $.ajax({
                    url: "{{url("admin/vegetable")}}"
                    , data: data.field
                    , type: "post"
                    , headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                    , success: function (res) {
                        if (res.code) {
                            layer.confirm('操作成功', {
                                btn: ['返回', '继续添加'] //按钮
                            }, function () {
                                window.location = "{{url('admin/vegetable')}}"
                            }, function () {
                                $("#addLandForm")[0].reset();
                                layui.form.render();
                                layer.close();
                            });
                        } else {
                            layer.msg(res.message)
                        }

                    },
                    error: function (data) {
                        layer.alert(JSON.stringify(data), {
                            title: data
                        });
                    }
                })

                return false;
            });
        });

        function importWaypointModel(grow) {
            //重载该实例，支持重载全部基础参数
            uploadInst.reload({
                data: {grow: grow}
            });
            $("#upload").click();
        }
    </script>
@endsection
