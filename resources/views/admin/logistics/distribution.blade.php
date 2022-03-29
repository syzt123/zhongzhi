@extends('admin.layout.base')

@section('sidebar')
    @parent

@endsection

@section('content')
    <table class="layui-hide" id="test" lay-filter="test"></table>
@endsection
{{--<script type="text/html" id="toolbarDemo">--}}
{{--    <div class="layui-btn-container">--}}
{{--        <button class="layui-btn layui-btn-sm" lay-event="getCheckData">添加</button>--}}
{{--    </div>--}}
{{--</script>--}}
{{--<script type="text/html" id="barDemo">--}}
{{--    <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>--}}
{{--    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>--}}
{{--</script>--}}
<form class="layui-form" action="" id="status" style="margin-top: 30px">
    <div class="layui-form-item">
        <label class="layui-form-label">状态</label>
        <div class="layui-input-inline">
            <select name="status" lay-filter="aihao">
                <option value="1" selected="">待配送</option>
                <option value="2">配送中</option>
                <option value="3">配送完成</option>
            </select>
        </div>
    </div>
    <input type="hidden" name="id" id="id">
    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit lay-filter="formDemo">立即提交</button>
        </div>
    </div>
</form>
@section('js')
    <script>
        //JS
        layui.use(['table', 'form'], function () {
            var table = layui.table,
                form = layui.form,
                $ = layui.$;

            table.render({
                elem: '#test'
                , url: "{{url('admin/logistics/distribution/data')}}"
                // , toolbar: '#toolbarDemo' //开启头部工具栏，并为其绑定左侧模板
                , page: true //开启分页
                , cellMinWidth: 80 //全局定义常规单元格的最小宽度，layui 2.2.1 新增
                , cols: [[
                    {field: 'id', min: 80, title: 'ID', sort: true}
                    , {field: 'order_id', min: 200, title: '订单号',}
                    , {field: 'nickname', min: 80, title: '用户'}
                    // , {field: 'order_type', min: 80, title: '订单类型'}
                    , {field: 'f_price', min: 80, title: '金额'}
                    , {field: 'status', event: 'edit_status', min: 80, title: '已支付'}
                    , {field: 'create_time', min: 80, title: '时间'}
                    // ,{field:'v_address', min:80, title: '配送地址'}
                    // , {fixed: 'right', title: '操作', toolbar: '#barDemo', width: 150}
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
            //监听单元格编辑
            table.on('tool(test)', function (obj) {
                var value = obj.value //得到修改后的值
                    , data = obj.data //得到所在行所有键值
                    , event = obj.event //得到所在行所有键值
                    , field = obj.field; //得到字段
                if (event === "edit_status") {
                    console.log(obj)
                    $("#id").val(obj.data.id)
                    layer.open({
                        type: 1,
                        area: ['400px', '260px'], //宽高
                        content: $('#status') //这里content是一个DOM，注意：最好该元素要存放在body最外层，否则可能被其它的相对元素所影响
                    });

                }

                // layer.msg('[ID: '+ data.id +'] ' + field + ' 字段更改值为：'+ util.escape(value));
            });
            //监听提交
            form.on('submit(formDemo)', function (data) {
                console.log(data);

                $.ajax({
                    url: "{{url('admin/logistics/distribution')}}",
                    data: data.field,
                    type: "put",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function (res) {
                        table.render({
                            elem: '#test'
                            , url: "{{url('admin/logistics/distribution/data')}}"
                        });
                    },
                    error: function (data) {
                        layer.alert(JSON.stringify(data), {
                            title: data
                        });
                    }
                })
                // layer.msg(JSON.stringify(data.field));
                // return false;
            });
        });
    </script>
@endsection


