<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark nav-box">
        <div class="container">
            <a class="navbar-brand float-left" href="/"><img src="__STATIC__/images/logo.png" </a>
            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#bs-navbar-collapse" aria-expanded="false">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="bs-navbar-collapse">
                <ul class="navbar-nav nav-fill main-nav">
                    <volist name="navigator" id="nav">
                        <if condition="empty($nav['subnav'])">
                            <li class="nav-item"><a class="nav-link" href="{$nav['url']}">{$nav['title']}</a></li>
                            <else/>
                            <li class="nav-item dropdown">
                                <a href="javascript:" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{$nav['title']} <span class="caret"></span></a>
                                <div class="dropdown-menu">
                                    <volist name="nav['subnav']" id="nav">
                                        <a class="dropdown-item" href="{$nav['url']}">{$nav['title']}</a>
                                    </volist>
                                </div>
                            </li>
                        </if>
                    </volist>
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
                                <a class="dropdown-item" href="{:url('member/logout')}"><i class="ion-log-out"></i> 退出登录</a>
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