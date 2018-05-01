<header>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">Brand</a>
            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active"><a class="nav-link" href="{:url('index/index')}">首页<span class="sr-only">(current)</span></a></li>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">菜单 <span class="caret"></span></a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{:url('Post/index')}">文章列表</a>
                            <div role="separator" class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{:url('Page/index')}">单页</a>
                        </div>
                    </li>
                </ul>
                <ul class="navbar-nav justify-content-end">

                    <if condition="$isLogin">
                        <li class="nav-item dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">会员中心 <span class="caret"></span></a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#">Action</a>
                                <a class="dropdown-item" href="#">Another action</a>
                                <a class="dropdown-item" href="#">Something else here</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{:url('member/logout')}"><i class="fa fa-power-off"></i> 退出登录</a>
                            </div>
                        </li>
                        <else/>
                        <li class="nav-item"><a class="nav-link" href="{:url('login/index')}">登录</a></li>
                        <li class="nav-item"><a class="nav-link" href="{:url('login/register')}">注册</a></li>
                    </if>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>

</header>