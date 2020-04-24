<extend name="public:base" />

<block name="body">
<include file="public/bread" menu="member_index" title="会员管理"/>

<div id="page-wrapper">
    <div class="row list-header">
        <div class="col col-5">
            <div class="btn-toolbar list-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                <div class="btn-group btn-group-sm mr-2" role="group" aria-label="check action group">
                    <a href="javascript:" class="btn btn-outline-secondary checkall-btn" data-toggle="button" aria-pressed="false">全选</a>
                    <a href="javascript:" class="btn btn-outline-secondary checkreverse-btn">反选</a>
                </div>
                <div class="btn-group btn-group-sm mr-2" role="group" aria-label="action button group">
                    <a href="javascript:" class="btn btn-outline-secondary action-btn" data-action="enable">启用</a>
                    <a href="javascript:" class="btn btn-outline-secondary action-btn" data-action="disable">禁用</a>
                </div>
                <a href="{:url('member/statics')}" class="btn btn-outline-info btn-sm mr-2"><i class="ion-md-stats"></i> 会员统计</a>
                <a href="{:url('member/add')}" class="btn btn-outline-primary btn-sm mr-2"><i class="ion-md-add"></i> 添加会员</a>
                <a href="javascript:" class="btn btn-outline-warning btn-sm action-btn" data-need-checks="false" data-action="setIncrement"><i class="ion-md-add"></i> 设置起始ID</a>
            </div>
        </div>
        <div class="col col-7">
            <form action="{:url('member/index')}" method="post">
                <div class="form-row">
                    <div class="form-group col input-group input-group-sm">
                        <div class="input-group-prepend">
                        <span class="input-group-text">上级</span>
                        </div>
                        <input type="text" class="form-control" name="referer" placeholder="填写id或会员名" value="{$referer}">
                    </div>
                    <div class="form-group col input-group input-group-sm date-range">
                        <div class="input-group-prepend">
                            <span class="input-group-text">注册时间</span>
                        </div>
                        <input type="text" class="form-control fromdate" name="start_date" placeholder="选择开始日期" value="{$start_date}">
                        <div class="input-group-middle"><span class="input-group-text">-</span></div>
                        <input type="text" class="form-control todate" name="end_date" placeholder="选择结束日期" value="{$end_date}">
                    </div>
                    <div class="form-group col input-group input-group-sm">
                        <div class="input-group-prepend">
                        <span class="input-group-text">关键字</span>
                        </div>
                        <input type="text" class="form-control" value="{$keyword}" name="keyword" placeholder="输入用户名或者邮箱关键词搜索">
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
                <th>用户名</th>
                <th>手机/邮箱</th>
                <th>余额</th>
                <th>推荐人</th>
                <th width="120">代理</th>
                <th width="120">级别</th>
                <th width="200">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        <php>$empty=list_empty(10);</php>
        <volist name="lists" id="v" empty="$empty">
            <tr>
                <td><input type="checkbox" name="id" value="{$v.id}" /></td>
                <td>
                    <div class="media">
                        <if condition="!empty($v['avatar'])">
                            <img src="{$v.avatar}" class="mr-2 rounded" width="60"/>
                        </if>
                        <div class="media-body">
                            <h5 class="text-nowrap mt-0 mb-1">
                                [{$v.id}]
                                <if condition="!empty($v['nickname'])">
                                    {$v.nickname}
                                    <else/>
                                    {$v.username}
                                </if>
                                <span class="badge badge-{$typestyles[$v['type']]}">{$types[$v['type']]}</span>
                                <if condition="$v.status eq 1"><else/><span class="badge badge-danger" >禁用</span></if>
                            </h5>
                            <div style="font-size:12px;">
                            <if condition="!empty($v['realname'])">真实姓名：{$v.realname}</if>
                            注册时间：{$v.create_time|showdate}<br/>
                                上次登陆：{$v.logintime|showdate} <if condition="!empty($v['login_ip'])">[{$v.login_ip}]</if>
                            </div>
                        </div>
                    </div>
                </td>
                <td>{$v.mobile}<br />{$v.email}</td>
                <td>
                    <foreach name="moneyTypes" item="mt" key="k">
                        <div class="input-group input-group-sm mb-2">
								<span class="input-group-prepend">
									<span class="input-group-text">{$mt}</span>
								</span>
                            <span class="form-control">{$v[$k]|showmoney}</span>
                        </div>
                    </foreach>
                </td>
                <td>
                    <empty name="v.refer_name">
                        <a href="javascript:" data-id="{$v.id}" class="bindreferer">设置</a>
                        <else/>
                        <div class="media">
                            <if condition="!empty($v['refer_avatar'])">
                                <img src="{$v.refer_avatar}" class="mr-2 rounded" width="30"/>
                            </if>
                            <div class="media-body">
                                <h5 class="mt-0 mb-1">
                                    [{$v.referer}]
                                    <if condition="!empty($v['refer_nickname'])">
                                        {$v.refer_nickname}
                                        <else/>
                                        {$v.refer_name}
                                    </if>
                                </h5>
                                <div>
                                    <a href="javascript:" data-id="{$v.id}" class="bindreferer">更换</a>
                                    <a href="javascript:" data-id="{$v.id}" class="delreferer">清除</a>
                                </div>
                            </div>
                        </div>
                    </empty>
                </td>
                <td class="operations text-left">
                    <if condition="$v.is_agent neq 0">
                        <a class="btn btn-outline-primary" title="查看下线" href="{:url('member/index',array('referer'=>$v['id']))}"><i class="ion-md-people"></i> </a>
                        <a class="btn btn-outline-danger link-confirm" data-confirm="取消代理不能更改已注册的用户!!!" title="取消代理" href="{:url('member/cancel_agent',array('id'=>$v['id']))}" ><i class="ion-md-log-out"></i> </a><br />
                        <a class="btn btn-sm pl-1 pr-1 pt-0 pb-0 mt-2 btn-{$agents[$v['is_agent']]['style']} btn-setagent" title="更改级别" data-id="{$v.id}" data-agent="{$v.is_agent}" href="{:url('member/set_agent',array('id'=>$v['id']))}" >{$agents[$v['is_agent']]['name']}&nbsp;<i class="ion-md-create"></i></a>
                    <else/>
                        <a class="btn btn-sm btn-outline-primary"  data-id="{$v.id}" data-agent="0" title="设置代理" href="{:url('member/set_agent',array('id'=>$v['id']))}" ><i class="ion-md-medal"></i> </a>
                    </if>
                </td>
                <td>
                    <a href="javascript:" class="btn btn-sm has-tooltip pl-1 pr-1 pt-0 pb-0 btn-{$levels[$v['level_id']]['style']} btn-setlevel" data-id="{$v.id}" data-level="{$v.level_id}" title="点击修改">{$levels[$v['level_id']]['level_name']}&nbsp;<i class="ion-md-create"></i></a>
                </td>
                <td class="operations">
                    <a class="btn btn-outline-primary" title="编辑" href="{:url('member/update',array('id'=>$v['id']))}"><i class="ion-md-create"></i> </a>
                    <a class="btn btn-outline-warning btn-recharge" title="充值" href="javascript:" data-id="{$v.id}" ><i class="ion-md-card"></i> </a>
                    <a class="btn btn-outline-primary" title="资金明细" href="{:url('member/money_log',array('id'=>$v['id']))}" ><i class="ion-md-paper"></i> </a>

                    <if condition="$v.status eq 1">
                        <a class="btn btn-outline-warning link-confirm" title="禁用" data-confirm="禁用后用户将不能登陆!\n请确认!!!" href="{:url('member/status',array('id'=>$v['id'],'type'=>0))}" ><i class="ion-md-remove-circle-outline"></i> </a>
                        <else/>
                        <a class="btn btn-outline-success link-confirm" title="启用" data-confirm="确认启用用户?" href="{:url('member/status',array('id'=>$v['id'],'type'=>1))}" style="color:#50AD1E;"><i class="ion-md-checkmark-circle"></i> </a>
                    </if>
                    <a class="btn btn-outline-danger link-confirm" title="删除" data-confirm="警告：删除会员将清除所有与会员相关的资料并且无法恢复!\n请确认!!!" href="{:url('member/delete',array('id'=>$v['id']))}" ><i class="ion-md-close"></i> </a>
                </td>
            </tr>
        </volist>
        </tbody>
    </table>
    {$page|raw}
