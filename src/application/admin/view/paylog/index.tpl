{extend name="public:base" /}

{block name="body"}
    {include file="public/bread" menu="paylog_index" title="" /}

    <div id="page-wrapper">
        <div class="row list-header">
            <div class="col-md-12">
                <form action="{:url('paylog/index',searchKey('fromdate,todate',''))}" class="form-inline" method="post">
                    <div class="btn-group">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                           订单类型 {$orderTypes[$ordertype]} <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu">
                            {foreach $orderTypes as $k => $t}
                                <a class="dropdown-item" href="{:url('index',searchKey('ordertype',$k))}">{$t}</a>
                            {/foreach}
                        </div>
                    </div>
                    <div class="input-group date-range ml-3">
                        <div class="input-group-prepend"><span class="input-group-text">时间范围</span></div>
                        <input type="text" class="form-control" name="fromdate" value="{$fromdate}">
                        <div class="input-group-middle"><span class="input-group-text">-</span></div>
                        <input type="text" class="form-control" name="todate" value="{$todate}">
                        <div class="input-group-append">
                          <button class="btn btn-outline-dark" type="submit"><i class="ion-md-search"></i></button>
                        </div>
                    </div>
                    {if $id}
                        <div class="btn-group ml-3">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">会员: {$member.username}<span class="caret"></span>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{:url('index',searchKey('id',0))}">不限会员</a>
                            </div>
                        </div>
                    {/if}
                    <div class="btn-group ml-3">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                           支付类型: {$payTypes[$type]} <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu">
                            {foreach $payTypes as $k => $t}
                                <a class="dropdown-item" href="{:url('index',searchKey('type',$k))}">{$t}</a>
                            {/foreach}
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <table class="table table-hover table-bordered">
            <thead>
            <tr>
                <th class="text-center">\</th>
                {foreach $orderTypes as $k => $t}
                    {if $k != 'all'}
                    <th>{$t}</th>
                    {/if}
                {/foreach}
                <th>合计</th>
            </tr>
            <tbody>
                {foreach $payTypes as $fk => $f}
                    {if $fk != 'all'}
                    <tr>
                        <th>{$f}</th>
                        {foreach $orderTypes as $tk => $t}
                            {if $tk != 'all'}
                            <td>{$statics[$fk][$tk]|showmoney}</td>
                            {/if}
                        {/foreach}
                        <td>{$statics[$fk]['sum']|showmoney}</td>
                    </tr>
                    {/if}
                {/foreach}
            </tbody>
            </thead>
        </table>
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <th width="50">#</th>
                <th>支付单号</th>
                <th>用户名</th>
                <th>金额</th>
                <th>支付渠道</th>
                <th>订单</th>
                <th>时间</th>
                <th>退款</th>
                <th width="70"></th>
            </tr>
            </thead>
            <tbody>
            {empty name="lists"}{:list_empty(9)}{/empty}
            {foreach $logs as $key => $v}
                <tr>
                    <td>{$v.id}</td>
                    <td>{$v.order_no}</td>
                    <td>
                        {if $v['member_id']}
                            <a href="{:url('index',array('id'=>$v['member_id'],'fromdate'=>$fromdate,'todate'=>$todate,'from_id'=>$from_id))}" class="media">
                                {if !empty($v['avatar'])}
                                    <img src="{$v.avatar}" class="mr-2 rounded" width="30"/>
                                {/if}
                                <div class="media-body">
                                    <h5 class="mt-0 mb-1" style="font-size:13px;">
                                        {if !empty($v['nickname'])}
                                            {$v.nickname}
                                        {else/}
                                            {$v.username}
                                        {/if}
                                    </h5>
                                    <div style="font-size:12px;">
                                        [{$v.member_id} {$levels[$v['level_id']]['level_name']}]
                                    </div>
                                </div>
                            </a>
                            {else/}
                            -
                        {/if}
                    </td>
                    <td class="text-success">￥{$v.pay_amount|showmoney}</td>
                    <td>{$payTypes[$v['pay_type']]}</td>
                    <td><a href="{:url($orderDetails[$v['order_type']],['id'=>$v['order_id']])}" target="_blank">{$orderTypes[$v['order_type']]}</a></td>
                    <td>{$v.create_time|showdate}</td>
                    <td>{if $v['is_refund'] > 0}
                        <span class="badge badge-warning">{$v['is_refund']}次 共￥{$v['refund_fee']}</span>
                    {else/}
                        <span class="badge badge-secondary">无</span>
                    {/if}</td>
                    <td>

                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
        {$page|raw}
    </div>

{/block}