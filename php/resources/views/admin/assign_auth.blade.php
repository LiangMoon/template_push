@extends('admin.layouts.layout')
@section('content')
  <div class="content-wrapper" style="min-height: 901px;">
    <section class="content-header">
      <h3>权限分配<small>Assign&nbsp;Auth</small></h3>
      @include('admin.layouts._tip')
    </section>
    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">表单</h3>
            </div>
            <form class="form-horizontal" id="auth_form" action="/assign-auth" method="post">
              {{csrf_field()}}
              <div class="box-body">
                <div class="form-group">
                  <label for="manager" class="col-sm-2 control-label">用户</label>
                  <select class="form-control select2" style="width: 20%;" id="manager_id" name="manager_id" onchange="ownedAuth()">
                    <option value="">请选择用户</option>
                    @foreach($oUsers as $v)
                      <option value="{{$v->id}}">{{$v->name}}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group">
                  <label for="auth-list" class="col-sm-2 control-label">项目权限</label>
                  <div class="checkbox col-sm-10" style="padding-left: 0">
                    <label style="width: 33%;margin-bottom: 5px"><input type="checkbox" id="all">全选</label>
                    @foreach($oProjects as $v)
                      <label style="width: 33%;margin-bottom: 5px"><input type="checkbox" name="auth_id[]" value="{{$v->id}}">{{str_limit($v->name,36)}}</label>
                    @endforeach
                  </div>
                </div>
              </div>
              <div class="box-footer">
                <button type="button" class="btn btn-info" onclick="submitForm()" style="margin-left: 16%">确定</button>
                <input type="reset" class="btn btn-default" style="margin-left: 3%" value="取消">
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </div>
@stop

@section('script')
  <script src="{{asset('/adminlte/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
  <script>
    $(function () {
      $('.select2').select2();
    });

    $('#all').click(function () {
      $("input[name='auth_id[]']").prop('checked', $(this).prop('checked'));
    });

    function ownedAuth() {
      $("input[name='auth_id[]']").prop('checked', false);
      let managerId = $('#manager_id').val();
      $.get('/owned-auth',{'managerId':managerId},function (res) {
        if(res){
          let aAuthIds = res.split(',');
          $.each(aAuthIds, function(key, value) {
            $("input[name='auth_id[]'][value='"+value+"']").prop("checked", true);
          });
        }
      })
    }

    function submitForm() {
      let managerId = $('#manager_id').val();
      if(managerId === ""){
        swal('请选择用户');
        return;
      }
      $('#auth_form').submit();
    }
  </script>
@stop