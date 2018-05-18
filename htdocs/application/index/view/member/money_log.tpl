<extend name="public:base" />
<block name="body">
    <div class="container">
        <div class="page-header">
            <div class="row">
            <h1 class="col-4">收益记录</h1>
            <div class="col-8 mt-3 text-right" >
                <div class="btn-group">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {$types[$type]['name']} <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <foreach name="types" item="t" key="k">
                            <li><a href="{:url('index/member/moneylog',array('type'=>$k))}">{$t}</a></li>
                        </foreach>
                    </ul>
                </div>
            </div>
            </div>
        </div>
        <ul class="list-group">
            <li class="list-group-item">
                <div class="row">
                <div class="col-3">积分</div>
                <div class="col-3">来源</div>
                <div class="col-3">时间</div>
                <div class="col-3">备注</div>
                </div>
            </li>
            <php>$empty='<span class="col-12 empty">暂时没有记录</span>';</php>
            <foreach name="logs" empty="$empty" item="v">
                <li class="list-group-item">
                    <div class="row">
                    <div class="col-3 {$v['amount']>0?'increrow':'decrerow'}">{$v['field']=='money'?'<span class="badge badge-success">消费积分</span>':'<span class="badge badge-info">现金积分</span>'}&nbsp;{$v.amount|showmoney}</div>
                    <div class="col-3">
                        <if condition="$v['from_member_id']">
                            [{$v['from_member_id']}]{$v['username']}
                            <else/>
                            -
                        </if>
                    </div>
                    <div class="col-3">{$v.create_at|showdate}</div>
                    <div class="col-3">{$v.reson}</div>
                    </div>
                </li>
            </foreach>
        </ul>
        {$page}
    </div>
</block>