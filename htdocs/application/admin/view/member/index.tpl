<extend name="public:base" />

<block name="body">
<include file="public/bread" menu="member_index" title=""/>

<div id="page-wrapper">
    <div class="row list-header">
        <div class="col col-6">
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
                <a href="{:url('member/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加会员</a>
            </div>
        </div>
        <div class="col col-6">
            <form action="{:url('member/index')}" method="post">
                <div class="form-row">
                <div class="form-group col input-group input-group-sm">
                    <div class="input-group-prepend">
                    <span class="input-group-text">上级</span>
                    </div>
                    <input type="text" class="form-control" name="referer" placeholder="填写id或会员名" value="{$referer}">
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
                <th>注册时间</th>
                <th>上次登陆</th>
                <th>代理</th>
                <th>类型/级别</th>
                <th width="200">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        <php>$empty=list_empty(10);</php>
        <volist name="lists" id="v" empty="$empty">
            <tr>
                <td><input type="checkbox" name="id" value="{$v.id}" /></td>
                <td>{$v.username}
                    <if condition="$v.status eq 1"><else/><span class="badge badge-danger" >禁用</span></if><br/>{$v.realname}</td>
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
                        -
                        <else/>
                        {$v.refer_name}[{$v.referer}]<br />
                        {$v.refer_realname}
                    </empty>
                </td>
                <td>{$v.create_time|showdate}</td>
                <td>{$v.login_ip}<br />{$v.logintime|showdate}</td>
                <td class="operations">

                    <if condition="$v.is_agent neq 0">
                        <a class="btn btn-outline-primary" title="查看下线" href="{:url('member/index',array('referer'=>$v['id']))}"><i class="ion-md-people"></i> </a>
                        <a class="btn btn-outline-danger link-confirm" data-confirm="取消代理不能更改已注册的用户!!!" title="取消代理" href="{:url('member/cancel_agent',array('id'=>$v['id']))}" ><i class="ion-md-log-out"></i> </a>
                    <else/>
                        <a class="btn btn-outline-primary {$v.refer_agent>2?'disabled':''}" title="设置代理" href="{:url('member/set_agent',array('id'=>$v['id']))}" ><i class="ion-md-check"></i> </a>
                    </if>

                </td> 
                <td>
                    <span class="badge badge-info">{$types[$v['type']]}</span>
                    <span class="badge badge-info">{$levels[$v['level_id']]['level_name']}</span>
                </td>
                <td class="operations">
                    <a class="btn btn-outline-primary" title="编辑" href="{:url('member/update',array('id'=>$v['id']))}"><i class="ion-md-create"></i> </a>
                    <a class="btn btn-outline-warning btn-recharge" title="充值" href="javascript:" data-id="{$v.id}" ><i class="ion-md-card"></i> </a>
                    <a class="btn btn-outline-primary" title="资金明细" href="{:url('member/money_log',array('id'=>$v['id']))}" ><i class="ion-md-paper"></i> </a>

                    <if condition="$v.status eq 1">
                        <a class="btn btn-outline-warning link-confirm" title="禁用" data-confirm="禁用后用户将不能登陆!\n请确认!!!" href="{:url('member/status',array('id'=>$v['id'],'type'=>0))}" ><i class="ion-md-remove-circle-outline"></i> </a>
                        <else/>
                        <a class="btn btn-outline-success" title="启用" href="{:url('member/status',array('id'=>$v['id'],'type'=>1))}" style="color:#50AD1E;"><i class="ion-md-check"></i> </a>
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
    <script type="text/plain" id="rechargeTpl">
        <div class="row" style="margin:0 10%;">
            <div class="col-12 form-group"><div class="input-group"><div ><span class="input-group-text">充值类型</span> </div><div class="col w-50 text-center" ><div class="btn-group btn-group-toggle" data-toggle="buttons">
                <foreach name="moneyTypes" item="mt" key="k">
                <label class="btn btn-outline-primary {$k=='money'?'active':''}"> <input type="radio" name="field" value="{$k}" autocomplete="off" {$k=='money'?'checked':''}> {$mt}</label>
                </foreach>
            </div></div> </div></div>
            <div class="col-12 form-group"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">充值金额</span> </div><input type="text" name="amount" class="form-control" placeholder="请填写充值金额"/> </div></div>
            <div class="col-12 form-group"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">充值原因</span> </div><input type="text" name="reson" class="form-control" placeholder="请填写充值原因"/> </div> </div>
        </div>
    </script>
    <script type="text/javascript">
        (function(w){
            w.actionEnable=function(ids){
                dialog.confirm('确定将选中会员设置为正常状态？',function() {
                    $.ajax({
                        url:"{:url('member/delete',['id'=>'__id__','type'=>1])}".replace('__id__',ids.join(',')),
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
                        url:"{:url('member/delete',['id'=>'__id__','type'=>0])}".replace('__id__',ids.join(',')),
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
        });
    </script>
</block>