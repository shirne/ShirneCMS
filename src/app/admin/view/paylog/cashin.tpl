{extend name="public:base" /}

{block name="body"}

{include  file="public/bread" menu="paylog_cashin" title=""  /}

<div id="page-wrapper">

    <div class="row list-header">
        <div class="col-6">
            <div class="btn-group btn-group-sm" role="group" aria-label="Button group with nested dropdown">
                <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    导出订单
                </button>
                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                    <a class="dropdown-item" href="{:url('paylog/export',['order_ids'=>$orderids])}" target="_blank" >导出本页</a>
                    <a class="dropdown-item" href="{:url('paylog/export',['status'=>1])}" target="_blank">导出未处理</a>
                    <a class="dropdown-item" href="{:url('paylog/export',['status'=>$status,'key'=>base64_encode($key)])}" target="_blank">导出筛选结果</a>
                </div>
            </div>
            <span class="ml-3">总有效金额: {$total|showmoney}</span>
        </div>
        <div class="col-6">
            <form action="{:url('Paylog/cashin')}" method="post">
                <div class="form-row">
                    <div class="col form-group">
                        <select name="status" class="form-control form-control-sm">
                            <option value="">全部</option>
                            <option value="1"{$status==='1'?' selected':''}>已审核</option>
                            <option value="0"{$status==='0'?' selected':''}>未审核</option>
                        </select>
                    </div>
                    <div class="col form-group input-group input-group-sm">
                        <input type="text" class="form-control" value="{$keyword}" name="key" placeholder="输入名称搜索">
                        <div class="input-group-append">
                          <button class="btn btn-outline-secondary" type="submit"><i class="ion-md-search"></i></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <table class="table table-hover table-striped">
        <thead>
        <tr>
            <th width="50">编号</th>
            <th>会员</th>
            <th>提现金额</th>
            <th>应转款</th>
            <th>提现方式</th>
            <th>提现信息</th>
            <th>下单时间</th>
            <th>状态</th>
            <th width="160">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        {php}$empty=list_empty(9);{/php}
        {volist name="lists" id="v" empty="$empty"}
            <tr>
                <td>{$v.id}</td>
                <td>
                    <div class="media">
                        {if !empty($v['avatar'])}
                            <img src="{$v.avatar}" class="mr-2 rounded" width="30"/>
                        {/if}
                        <div class="media-body">
                            <h5 class="mt-0 mb-1" >
                                {if !empty($v['nickname'])}
                                    {$v.nickname}
                                    {else/}
                                    {$v.username}
                                {/if}
                            </h5>
                            <p>[{$v.member_id}]</p>
                        </div>
                    </div>
                </td>
                <td>{$v.amount|showmoney}<br /><span class="text-muted">手续费:{$v.cash_fee|showmoney}</span></td>
                <td>{$v.real_amount|showmoney}</td>
                <td>{$v.cashtype|showcashtype}</td>
                <td>
                    {if $v.cashtype EQ 'wechat'}
                        {$v.card_name}
                        {elseif $v.cashtype EQ 'alipay'/}
                        {$v.card_name}
                        {else/}
                        {$v.bank}<span class="text-muted"> / </span>{$v.bank_name}<br />
                        {$v.card_name}<span class="text-muted"> / </span>{$v.cardno|fmtCardno}
                    {/if}
                </td>
                <td>{$v.create_time|showdate='Y-m-d H:i:s'}</td>
                <td>{$v.status|audit_status|raw}</td>
                <td class="operations">
                    {if $v['status'] EQ 0}
                    <a class="btn btn-outline-success pay-confirm" title="确认" data-id="{$v.id}" data-amount="{$v.real_amount}" data-cashtype="{$v.cashtype}"  href="javascript:"><i class="ion-md-checkmark-circle"></i> </a>
                    <a class="btn btn-outline-danger link-confirm" title="无效" data-confirm="您真的确定要作废吗？\n此操作将取消申请并将全部金额退回到会员的提现账户" href="{:url('Paylog/cashdelete',array('id'=>$v['id']))}" ><i class="ion-md-trash"></i> </a>
                        {else/}
                        -
                    {/if}
                </td>
            </tr>
        {/volist}
        </tbody>
    </table>
    {$page|raw}
</div>

{/block}
{block name="script"}
    <script type="text/javascript">
        jQuery(function($){
            var cashtypes="{:implode(',',getSetting('cash_types'))}".split(',')
            $('.pay-confirm').click(function(e){
                e.preventDefault();
                e.stopPropagation();
                var  id=$(this).data('id'),
                cashtype=$(this).data('cashtype'),
                amount=$(this).data('amount');
                var url="{:url('Paylog/cashupdate',['id'=>'__ID__'])}".replace('__ID__',id)
                var paylist=[],paytype_list=[];
                var paytype_picked=function(paytype){
                    var loading=dialog.loading('正在处理')
                    $.ajax({
                        url:url,
                        dataType:'json',
                        type:'POST',
                        data:{
                           paytype: paytype
                        },
                        success:function(json){
                            loading.close()
                            if(json.code==1){
                                dialog.success(json.msg)
                                setTimeout(function(){
                                    location.reload()
                                },500)
                            }else{
                                dialog.error(json.msg)
                            }
                        },
                        error:function(){
                            loading.close()
                            dialog.error('服务器错误')
                        }
                    })
                }
                if(cashtype=='alipay'){
                    dialog.confirm('请确认款项已转入对应支付宝账户',function(){
                        paytype_picked('alipay')
                    })
                    return false;
                }else if(cashtype=='wechat'){
                    if(cashtypes.indexOf('wechat')>-1){
                        paylist.push('企业付款')
                        paytype_list.push('wechat')
                    }
                    if(amount<=20000){
                        if(cashtypes.indexOf('wechatpack')>-1){
                            paylist.push('微信红包')
                            paytype_list.push('wechatpack')
                        }
                        if(cashtypes.indexOf('wechatminipack')>-1){
                            paylist.push('小程序红包')
                            paytype_list.push('wechatminipack')
                        }
                    }
                }else{
                    if(cashtypes.indexOf('wechat')>-1){
                        paylist.push('企业付款')
                        paytype_list.push('wechat')
                        paylist.push('手动打款')
                        paytype_list.push('handle')
                    }else{
                        dialog.confirm('请确认款项已转入对应银行账户',function(){
                            paytype_picked('handle')
                        })
                        return false;
                    }
                }
                dialog.action(paylist,function(idx){
                    paytype_picked(paytype_list[idx]);
                    return true;
                },'请选择打款方式')
            })
        })
    </script>
{/block}