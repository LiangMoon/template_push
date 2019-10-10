<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

@include('admin.layouts.head')

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    {{-- 头部--}}
    @include('admin.layouts.header')

    {{--侧边栏--}}
    @include('admin.layouts.sidebar')

    {{-- 主题内容--}}
    @yield('content')

    {{--底部--}}
    @include('admin.layouts.footer')

    {{-- 控制侧边栏--}}
    @include('admin.layouts.operate_sidebar')

    <div class="control-sidebar-bg"></div>

</div>
{{-- adminlte使用的js--}}
@include('admin.layouts.adminltejs')

{{-- 项目自定义js--}}
@yield('script')

</body>
</html>
