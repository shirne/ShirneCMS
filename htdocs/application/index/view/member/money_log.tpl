<extend name="public:base" />
<block name="body">
    <div class="container">
        <div class="page-header">
            <div class="row">
            <h1 class="col-4">收益记录</h1>
            <div class="col-8 mt-3 text-right" >
                <div class="btn-group">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        积分类型：{$fields[$field]} <span class="caret"></span>
                    </button>
                    <div class="dropdown-menu">
                        <foreach name="fields" item="t" key="k">
                            <a class="dropdown-item"  href="{:url('index/member/moneylog',searchKey('field',$k))}">{$t}</a>
                        </foreach>
                    </div>
                </div>
                <div class="btn-group ml-3">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        记录类型：{$types[$type]} <span class="caret"></span>
                    </button>
                    <div class="dropdown-menu">
                        <foreach name="types" item="t" key="k">
                            <a class="dropdown-item" href="{:url('index/member/moneylog',array('type'=>$k))}">{$t}</a>
                        </foreach>
                    </div>
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
                    <div class="col-3 {$v['amount']>0?'text-success':'text-danger'}">{$v.field|money_type|raw}&nbsp;{$v.amount|showmoney}</div>
                    <div class="col-3">
                        <if condition="$v['from_member_id']">
                            [{$v['from_member_id']}]{$v['username']}
                            <else/>
                            -
                        </if>
                    </div>
                    <div class="col-3">{$v.create_time|showdate}</div>
                    <div class="col-3">{$v.reson}</div>
                    </div>
                </li>
            </foreach>
        </ul>
        {$page|raw}
    </div>
</block>