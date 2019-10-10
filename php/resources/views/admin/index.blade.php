@extends('admin.layouts.layout')

@section('content')
    <div class="content-wrapper" style="min-height: 901px;">
        <!-- 头部文字 -->
        <section class="content-header">
            <h3>
                推送
                <small>各种项目的推送均可以使用</small>
            </h3>
        </section>
        <!-- 主要内容 -->
        <section class="content">
            <div class="callout callout-info">
                <h4>Tip!</h4>
                <p>本系统用于多个项目发布模板消息使用</p>
            </div>
            @foreach($oAccounts as $k=>$v)
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title"><span style="font-weight: bold;">{{ "项目".($k+1) }}</span></h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                                    title="Collapse">
                                <i class="fa fa-minus"></i></button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip"
                                    title="Remove">
                                <i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body"><span style="font-weight: bold;">项目名称：</span>{{ $v->name }}</div>
                    <div class="box-body"><span style="font-weight: bold;">创建时间：</span>{{ $v->created_at }}</div>
                    <div class="box-footer"><span style="font-weight: bold;">使用说明：</span>{{ $v->desc }}</div>
                </div>
            @endforeach
        </section>
    </div>
@endsection



