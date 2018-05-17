<extend name="public:base" />
<block name="body">
    <div class="main-content">
        <div class="row page-header">
            <h1 class="col-xs-4">收益记录</h1>
            <div class="col-xs-8" style="text-align: right;">
                <div class="btn-group">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {$types[$type]['name']} <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <foreach name="types" item="t" key="k">
                            <li><a href="{:url('index/member/moneylog',array('type'=>$k))}">{$t['name']}</a></li>
                        </foreach>
                    </ul>
                </div>
            </div>
        </div>
        <ul class="list-group">
            <li class="list-group-item row">
                <div class="col-xs-3">积分</div>
                <div class="col-xs-3">来源</div>
                <div class="col-xs-3">时间</div>
                <div class="col-xs-3">备注</div>
            </li>
            <foreach name="logs" item="v">
                <li class="list-group-item row">
                    <div class="col-xs-3 {$v['amount']>0?'increrow':'decrerow'}">{$v.amount|showmoney}</div>
                    <div class="col-xs-3">
                        <if condition="$v['from_member_id']">
                            [{$v['from_member_id']}]{$v['username']}
                            <else/>
                            -
                        </if>
                    </div>
                    <div class="col-xs-3">{$v.create_at|showdate}</div>
                    <div class="col-xs-3">{$v.reson}</div>
                </li>
            </foreach>
        </ul>
        {$page}
    </div>
</block>