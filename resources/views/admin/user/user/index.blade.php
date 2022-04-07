@extends('admin.layout.base')

@section('sidebar')
    @parent

@endsection

@section('content')
    <table class="layui-hide" id="test"  lay-filter="test"></table>
@endsection
<script type="text/html" id="toolbarDemo">
    <div class="layui-btn-container">
        <button class="layui-btn layui-btn-sm" lay-event="add">添加</button>
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
            var table = layui.table,$=layui.$;

            table.render({
                elem: '#test'
                ,url: "{{url('admin/user/user/data')}}"
                ,toolbar: '#toolbarDemo' //开启头部工具栏，并为其绑定左侧模板
                ,page: true //开启分页
                ,cellMinWidth: 80 //全局定义常规单元格的最小宽度，layui 2.2.1 新增
                ,cols: [[
                    {field:'id', min:80, title: 'ID', sort: true}
                    ,{field:'tel', min:80, title: '电话'}
                    ,{field:'nickname', min:80, title: '昵称'}
                    ,{field:'gold', min:80, title: '蔬菜币'}
                    ,{field:'vegetable_num', min:80, title: '蔬菜量'}
                    ,{field:'status', min:80, title: '状态'}
                    ,{field:'v_address', min:80, title: '配送地址'}
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
            //头工具栏事件
            table.on('toolbar(test)', function(obj){
                console.log(obj)
                var checkStatus = table.checkStatus(obj.config.id);
                switch(obj.event){
                    case 'add':
                        window.location = "{{url('admin/user/user/add')}}";
                        break;
                }
            });
            //监听行工具事件
            table.on('tool(test)', function(obj){
                var data = obj.data;
                console.log(data.id)
                if(obj.event === 'del'){
                    layer.confirm('真的删除行么', function(index){
                        $.ajax({
                            url:"{{url('admin/user/user/del/id')}}".replace(/id/,data.id),
                            type:'delete'
                            , headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            }
                            , success: function (res) {
                                if(res.code)
                                {
                                    obj.del();
                                    layer.close(index);
                                    table.render(
                                        {
                                            elem: '#test'
                                            , url: "{{url('admin/user/user/data')}}"
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
                                            ,parseData: function(res){ //res 即为原始返回的数据
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
                                        }
                                    )
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
                    });
                } else if(obj.event === 'edit'){
                    window.location="{{ url('admin/user/user/edit/id') }}".replace(/id/,data.id);
                }
            });
        });
    </script>
@endsection
