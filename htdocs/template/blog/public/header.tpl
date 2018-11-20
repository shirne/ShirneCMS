<header>

    <div class="container">
    <nav class="navbar justify-content-between navbar-expand-lg nav-box">
            <a class="navbar-brand float-left" href="/">临风小筑</a>
            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#bs-navbar-collapse" aria-expanded="false">
                <span class="ion-md-menu navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="bs-navbar-collapse">
                <ul class="navbar-nav main-nav">
                    <volist name="navigator" id="nav">
                        <if condition="empty($nav['subnav'])">
                            <li class="nav-item" data-model="{$nav['model']}"><a class="nav-link" href="{$nav['url']}" target="{$nav['target']}">{$nav['title']}</a></li>
                            <else/>
                            <li class="nav-item dropdown" data-model="{$nav['model']}">
                                <a href="{$nav['url']}" target="{$nav['target']}" class="nav-link dropdown-toggle" role="button" aria-haspopup="true" aria-expanded="false">{$nav['title']} <span class="caret"></span></a>
                                <div class="dropdown-menu">
                                    <volist name="nav['subnav']" id="nav">
                                        <a class="dropdown-item" target="{$nav['target']}" href="{$nav['url']}">{$nav['title']}</a>
                                    </volist>
                                </div>
                            </li>
                        </if>
                    </volist>
                    <if condition="$isLogin">
                        <li class="nav-item dropdown">
                            <a href="javascript:" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">会员中心 <span class="caret"></span></a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{:url('index/member/index')}">个人中心</a>
                                <a class="dropdown-item" href="{:url('index/member/profile')}">修改资料</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{:url('index/member/logout')}"><i class="ion-md-log-out"></i> 退出登录</a>
                            </div>
                        </li>
                        <else/>
                        <li class="nav-item"><a class="nav-link" href="{:url('index/login/index')}">{:lang('Sign in')}</a></li>
                        <li class="nav-item"><a class="nav-link" href="{:url('index/login/register')}">{:lang('Sign up')}</a></li>
                    </if>
                </ul>
            </div>
    </nav>

    </div>
</header>