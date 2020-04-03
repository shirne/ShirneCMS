{extend name="public:base" /}
{block name="body"}
    <div class="container user-index">
        <div class="text-center user-header">
            <a href="javascript:">
                <img class="user-avatar img-circle" src="{$user.avatar|default='/static/images/avatar.png'}" width="60" >
            </a>
            <h4 class="user-name">{$user.username}<span class="badge badge-info">{$userLevel['level_name']}</span></h4>
            <div class="row inforow">
                <div class="col-3"><p>消费积分</p><p>￥{$user.money|showmoney}</p></div>
                <div class="col-3"><p>已提现</p><p>￥{$totalCash|showmoney}</p></div>
                <div class="col-3"><p>总收益</p><p>￥{$totalAward|showmoney}</p></div>
                <div class="col-3"><p>现金积分</p><p>￥{$user.credit|showmoney}</p></div>
            </div>
        </div>
        <div class="list-group">
            <a class="list-group-item" href="{:aurl('index/member/profile')}"><i class="ion-md-person"></i> 个人资料</a>
            <a class="list-group-item" href="{:aurl('index/member/password')}"><i class="ion-md-lock"></i> 修改密码</a>
            <a class="list-group-item" href="{:aurl('index/member.account/cards')}"><i class="ion-md-card"></i> 银行卡</a>
            <a class="list-group-item" href="{:aurl('index/member.address')}"><i class="ion-md-map"></i> 收货地址</a>
        </div>
        <div class="list-group">
            <a class="list-group-item" href="{:aurl('index/member.order/index')}"><i class="ion-md-list"></i> 我的订单</a>
            <a class="list-group-item" href="{:aurl('index/member.account/moneylog')}"><i class="ion-md-paper"></i> 积分记录</a>
            <a class="list-group-item" href="{:aurl('index/member.account/cashList')}"><i class="ion-md-reorder"></i> 提现记录</a>
            <a class="list-group-item" href="{:aurl('index/member.account/cash')}"><i class="ion-md-cash"></i> 我要提现</a>
            <a class="list-group-item" href="{:aurl('index/member.account/rechargeList')}"><i class="ion-md-reorder"></i> 充值记录</a>
            <a class="list-group-item" href="{:aurl('index/member.account/recharge')}"><i class="ion-md-cash"></i> 我要充值</a>
        </div>
        {if $user['is_agent'] GT 0}
            <div class="list-group">
                <a class="list-group-item" href="{:aurl('index/member.agent/team')}"><i class="ion-md-people"></i> 团队管理</a>
                <a class="list-group-item" href="{:aurl('index/member.agent/shares')}"><i class="ion-md-share"></i> 推广二维码</a>
            </div>
        {/if}
        <div class="list-group">
            <a class="list-group-item" href="{:aurl('index/member/feedback')}"><i class="ion-md-text"></i> 反馈中心</a>
            <a class="list-group-item" href="{:aurl('index/member/notice')}"><i class="ion-md-notifications"></i> 系统公告</a>
            <a class="list-group-item" href="{:aurl('index/member/logout')}"><i class="ion-md-log-out"></i> 退出登录</a>
        </div>
    </div>
{/block}