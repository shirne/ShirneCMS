{extend name="public:base"/}

{block name="body"}
<div class="weui-flex userinfo">
    <div class="member_avatar"><img src="{$user.avatar|default='/static/images/avatar.png'}" /> </div>
    <div class="weui-flex__item member_name">{$user.username}<br />{$user.mobile}</div>
    <div class="member_balance">账号余额<br /><span class="text-muted">￥{$user.money|showmoney}</span></div>
</div>
<div class="weui-panel weui-panel_access orders">
    <div class="weui-panel__hd">
        <a href="{:aurl('index/member.order/index')}" class="float-right">所有订单&gt;</a>
        我的订单
    </div>
    <div class="weui-panel__bd view_menu">
        <div class="weui-flex">
            <a href="{:aurl('index/member.order/index',['status'=>1])}" class="weui-flex__item">
                <div class="item-icon"><img src="__STATIC__/icons/order_1.png" /> </div>
                <div class="item-text">待付款</div>
                {if $counts[0] > 0}<span class="counter">{$counts[0]}</span>{/if}
            </a>
            <a href="{:aurl('index/member.order/index',['status'=>2])}" class="weui-flex__item">
                <div class="item-icon"><img src="__STATIC__/icons/order_2.png" /> </div>
                <div class="item-text">待发货</div>
                {if $counts[1] > 0}<span class="counter">{$counts[1]}</span>{/if}
            </a>
            <a href="{:aurl('index/member.order/index',['status'=>3])}" class="weui-flex__item">
                <div class="item-icon"><img src="__STATIC__/icons/order_3.png" /> </div>
                <div class="item-text">待收货</div>
                {if $counts[2] > 0}<span class="counter">{$counts[2]}</span>{/if}
            </a>
            <a href="{:aurl('index/member.order/index',['status'=>4])}" class="weui-flex__item">
                <div class="item-icon"><img src="__STATIC__/icons/order_5.png" /> </div>
                <div class="item-text">待评价</div>
                {if $counts[3] > 0}<span class="counter">{$counts[3]}</span>{/if}
            </a>
        </div>
    </div>
</div>
<div class="weui-cells member-menus">
    <a class="weui-cell weui-cell_access" href="{:aurl('index/member/install')}">
        <div class="weui-cell__hd"><img src="__STATIC__/icons/menu_time.png"></div>
        <div class="weui-cell__bd weui-cell_primary">
            <p>预约安装</p>
        </div>
        <span class="weui-cell__ft"></span>
    </a>
    <a class="weui-cell weui-cell_access" href="{:aurl('index/member/repair')}">
        <div class="weui-cell__hd"><img src="__STATIC__/icons/menu_repair.png"></div>
        <div class="weui-cell__bd weui-cell_primary">
            <p>维修服务</p>
        </div>
        <span class="weui-cell__ft"></span>
    </a>
    <a class="weui-cell weui-cell_access" href="{:aurl('index/member/attention')}">
        <div class="weui-cell__hd"><img src="__STATIC__/icons/menu_dingdan.png"></div>
        <div class="weui-cell__bd weui-cell_primary">
            <p>安装须知</p>
        </div>
        <span class="weui-cell__ft"></span>
    </a>
    <a class="weui-cell weui-cell_access" href="javascript:;">
        <div class="weui-cell__hd"><img src="__STATIC__/icons/menu_headphone.png"></div>
        <div class="weui-cell__bd weui-cell_primary">
            <p>售后服务</p>
        </div>
        <span class="weui-cell__ft"></span>
    </a>
    <a class="weui-cell weui-cell_access" href="{:aurl('index/member/password')}">
        <div class="weui-cell__hd"><img src="__STATIC__/icons/menu_secret.png"></div>
        <div class="weui-cell__bd weui-cell_primary">
            <p>修改密码</p>
        </div>
        <span class="weui-cell__ft"></span>
    </a>
    <a class="weui-cell weui-cell_access" href="{:aurl('index/member/logout')}">
        <div class="weui-cell__hd"><img src="__STATIC__/icons/menu_quit.png"></div>
        <div class="weui-cell__bd weui-cell_primary">
            <p>退出登录</p>
        </div>
        <span class="weui-cell__ft"></span>
    </a>
</div>
{/block}
{block name="script"}
<script type="text/javascript">

</script>
{/block}