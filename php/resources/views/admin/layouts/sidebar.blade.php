<aside class="main-sidebar">
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ asset('/adminlte/dist/img/logoadd.jpg') }}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{ isset(Auth::user()->name) ? Auth::user()->name : '' }}</p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- 检索表单-->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="搜索">
                <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>

        <!-- 侧边栏主菜单-->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">主菜单导航</li>
            <li>
                <a href="/home">
                    <i class="fa fa-home"></i> <span>后台首页</span>
                    <span class="pull-right-container"></span>
                </a>
            </li>
            {{--<li>
                <a href="/home">
                    <i class="fa fa-user"></i> <span>管理员</span>
                    <span class="pull-right-container">
            </span>
                </a>
            </li>--}}
            <li>
                <a href="/project">
                    <i class="fa fa-tv"></i> <span>项目管理</span>
                    <span class="pull-right-container"></span>
                </a>
            </li>
            <li>
                <a href="/push-page">
                    <i class="fa fa-commenting-o"></i> <span>模板推送</span>
                    <span class="pull-right-container"></span>
                </a>
            </li>
            @if(Auth::user()->identity == 1)
                <li>
                    <a href="/assign-auth">
                        <i class="fa fa-check-square-o"></i> <span>权限分配</span>
                        <span class="pull-right-container"></span>
                    </a>
                </li>
            @endif
        </ul>
    </section>
</aside>
