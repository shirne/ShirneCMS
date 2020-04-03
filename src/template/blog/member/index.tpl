<extend name="public:base" />
<block name="body">
    <div class="container user-index">
        <div class="text-center user-header">
            <a href="javascript:">
                <img class="user-avatar img-circle" src="{$user.avatar|default='/static/images/avatar.png'}" width="60" >
            </a>
            <h4 class="user-name">{$user.username}<span class="badge badge-info">{$userLevel['level_name']}</span></h4>
        </div>
        <div class="list-group mt-3">
            <a class="list-group-item" href="{:aurl('index/member/profile')}"><i class="ion-md-person"></i> 个人资料</a>
            <a class="list-group-item" href="{:aurl('index/member/password')}"><i class="ion-md-lock"></i> 修改密码</a>
        </div>
        <div class="list-group mt-3">
            <a class="list-group-item" href="{:aurl('index/member/feedback')}"><i class="ion-md-text"></i> 反馈中心</a>
            <a class="list-group-item" href="{:aurl('index/member/notice')}"><i class="ion-md-notifications"></i> 系统公告</a>
            <a class="list-group-item" href="{:aurl('index/member/logout')}"><i class="ion-md-log-out"></i> 退出登录</a>
        </div>
    </div>
</block>