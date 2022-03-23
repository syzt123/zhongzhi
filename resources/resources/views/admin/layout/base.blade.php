<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>指尖种植</title>
    <link rel="stylesheet" href="/storage/static/layui/css/layui.css">
</head>
<body>
<div class="layui-layout layui-layout-admin">
    <div class="layui-header">
        <div class="layui-logo layui-hide-xs layui-bg-black">指尖种植</div>

        <ul class="layui-nav layui-layout-right">
            <li class="layui-nav-item layui-hide layui-show-md-inline-block">
                <a href="javascript:;">
                    <img src="//tva1.sinaimg.cn/crop.0.0.118.118.180/5db11ff4gw1e77d3nqrv8j203b03cweg.jpg"
                         class="layui-nav-img">
                    管理员
                </a>
                <dl class="layui-nav-child">
                    <dd><a href="">Your Profile</a></dd>
                    <dd><a href="">Settings</a></dd>
                    <dd><a href="">Sign out</a></dd>
                </dl>
            </li>

        </ul>
    </div>
    @section('sidebar')
        <div class="layui-side layui-bg-black">
            <div class="layui-side-scroll">
            {{--                <p>{{ request()->path() }}</p>--}}
            <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
                <ul class="layui-nav layui-nav-tree" lay-filter="test">
                    @if (request()->path() === "admin")
                        <li class="layui-nav-item  layui-nav-itemed layui-this"><a href="">首页</a></li>
                    @else
                        <li class="layui-nav-item"><a href="{{url("admin")}}">首页</a></li>
                    @endif
                    @if (strpos(request()->path(),'land'))
                        <li class="layui-nav-item layui-nav-itemed">
                            <a class="" href="javascript:;">土地管理</a>
                            <dl class="layui-nav-child">
                                @if(request()->path() === "admin/land")
                                    <dd class="layui-this"><a href="{{url("admin/land")}}">现有土地</a></dd>
                                @else
                                    <dd><a href="{{url("admin/land")}}">现有土地</a></dd>
                                @endif
                            </dl>
                        </li>
                    @else
                        <li class="layui-nav-item">
                            <a class="" href="javascript:;">土地管理</a>
                            <dl class="layui-nav-child">
                                <dd><a href="{{url("admin/land")}}">现有土地</a></dd>
                            </dl>
                        </li>
                    @endif
                    @if(strpos(request()->path(),'user'))
                        <li class="layui-nav-item layui-nav-itemed">
                            <a href="javascript:;">用户管理</a>
                            <dl class="layui-nav-child">
                                @if(request()->path() === "admin/user")
                                    <dd class="layui-this"><a href="{{url("admin/user")}}">用户列表</a></dd>
                                    <dd><a href="{{url("admin/user/buy_log")}}">购买记录</a></dd>
                                    <dd><a href="{{url("admin/user/exchange_log")}}">兑换记录</a></dd>
                                @elseif(request()->path() === "admin/user/buy_log")
                                    <dd><a href="{{url("admin/user")}}">用户列表</a></dd>
                                    <dd class="layui-this"><a href="{{url("admin/user/buy_log")}}">购买记录</a></dd>
                                    <dd><a href="{{url("admin/user/exchange_log")}}">兑换记录</a></dd>
                                @elseif(request()->path() === "admin/user/exchange_log")
                                    <dd><a href="{{url("admin/user")}}">用户列表</a></dd>
                                    <dd><a href="{{url("admin/user/buy_log")}}">购买记录</a></dd>
                                    <dd class="layui-this"><a href="{{url("admin/user/exchange_log")}}">兑换记录</a></dd>
                                @endif
                            </dl>
                        </li>
                    @else
                        <li class="layui-nav-item">
                            <a href="javascript:;">用户管理</a>
                            <dl class="layui-nav-child">
                                <dd><a href="{{url("admin/user")}}">用户列表</a></dd>
                                <dd><a href="{{url("admin/user/buy_log")}}">购买记录</a></dd>
                                <dd><a href="{{url("admin/user/exchange_log")}}">兑换记录</a></dd>
                            </dl>
                        </li>
                    @endif
                    @if(strpos(request()->path(),'vegetable'))
                        <li class="layui-nav-item layui-nav-itemed">
                            <a href="javascript:;">蔬菜管理</a>
                            <dl class="layui-nav-child">
                                @if(request()->path()=="admin/vegetable")
                                    <dd class="layui-this"><a href="{{url("admin/vegetable")}}">蔬菜种类</a></dd>
                                @else
                                    <dd><a href="{{url("admin/vegetable")}}">蔬菜种类</a></dd>
                                @endif
                            </dl>
                        </li>
                    @else
                        <li class="layui-nav-item">
                            <a href="javascript:;">蔬菜管理</a>
                            <dl class="layui-nav-child">
                                <dd><a href="{{url("admin/vegetable")}}">蔬菜种类</a></dd>
                            </dl>
                        </li>
                    @endif
                    @if(strpos(request()->path(),'system'))
                        <li class="layui-nav-item layui-nav-itemed">
                            <a href="javascript:;">系统设置</a>
                            <dl class="layui-nav-child">
                                @if(request()->path()=="admin/system/notice")
                                    <dd class="layui-this"><a href="{{url("admin/system/notice")}}">公告设置</a></dd>
                                @else
                                    <dd><a href="{{url("admin/system/notice")}}">公告设置</a></dd>
                                @endif

                            </dl>
                        </li>
                    @else
                        <li class="layui-nav-item">
                            <a href="javascript:;">系统设置</a>
                            <dl class="layui-nav-child">
                                <dd><a href="{{url("admin/system/notice")}}">公告设置</a></dd>
                            </dl>
                        </li>
                    @endif
                    @if(strpos(request()->path(),'logistics'))
                        <li class="layui-nav-item layui-nav-itemed">
                            <a href="javascript:;">物流中心</a>
                            <dl class="layui-nav-child">
                                @if(request()->path()=="admin/logistics/order")
                                    <dd class="layui-this"><a href="{{url("admin/logistics/order")}}">订单管理</a></dd>
                                    <dd><a href="{{url("admin/logistics/distribution")}}">配送管理</a></dd>
                                @elseif(request()->path()=="admin/logistics/distribution")
                                    <dd><a href="{{url("admin/logistics/order")}}">订单管理</a></dd>
                                    <dd class="layui-this"><a href="{{url("admin/logistics/distribution")}}">配送管理</a>
                                    </dd>
                                @endif
                            </dl>
                        </li>
                    @else
                        <li class="layui-nav-item">
                            <a href="javascript:;">物流中心</a>
                            <dl class="layui-nav-child">
                                <dd><a href="{{url("admin/logistics/order")}}">订单管理</a></dd>
                                <dd><a href="{{url("admin/logistics/distribution")}}">配送管理</a></dd>
                            </dl>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    @show

    <div class="layui-body">
        <!-- 内容主体区域 -->
        <div style="padding: 15px;">
            @yield('content')
        </div>
    </div>


    <div class="layui-footer">
        <!-- 底部固定区域 -->
        底部固定区域
    </div>
</div>
<script src="/storage/static/layui/layui.js"></script>
@yield('js')

</body>
</html>
