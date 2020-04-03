<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark nav-box">
        <div class="container">
            <a class="navbar-brand float-left" href="/"><img src="__STATIC__/images/logo.png" /></a>
            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#bs-navbar-collapse" aria-expanded="false">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="bs-navbar-collapse">
                <ul class="navbar-nav nav-fill main-nav">
                    {volist name="navigator" id="nav"}
                        {if empty($nav['subnav'])}
                            <li class="nav-item" data-model="{$nav['model']}"><a class="nav-link" href="{$nav['url']}" target="{$nav['target']}">{$nav['title']}</a></li>
                            {else/}
                            <li class="nav-item dropdown" data-model="{$nav['model']}">
                                <a href="javascript:" target="{$nav['target']}" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{$nav['title']} <span class="caret"></span></a>
                                <div class="dropdown-menu">
                                    {volist name="nav['subnav']" id="nav"}
                                        <a class="dropdown-item" target="{$nav['target']}" href="{$nav['url']}">{$nav['title']}</a>
                                    {/volist}
                                </div>
                            </li>
                        {/if}
                    {/volist}
                </ul>
                <ul class="navbar-nav justify-content-end">

                    {if $isLogin}
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">会员中心 <span class="caret"></span></a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{:aurl('index/member/index')}">个人中心</a>
                                <a class="dropdown-item" href="{:aurl('index/member/profile')}">修改资料</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{:aurl('index/member/logout')}"><i class="ion-md-log-out"></i> 退出登录</a>
                            </div>
                        </li>
                        {else/}
                        <li class="nav-item"><a class="nav-link" href="{:url('index/login/index')}">{:lang('Sign in')}</a></li>
                        <li class="nav-item"><a class="nav-link" href="{:url('index/login/register')}">{:lang('Sign up')}</a></li>
                    {/if}
                </ul>
            </div>
        </div>
    </nav>

</header>