</div>

</block>
<block name="script">
    <script type="text/html" id="rechargeTpl">
        <div class="row" style="margin:0 10%;">
            <div class="col-12 form-group">
                <div class="input-group">
                    <div ><span class="input-group-text">充值类型</span> </div>
                    <div class="col w-50 text-center" >
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <foreach name="moneyTypes" item="mt" key="k">
                        <label class="btn btn-outline-primary {$k=='money'?'active':''}"> <input type="radio" name="field" value="{$k}" autocomplete="off" {$k=='money'?'checked':''}> {$mt}</label>
                        </foreach>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 form-group"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">充值金额</span> </div><input type="text" name="amount" class="form-control" placeholder="请填写充值金额"/> </div></div>
            <div class="col-12 form-group"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">充值原因</span> </div><input type="text" name="reson" class="form-control" placeholder="请填写充值原因"/> </div> </div>
        </div>
    </script>
    <script type="text/plain" id="member_agent">
        {$agents|array_values|json_encode|raw}
    </script>
    <script type="text/plain" id="member_level">
        {$levels|array_values|json_encode|raw}
    </script>
    <script type="text/javascript">
        (function(w){
            w.actionEnable=function(ids){
                dialog.confirm('确定将选中会员设置为正常状态？',function() {
                    $.ajax({
                        url:"{:url('member/status',['id'=>'__id__','type'=>1])}".replace('__id__',ids.join(',')),
                        type:'GET',
                        dataType:'JSON',
                        success:function(json){
                            if(json.code==1){
                                dialog.alert(json.msg,function() {
                                    location.reload();
                                });
                            }else{
                                dialog.warning(json.msg);
                            }
                        }
                    });
                });
            };
            w.actionDisable=function(ids){
                dialog.confirm('确定禁用选中会员？',function() {
                    $.ajax({
                        url:"{:url('member/status',['id'=>'__id__','type'=>0])}".replace('__id__',ids.join(',')),
                        type:'GET',
                        dataType:'JSON',
                        success:function(json){
                            if(json.code==1){
                                dialog.alert(json.msg,function() {
                                    location.reload();
                                });
                            }else{
                                dialog.warning(json.msg);
                            }
                        }
                    });
                });
            };
            w.actionSetIncrement=function () {
                dialog.prompt('请输入新的起始ID',function (input) {
                    $.ajax({
                        url:"{:url('member/set_increment',['incre'=>'__INCRE__'])}".replace('__INCRE__',input),
                        type:'GET',
                        dataType:'JSON',
                        success:function(json){
                            if(json.code==1){
                                dialog.alert(json.msg,function() {
                                    location.reload();
                                });
                            }else{
                                dialog.warning(json.msg);
                            }
                        }
                    });
                })
            }
        })(window);
        jQuery(function(){
            var tpl=$('#rechargeTpl').text();
            $('.btn-recharge').click(function() {
                var id=$(this).data('id');
                var dlg=new Dialog({
                    onshown:function(body){
                    },
                    onsure:function(body){
                        var amountField=body.find('[name=amount]');
                        var amount=amountField.val();
                        if(!amount){
                            dialog.warning('请填写金额');
                            amountField.focus();
                            return false;
                        }
                        if(amount!=parseFloat(amount)){
                            dialog.warning('请填写两位尾数以内的金额');
                            amountField.focus();
                            return false;
                        }
                        $.ajax({
                            url:'{:url("recharge")}',
                            type:'POST',
                            data:{
                                id:id,
                                amount:amount,
                                field:body.find('input[name=field]:checked').val(),
                                reson:body.find('input[name=reson]').val()
                            },
                            dataType:'JSON',
                            success:function(j){
                                if(j.code==1) {
                                    dlg.hide();
                                    dialog.alert(j.msg,function() {
                                        location.reload();
                                    })
                                }else{
                                    dialog.warning(j.msg);
                                }
                            }
                        })
                    }
                }).show(tpl,'会员充值');
            });
            $('.bindreferer').click(function (e) {
                var id=$(this).data('id')
                dialog.pickUser(function (user) {
                    $.ajax({
                        url:"{:url('set_referer')}",
                        dataType:'json',
                        data:{
                            id:id,
                            referer:user.id
                        },
                        success:function (json) {
                            dialog.alert(json.msg,function () {
                                if(json.code==1){
                                    location.reload()
                                }
                            })

                        }
                    })
                    return false;
                },{is_agent:1})
            })
            $('.delreferer').click(function (e) {
                var id=$(this).data('id')
                dialog.confirm('确定清除该会员的推荐人？',function () {
                    $.ajax({
                        url:"{:url('del_referer')}",
                        dataType:'json',
                        data:{
                            id:id
                        },
                        success:function (json) {
                            dialog.alert(json.msg,function () {
                                if(json.code==1){
                                    location.reload()
                                }
                            })

                        }
                    })
                    return false;
                },{is_agent:1})
            })

            var levels=JSON.parse($('#member_level').text());
            $('.btn-setlevel').click(function(e){
                var id=$(this).data('id')
                var level_id=$(this).data('level')
                dialog.pickList({
                    isajax:false,
                    list:levels,
                    idkey:'level_id',
                    rowTemplate:'<a href="javascript:" data-id="{@level_id}" class="list-group-item list-group-item-action" style="line-height:30px;">[{@level_id}]&nbsp;{@level_name}</a>'
                },function(level){
                    if(level.level_id==level_id){
                        dialog.warning('未修改')
                        return false;
                    }
                    $.ajax({
                        url:"{:url('set_level')}",
                        dataType:'json',
                        data:{
                            id:id,
                            level_id:level.level_id
                        },
                        success:function (json) {
                            dialog.alert(json.msg,function () {
                                if(json.code==1){
                                    location.reload()
                                }
                            })
                        }
                    })
                })
            });

            var agents=JSON.parse($('#member_agent').text());
            $('.btn-setagent').click(function(e){
                e.preventDefault();

                var id=$(this).data('id')
                var agent_id=$(this).data('agent')
                dialog.pickList({
                    isajax:false,
                    list:agents,
                    idkey:'id',
                    rowTemplate:'<a href="javascript:" data-id="{@id}" class="list-group-item list-group-item-action" style="line-height:30px;">[{@id}]&nbsp;{@name} {@cost_credit}积分 {@total_award}收益  </a>'
                },function(agent){
                    if(agent.id==agent_id){
                        dialog.warning('未修改')
                        return false;
                    }
                    $.ajax({
                        url:"{:url('set_agent')}",
                        dataType:'json',
                        data:{
                            id:id,
                            agent_id:agent.id
                        },
                        success:function (json) {
                            dialog.alert(json.msg,function () {
                                if(json.code==1){
                                    location.reload()
                                }
                            })
                        }
                    })
                })
            })
        });
    </script>
</block>