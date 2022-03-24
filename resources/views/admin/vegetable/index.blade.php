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
    <script>
        //JS
        layui.use('table', function(){
            var table = layui.table;

            table.render({
                elem: '#test'
                ,url: "{{url('admin/vegetable/data')}}"
                ,toolbar: '#toolbarDemo' //开启头部工具栏，并为其绑定左侧模板
                ,page: true //开启分页
                ,cellMinWidth: 80 //全局定义常规单元格的最小宽度，layui 2.2.1 新增
                ,cols: [[
                    {field:'id', min:80, title: 'ID', sort: true}
                    ,{field:'v_type', min:80, title: '名称'}
                    ,{field:'status', min:80, title: '状态'}
                    ,{field:'v_price', min:80, title: '认领价格'}
                    ,{field:'f_price', min:80, title: '蔬菜币'}
                    ,{field:'grow_1', min:80, title: '种子时期'}
                    ,{field:'grow_2', min:80, title: '幼苗时期'}
                    ,{field:'grow_3', min:80, title: '生长时期'}
                    ,{field:'grow_4', min:80, title: '成年期'}
                    ,{field:'grow_5', min:80, title: '成熟期'}
                    ,{field:'storage_time', min:80, title: '贮藏时间'}
                    // ,{field:'v_address', min:80, title: '配送地址'}
                    ,{fixed: 'right', title:'操作', toolbar: '#barDemo', width:150}
                ]]
                , parseData: function (res) { //res 即为原始返回的数据
                    // console.log(res)
                    // for (var i = 0; i < res.data.data.length; i++) {
                    //     res.data.data[i].v_price = res.data.data[i].v_price / 100
                    //     res.data.data[i].n_price = res.data.data[i].n_price / 100
                    // }
                    return {
                        "code": !res.code, //解析接口状态
                        "msg": res.message, //解析提示文本
                        "count": res.data.total, //解析数据长度
                        "data": res.data.data //解析数据列表
                    };
                }
            });
        });
    </script>
@endsection
