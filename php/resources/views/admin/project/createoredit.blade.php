@extends('admin.layouts.layout')

@section('css')
    <link rel="stylesheet" href="/adminlte/bower_components/select2/dist/css/select2.min.css">
@endsection
@section('content')
    <div class="content-wrapper" style="min-height: 901px;">
        <section class="content-header">
            <h1>项目
                <small>Add/Edit</small>
            </h1>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">表单</h3>
                        </div>
                        <form class="form-horizontal" id="form_project_add" action="/save-project" method="post">
                            {{ csrf_field() }}
                            <input type="hidden" class="form-control" id="id" name="id"
                                   @if(isset($oProject))
                                   value="{{ $oProject->id }}"
                                   @else
                                   value=""
                                   @endif
                            >
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="name" class="col-sm-1 control-label">项目名称</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="name" name="name"
                                               @if(isset($oProject))
                                               value="{{ $oProject->name }}"
                                               @else
                                               value="{{ old('name') }}"
                                               @endif
                                               placeholder="项目名称">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="desc" class="col-sm-1 control-label">项目描述</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="desc" name="desc"
                                               placeholder="项目描述"
                                               @if(isset($oProject))
                                               value="{{ $oProject->desc }}"
                                               @else
                                               value="{{ old('desc') }}"
                                                @endif
                                        >
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="url" class="col-sm-1 control-label">Url</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="url" name="url"
                                               placeholder="获取本项目token链接"
                                               @if(isset($oProject))
                                               value="{{ $oProject->url }}"
                                               @else
                                               value="{{ old('url') }}"
                                                @endif
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="box-footer">
                                <button type="button" class="btn btn-default" style="margin-left: 20%" onclick="cancel()">取消</button>
                                <button type="button" class="btn btn-info" style="margin-left: 3%" onclick="saveProject()">保存
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('script')
    <script type="text/javascript" src="{{asset('/js/myjs.js')}}"></script>
    <script>
        function saveProject() {
            var name = $("#name").val();
            var desc = $("#desc").val();
            var url = $("#url").val();
            if (is_null(name)) {
                swal('请填写项目名称');
                return '';
            }
            if (is_null(url)) {
                swal('请填写获取本项目token值链接');
                return '';
            }
            $('#form_project_add').submit();
        }

        function cancel() {
            location.href = '/addoredit-project';
        }
    </script>

@endsection

