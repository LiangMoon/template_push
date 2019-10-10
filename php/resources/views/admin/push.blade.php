@extends('admin.layouts.layout')
@section('content')
    <div class="content-wrapper" style="min-height: 901px;">
        <section class="content-header">
            <h3>推送<small>Push</small></h3>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <form class="form-horizontal">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">表单</h3>
                            </div>
                            {{ csrf_field() }}
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="wxplatform" class="col-sm-2 control-label">微信平台</label>
                                    <select class="form-control select2" style="width: 30%;" id="wxplatform" name="wxplatform" onchange="getTemplateAll()">
                                        <option value="">请选择微信平台</option>
                                        @foreach($oAccounts as $oAccount)
                                            <option value="{{ $oAccount->url }}">{{str_limit($oAccount->name,42)}}</option>
                                        @endforeach
                                    </select>
                                    {{--<button type="button" class="control-label btn btn-primary"
                                            onclick="getTemplateAll()">
                                        获取模板消息列表
                                    </button>--}}
                                </div>
                                <div class="form-group">
                                    <label for="wxplatform" class="col-sm-2 control-label">模板消息</label>
                                    <select class="col-sm-5 form-control select2" style="width: 30%;" id="template_id"
                                            name="template_id" onchange="chooseTemplate();">
                                        <option selected="selected" value="-1">请选择平台使用模板</option>
                                    </select>
                                </div>
                                <div id="template_info" style="display:none;">
                                    <div class="form-group">
                                        <label for="person" class="col-sm-2 control-label">模板示例</label>
                                        <div class="col-sm-6" style="padding-left: 0">
                                            <pre id="template_example"></pre>
                                        </div>
                                    </div>
                                    <div id="template_edit_info" name="template_edit_info"></div>
                                    <div class="form-group">
                                        <label for="template_url" class="col-sm-2 control-label">模板链接</label>
                                        <div class="col-sm-6" style="padding-left: 0">
                                            <input class="form-control" id="template_url" name="template_url" placeholder="模板消息url"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="wxplatform" class="col-sm-2 control-label">推送类型</label>
                                    <select class="col-sm-5 form-control select2" style="width: 30%;"
                                            name="push_type" onchange="pushType();">
                                        <option selected="selected" value="-1">请选择推送类型</option>
                                        <option value="0">测试推送</option>
                                        <option value="1">正式推送</option>
                                    </select>
                                </div>
                                {{-- 正式推送 --}}
                                <div id="real_push" style="display: none">
                                    <div class="form-group">
                                        <label for="file" class="col-sm-2 control-label">上传</label>
                                        <div class="col-sm-10">
                                            <input type="file" id="excel-upload" class="btn btn-primary"
                                                   name="excel-upload" onchange="saveExcel('excel_file')">
                                            <input type="hidden" id="excel_file" name="excel_file" />
                                            <p class="help-block"><span style="color: deeppink">请上传标准版模板格式文件</span></p>
                                            {{--<button type="button" onclick="removeExcel('excel_file')"
                                                    class="btn btn-block btn-danger" style="width:24%">移除excel文件
                                            </button>--}}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="load" class="col-sm-2 control-label">模板下载</label>
                                        <div class="col-sm-10">
                                            <a href="/assets/1234.xlsx" class="btn btn-primary">点击下载模板文件 >></a>
                                        </div>
                                    </div>
                                </div>
                                {{-- 测试推送 --}}
                                <div class="form-group" id="test_push" style="display: none">
                                    <label class="col-sm-2 control-label">测试的openid</label>
                                    <div class="col-sm-6" style="padding-left: 0">
                                        <input class="form-control" name="test_openid" type="text" />
                                        （<span style="color: red">*</span>可填写一个或多个openid，多个openid之间以英文逗号分隔，不要有空格或换行）
                                    </div>
                                </div>
                                {{-- 下载推送结果 --}}
                                <div class="form-group" style="display: none" id="downloadRecords">
                                    <label for="load" class="col-sm-2 control-label">推送结果</label>
                                    <div class="col-sm-10">
                                        <a href="#" class="btn btn-primary">点击下载本次推送结果 >></a>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <button type="button" class="btn btn-info" onclick="sendMessage()" style="margin-left: 16%">发送</button>
                                <button type="button" class="btn btn-default" onclick="cancelSend()" style="margin-left: 3%">取消</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('script')
    <script src="http://libs.baidu.com/jquery/1.9.0/jquery.min.js"></script>
    <script src="{{asset('/adminlte/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/js/myjs.js')}}"></script>
    {{-- excel文件上传 --}}
    <script type="text/javascript" src="{{asset('/js/excelfileupload.js')}}"></script>
    {{--<script type="text/javascript" src="{{asset('/js/uploadExcel.js')}}"></script>--}}
    <script>
        $(function () {
            $('.select2').select2();
        });
        var data = {
            '_token': $('input[name=_token]').val()
        };
        {{-- 获取模板消息--}}
        function getTemplateAll() {
            var sUrl = $("#wxplatform option:selected").val();
            if (is_null(sUrl)) {
                swal('请选择公众号平台');
                return '';
            }
            swal('正在加载中，请稍后。。。');
            var token = $('input[name="_token"]').val();
            var data = {_token: token, url: sUrl};
            $.post("{{ route('template.all') }}", data,
                function (res) {
                    if (res.success) {
                        $('#template_id').html('');
                        var html = '<option selected="selected" value="-1">请选择平台使用模板</option>';
                        for (var elem in res.data) {
                            html += '<option value="' + res.data[elem]['template_id'] + '" data-content="' + res.data[elem]['content'] + '"data-tidycontent="' + res.data[elem]['tidycontent'] + '">' + res.data[elem]['title'] + '</option>';
                        }
                        $('#template_id').html(html);
                        $('#template_example').html('');
                        swal('获取成功');
                    } else {
                        swal('获取失败');
                    }
                })
        }

        function chooseTemplate() {
            var templateid = $('#template_id option:selected').val();
            var content = $('#template_id option:selected').data('content');
            var tidycontent = $('#template_id option:selected').data('tidycontent');
            if(templateid == '-1'){
              $('#template_info').hide();
            }else{
              var arr = tidycontent.split(",");
              var html = '';
              for (var i = 0; i < arr.length; i++) {
                html += '<div class="form-group">' +
                  '<label for="template_edit_info" class="col-sm-2 control-label">' + eval("arr[i]") + '</label>' +
                  '<div class="col-sm-6" style="padding-left: 0">' +
                  '<input class="form-control template-info" type="text" name="template_info[]">' +
                  '</div>' +
                  '</div>';
              }
              $('#template_example').html(content);
              $('#template_edit_info').html(html);
              $('#template_info').show();
            }
        }

        function pushType() {
            var pushType = $('select[name="push_type"]').val();
            if(pushType === '0'){
                $('#real_push').hide();
                $('#test_push').show();
            }else if(pushType === '1'){
                $('#test_push').hide();
                $('#real_push').show();
            }
        }
    </script>
    <script>
        function saveExcel(id) {
            $('#downloadRecords').hide();
            $.ajaxFileUpload({
                url: '/upload-excel',
                type: 'post',
                secureuri: false,
                fileElementId: 'excel-upload',//文件上传空间的id属性
                dataType: 'json',//返回值类型 :一般设置为json
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                data: data,
                success: function (data, status) {
                    if (data.success) {
                        $('#excel_file').val(data.data['path']);
                        swal(data.data['file_name'] + '上传成功！');
                    } else {
                        swal(data.msg);
                    }
                }
            })
        }

        {{--模板消息推送--}}
        function sendMessage() {
            var pushType = $('select[name="push_type"]').val();
            var project_url = $("#wxplatform option:selected").val();
            var template_id = $("#template_id").val();
            //var flag = true;
            //去掉非空校验
            /*$(".template-info").each(function (k,item) {
                if (is_null($(item).val())){
                    swal('请填写模板内容');
                    flag = false;
                    return false;
                }
            });*/
            //if (!flag) return false;

            if (!template_id || template_id == '-1') {
                swal("请选择您想要推送的模板");
                return false;
            }
            if(pushType === '0'){  //测试推送
                var testOpenid = $('input[name="test_openid"]').val();
                if($.trim(testOpenid) == ''){
                    swal('请填写测试的openid');
                    return false;
                }
                var data = $('form').serialize();
                $.post('/test-send-template', data, function (msg) {
                  if (msg.success) {
                    swal(msg.success_info);
                    $('#downloadRecords').show();
                    $('#downloadRecords a').attr('href',"{{url('download-records')}}"+"?url="+encodeURI(project_url)+"&push_type="+pushType);
                  } else {
                    swal(msg.error_info);
                  }
                }, 'json');
            }else if(pushType === '1'){  //正式推送
                var excel_file = $('#excel_file').val();
                if (!excel_file) {
                  swal('您尚未上传csv文件');
                  return false;
                } else if (confirm("您确定要推送吗？")) {
                  swal('正在推送，请耐心等候');
                  $('#excel_file').val(excel_file.replace(/[/]/g, '_'));
                  var data = $('form').serialize();
                  $.post('/send-template', data, function (msg) {
                    if (msg.success) {
                      swal(msg.success_info);
                      $('#downloadRecords').show();
                      $('#downloadRecords a').attr('href',"{{url('download-records')}}"+"?url="+encodeURI(project_url)+"&push_type="+pushType);
                    } else {
                      swal(msg.error_info);
                    }
                  }, 'json');
                }
            }
        }

        function cancelSend() {
            window.location.href = '/push-page';
        }
    </script>
@endsection
