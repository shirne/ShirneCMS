<extend name="public:base" />
<block name="body">
    <div class="container user-index">
        <div class="text-center user-header">
            <a href="javascript:">
                <img class="user-avatar img-circle" src="__STATIC__/images/avatar.png" width="60" >
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
            <a class="list-group-item" href="{:url('index/member/profile')}"><i class="fa fa-lock"></i> 个人资料</a>
            <a class="list-group-item" href="{:url('index/member/password')}"><i class="fa fa-lock"></i> 修改密码</a>
            <a class="list-group-item" href="{:url('index/member/cards')}"><i class="fa fa-credit-card"></i> 银行卡</a>
            <a class="list-group-item" href="{:url('index/member/address')}"><i class="fa fa-location-arrow"></i> 收货地址</a>
        </div>
        <div class="list-group">
            <a class="list-group-item" href="{:url('index/member/order')}"><i class="fa fa-file-text"></i> 我的订单</a>
            <a class="list-group-item" href="{:url('index/member/moneylog')}"><i class="fa fa-circle-o"></i> 积分记录</a>
            <a class="list-group-item" href="{:url('index/member/cashlist')}"><i class="fa fa-credit-card"></i> 提现记录</a>
            <a class="list-group-item" href="{:url('index/member/cash')}"><i class="fa fa-credit-card-alt"></i> 我要提现</a>
        </div>
        <if condition="$user['is_agent'] GT 0">
            <div class="list-group">
                <a class="list-group-item" href="{:url('index/member/team')}"><i class="fa fa-user"></i> 团队管理</a>
                <a class="list-group-item" href="{:url('index/member/shares')}"><i class="fa fa-share"></i> 推广二维码</a>
            </div>
        </if>
        <div class="list-group">
            <a class="list-group-item" href="{:url('index/member/feedback')}"><i class="fa fa-bug"></i> 反馈中心</a>
            <a class="list-group-item" href="{:url('index/member/notice')}"><i class="fa fa-bullhorn"></i> 系统公告</a>
            <a class="list-group-item" href="{:url('index/member/logout')}"><i class="fa fa-sign-out"></i> 退出登录</a>
        </div>
    </div>
</block>