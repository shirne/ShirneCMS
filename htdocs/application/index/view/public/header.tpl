<header>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark nav-box">
        <div class="container">
            <a class="navbar-brand float-left" href="/"><img src="__STATIC__/images/logo.png" </a>
            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#bs-navbar-collapse" aria-expanded="false">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="bs-navbar-collapse">
                <ul class="navbar-nav nav-fill main-nav">
                    <li class="nav-item active"><a class="nav-link" href="{:url('index/index')}">首页<span class="sr-only">(current)</span></a></li>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">服务范围 <span class="caret"></span></a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{:url('Article/index')}">云平台网站服务</a>
                            <a class="dropdown-item" href="{:url('Article/index')}">微信平台服务/小程序</a>
                            <a class="dropdown-item" href="{:url('Article/index')}">企业APP开发</a>
                            <a class="dropdown-item" href="{:url('Article/index')}">域名注册/企业邮箱</a>
                            <a class="dropdown-item" href="{:url('Article/index')}">电子商务平台</a>
                            <a class="dropdown-item" href="{:url('Article/index')}">云服务器/云主机</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">解决方案 <span class="caret"></span></a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{:url('Article/index')}">企业网站解决方案</a>
                            <a class="dropdown-item" href="{:url('Article/index')}">手机端网站解决方案</a>
                            <a class="dropdown-item" href="{:url('Article/index')}">云服务解决方案</a>
                            <a class="dropdown-item" href="{:url('Article/index')}">微信商城系统解决方案</a>
                        </div>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{:url('Article/index',array('name'=>''))}">案例中心</a></li>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">关于原设 <span class="caret"></span></a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{:url('Article/index')}">公司简介</a>
                            <a class="dropdown-item" href="{:url('Article/index')}">企业文化</a>
                            <a class="dropdown-item" href="{:url('Article/index')}">发展历程</a>
                            <a class="dropdown-item" href="{:url('Article/index')}">联系我们</a>
                        </div>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{:url('Article/index',array('name'=>'news'))}">新闻动态</a></li>
                    <li class="nav-item"><a class="nav-link" href="http://cloud.shirne.cn" target="_blank">云计算</a></li>
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