@extends('admin.layouts.layout')

@section('content')
    <div class="content-wrapper" style="min-height: 901px;">
        <!-- 头部文字 -->
        <section class="content-header">
            <h3>
                项目
                <small>使用本推送系统的所有项目</small>
            </h3>
            @include('admin.layouts._tip')
        </section>
        <!-- 主要内容 -->
        <section class="content">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">项目列表</h3>
                </div>
                <div class="box-footer clearfix">
                    <a href="/addoredit-project" class="btn btn-sm btn-info btn-flat pull-left">新增项目</a>
                </div>
                <div class="box-body">
                    <table id="example2" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>ID（点击编辑）</th>
                            <th>项目名称</th>
                            <th>描述</th>
                            <th>获取Token链接</th>
                            <th>创建时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        @foreach($oAccounts as $k=>$v)
                            <tbody>
                                <tr>
                                    <td><a href="/addoredit-project?id={{$v->id}}">{{$v->id}}</a></td>
                                    <td>{{str_limit($v->name,48)}}</td>
                                    <td><span class="label label-success">{{$v->desc}}</span></td>
                                    <td>{{$v->url}}</td>
                                    <td>{{$v->created_at}}</td>
                                    <td>
                                        <button type="button" class="btn btn-block btn-danger" style="width: 60px" onclick="del({{$v->id}})">删除</button>
                                        <form id="destory_{{$v->id}}" method="post" action="/delete-project?id={{$v->id}}">
                                            {{csrf_field()}}
                                        </form>
                                    </td>
                                </tr>
                            </tbody>
                        @endforeach
                    </table>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('script')
    <script type="text/javascript" src="{{ asset('/js/common.js') }}"></script>
    <script>
        $(function () {
            $('#example2').DataTable({
                'paging': true,
                'lengthChange': false,
                'searching': false,
                'ordering': true,
                'info': true,
                'autoWidth': false
            })
        })
    </script>
@endsection



