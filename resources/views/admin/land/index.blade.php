@extends('admin.layout.base')

@section('sidebar')
    @parent

@endsection

@section('content')
    <table class="layui-hide" id="test"></table>


@endsection
<script type="text/html" id="toolbarDemo">
    <div class="layui-btn-container">
        <button class="layui-btn layui-btn-sm" lay-event="getCheckData">添加</button>
    </div>
</script>
<script type="text/html" id="barDemo">
    <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
</script>
@section('js')


    //JS
    <script>
        //JS
        layui.use('table', function () {
            var table = layui.table;

            table.render({
                elem: '#test'
                , url: "{{url('admin/land/data')}}"
                , toolbar: '#toolbarDemo' //开启头部工具栏，并为其绑定左侧模板
                , page: true //开启分页
                , cellMinWidth: 80 //全局定义常规单元格的最小宽度，layui 2.2.1 新增
                , cols: [[
                    {field: 'id', min: 80, title: 'ID', sort: true}
                    , {field: 'monitor', min: 80, title: '摄像头地址'}
                    , {field: 'v_num', min: 80, title: '种植量'}
                    , {field: 'l_status', min: 80, title: '状态'}
                    , {fixed: 'right', title: '操作', toolbar: '#barDemo', width: 150}
                ]]
            });
        });
    </script>
@endsection
