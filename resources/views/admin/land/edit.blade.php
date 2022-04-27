@extends('admin.layout.base')

@section('sidebar')
    @parent

@endsection

@section('content')
    <blockquote class="layui-elem-quote layui-text">
        土地管理
    </blockquote>

    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
        <legend>编辑土地</legend>
    </fieldset>
    <form class="layui-form" action="" id="addLandForm">
        {{--<div class="layui-form-item">
            <label class="layui-form-label">摄像头地址</label>
            <div class="layui-input-block">
                <input type="text" name="monitor" value="{{$land->monitor}}" autocomplete="off" lay-verify="required" lay-reqtext="摄像头地址是必填项，岂能为空？"
                       placeholder="请输入" class="layui-input">
            </div>
        </div>--}}
        <div class="layui-form-item">
            <label class="layui-form-label">土地名称</label>
            <div class="layui-input-block">
                <input type="text" name="name" lay-verify="required" value="{{$land->name}}"  lay-reqtext="土地名称是必填项，岂能为空？"
                       placeholder="请输入" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">可种植的蔬菜量</label>
            <div class="layui-input-block">
                <input type="number" name="v_num" value="{{$land->v_num}}" lay-verify="required" lay-reqtext="可种植的蔬菜量是必填项，岂能为空？"
                       placeholder="请输入" autocomplete="off" class="layui-input">
            </div>
        </div>


        <div class="row" style="padding:10px;">
            <h4 style="font-size: 40px;color: red;">上传视频到腾讯云点播</h4>
            <input type="file" accept="video/*" onchange="vExampleUpload(this)"/>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">腾讯云点播url</label>
            <div class="layui-input-block">
                <input type="text" name="tx_video_url" id="tx_video_url" value="{{$land->tx_video_url}}" style="background: #cccccc" autocomplete="off" lay-verify="required" lay-reqtext="摄像头地址是必填项，岂能为空？"
                       placeholder="点击上传视频文件即自动填充"  readonly class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">腾讯云文件ID</label>
            <div class="layui-input-block">
                <input type="text" name="tx_video_id" id="tx_video_id" value="{{$land->tx_video_id}}" style="background: #cccccc" autocomplete="off" lay-verify="required" lay-reqtext="摄像头地址是必填项，岂能为空？"
                       placeholder="点击上传视频文件即自动填充" readonly class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">状态</label>
            <div class="layui-input-block">
                <input type="radio" name="l_status" value="0" title="未使用">
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

    <script src="https://cdn-go.cn/cdn/vod-js-sdk-v6/latest/vod-js-sdk-v6.js"></script>
    <script src="https://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script>
        /**
         * 计算签名。
         **/
        function getSignature() {
            return axios.post('https://pay.zjzc88.com/api/v1/vod/getSign', JSON.stringify({
                "Action": "GetUgcUploadSign"
            })).then(function (response) {
                console.log(response);//return;
                return response.data.data.sign
            });
        };

        /*
        * 防盗链地址获取。
        */
        function getAntiLeechUrl(videoUrl, callback) {
            return axios.post('https://pay.zjzc88.com/api/v1/vod/getSign', JSON.stringify({
                Action: "GetAntiLeechUrl",
                Url: videoUrl,
            })).then(function (response) {
                return response.data.data.url
            })
        }

        /**
         * 上传
         * @param rs
         */
        var uploaderInfos = [];
        function vExampleUpload(rs) {
            // new 新对象
            var tcInstance = new TcVod.default({
                getSignature: getSignature
            })
            if (rs.files[0] === undefined) {
                return;
            }
            var mediaFile = rs.files[0]

            var uploader = tcInstance.upload({
                mediaFile: mediaFile,
            })
            uploader.on('media_progress', function (info) {
                uploaderInfo.progress = info.percent;
            })
            uploader.on('media_upload', function (info) {
                uploaderInfo.isVideoUploadSuccess = true;
            })

            // console.log(uploader, 'uploader')

            var uploaderInfo = {
                videoInfo: uploader.videoInfo,
                isVideoUploadSuccess: false,
                isVideoUploadCancel: false,
                progress: 0,
                fileId: '',
                videoUrl: '',
                cancel: function () {
                    uploaderInfo.isVideoUploadCancel = true;
                    uploader.cancel()
                },
            }

            // this.uploaderInfos.push(uploaderInfo)


            uploader.done().then(function (doneResult) {
                console.log('doneResult', doneResult)
                uploaderInfo.fileId = doneResult.fileId;

                // 赋值
                $("#tx_video_url").val(doneResult.video.url)
                $("#tx_video_id").val(doneResult.fileId)
                //$("#cover_id").val(doneResult.cover.url)
                return getAntiLeechUrl(doneResult.video.url);
            }).then(function (videoUrl) {
                uploaderInfo.videoUrl = videoUrl
                //rs.reset();
            })



            // rs.reset()
            console.log('执行完')
        }

        //let sign = getSignature();


    </script>

    //JS
    <script>
        //JS
        layui.use(['form'], function () {
            var form = layui.form
                , layer = layui.layer
                , $ = layui.$ //重点处;
            $("input[name=l_status][title={{$land->l_status}}]").prop("checked","false");
            form.render();
            // $("input[name=l_status][value=1]").prop("checked","true");
            //监听提交
            form.on('submit(demo1)', function (data) {
                // console.log(data.field)
                data.field.id = "{{$land->id}}"
                $.ajax({
                    url: "{{url("admin/land/edit/submit")}}"
                    , data: data.field
                    , type: "put"
                    , headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                    , success: function (res) {
                        if(res.code)
                        {
                            layer.confirm('操作成功', {
                                btn: ['返回','确认'] //按钮
                            }, function(){
                                window.location = "{{url('admin/land')}}"
                            }, function(){
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
