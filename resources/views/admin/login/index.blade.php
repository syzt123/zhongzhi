@extends('admin.layout.base')

@section('sidebar')
    <div style="background-image: url('/static/bg.jpeg');background-position: 0 100%;background-repeat: no-repeat;background-attachment: fixed;height: 100vh">
        <div class="layui-row">
            <div class="layui-col-md2 layui-col-md-offset6" style="padding: 200px 0">
                <div class="layui-card">
                    <div class="layui-card-header">指尖种植管理后台</div>
                    <div class="layui-card-body">
                        <form class="layui-form layui-form-pane" action="" method="post">
                            @csrf
                            <div class="layui-form-item">
                                <label class="layui-form-label">用户名</label>
                                <div class="layui-input-block">
                                    <input type="text" name="name" lay-verify="required" lay-reqtext="用户名是必填项，岂能为空？" autocomplete="off" placeholder="请输入标题"
                                           class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">密码</label>
                                <div class="layui-input-block">
                                    <input type="password" name="password" lay-verify="required" lay-reqtext="密码是必填项，岂能为空？" placeholder="请输入密码" autocomplete="off"
                                           class="layui-input">
                                </div>
                                <div class="layui-form-mid layui-word-aux">{{$pass}}</div>
                            </div>
                            <div class="layui-form-item">
                                <button class="layui-btn layui-btn-fluid" lay-submit="" lay-filter="demo2">登录</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('js')
    <script>

        //JS
        layui.use(['element', 'layer', 'util','form'], function () {
            var form = layui.form
                , $ = layui.$;



        });
    </script>
@endsection

