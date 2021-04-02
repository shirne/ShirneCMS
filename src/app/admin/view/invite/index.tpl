{extend name="public:base" /}

{block name="body"}

{include  file="public/bread" menu="invite_index" title="邀请码列表"  /}

<div id="page-wrapper">
    <div class="row list-header">
        <div class="col-6">
            <div class="btn-toolbar list-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                <div class="btn-group btn-group-sm mr-2" role="group" aria-label="check action group">
                    <a href="javascript:" class="btn btn-outline-secondary checkall-btn" data-toggle="button" aria-pressed="false">全选</a>
                    <a href="javascript:" class="btn btn-outline-secondary checkreverse-btn">反选</a>
                </div>
                <div class="btn-group btn-group-sm mr-2" role="group" aria-label="action button group">
                    <a href="javascript:" class="btn btn-outline-secondary action-btn" data-action="transfer">转赠</a>
                    <a href="javascript:" class="btn btn-outline-secondary action-btn" data-action="enable">解锁</a>
                    <a href="javascript:" class="btn btn-outline-secondary action-btn" data-action="disable">锁定</a>
                </div>
                <a href="{:url('Invite/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 生成邀请码</a>
            </div>
            
        </div>
        <div class="col-6">
            <form action="{:url('Invite/index')}" method="post">
                <div class="input-group input-group-sm">
                    <select name="accurate" class="form-control" style="width: 130px;flex-grow: 0;">
                        <option value="0">按激活码搜索</option>
                        <option value="1" {$accurate==1?'selected':''}>按持有会员搜索</option>
                        <option value="2" {$accurate==2?'selected':''}>按使用会员搜索</option>
                    </select>
                    <input type="text" class="form-control" name="keyword" value="{$keyword}" placeholder="输入用户id或邀请码搜索">
                    <div class="input-group-append">
                      <button class="btn btn-outline-secondary" type="submit"><i class="ion-md-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th width="50">#</th>
                <th>邀请码</th>
                <th>所属会员</th>
                <th>会员组</th>
                <th>创建日期</th>
                <th>使用会员</th>
                <th>有效期</th>
                <th>状态</th>
                <th width="160">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        {php}$empty=list_empty(10);{/php}
        {volist name="lists" id="v" empty="$empty"}
            <tr>
                <td><input type="checkbox" name="id" value="{$v.id}" /></td>
                <td>{$v.code}</td>
                <td>
                    <div class="media">
                        <if condition="!empty($v['avatar'])">
                            <img src="{$v.avatar}" class="mr-2 rounded" width="30"/>
                        </if>
                        <div class="media-body">
                            <h5 class="mt-0 mb-1" style="font-size:13px;">
                                <if condition="!empty($v['nickname'])">
                                    {$v.nickname}
                                    <else/>
                                    {$v.username}
                                </if>
                            </h5>
                            <div style="font-size:12px;">
                                [{$v.member_id} {$levels[$v['level_id']]['level_name']}]
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    {if $v['level_id'] GT 0}
                        {$levels[$v['level_id']]['level_name']}
                        {else/}
                        -
                    {/if}
                </td>
                <td>{$v.create_time|showdate}</td>
<<<<<<< HEAD:src/app/admin/view/invite/index.tpl
                <td>[{$v.member_use}]{$v.use_username}</td>
                <td>{$v.use_at|showdate}</td>
                <td>{$v.valid_at|showdate}</td>
                <td>{if $v.status eq 1}<span class="badge badge-danger">锁定</span>{else/}<span class="badge badge-secondary">正常</span>{/if}</td>
                <td class="operations">
                    <a class="btn btn-outline-primary" title="转赠" href="{:url('Invite/update',array('id'=>$v['id']))}"><i class="ion-md-repeat"></i> </a>
                    {if $v.status eq 0}
=======
                <td>
                    <if condition="$v['member_use'] gt 0">
                        <div class="media">
                            <if condition="!empty($v['avatar'])">
                                <img src="{$v.avatar}" class="mr-2 rounded" width="30"/>
                            </if>
                            <div class="media-body">
                                <h5 class="mt-0 mb-1" style="font-size:13px;">
                                    [{$v.member_id}]<if condition="!empty($v['nickname'])">
                                        {$v.nickname}
                                        <else/>
                                        {$v.username}
                                    </if>
                                </h5>
                                <div style="font-size:12px;">
                                    {$v.use_time|showdate}
                                </div>
                            </div>
                        </div>
                        <else/>
                        -
                    </if>  
                </td>
                <td>{$v.invalid_time|showdate}</td>
                <td><if condition="$v.is_lock eq 1"><span class="badge badge-danger">锁定</span><else/><span class="badge badge-secondary">正常</span></if></td>
                <td class="operations">
                    <a class="btn btn-outline-primary btn-transfer" data-id="{$v.id}" title="转赠" href="javascript:"><i class="ion-md-repeat"></i> </a>
                    <if condition="$v.is_lock eq 0">
>>>>>>> v2:src/application/admin/view/invite/index.tpl
                        <a class="btn btn-outline-danger link-confirm" title="锁定" data-confirm="锁定后将不能使用此激活码注册!\n请确认!!!" href="{:url('Invite/lock',array('id'=>$v['id']))}" ><i class="ion-md-close"></i> </a>
                    {else/}
                        <a class="btn btn-outline-success link-confirm" title="解锁" href="{:url('Invite/unlock',array('id'=>$v['id']))}" style="color:#50AD1E;"><i class="ion-md-check"></i> </a>
                    {/if}
                </td>
            </tr>
        {/volist}
        </tbody>
    </table>
    {$page|raw}
</div>

<<<<<<< HEAD:src/app/admin/view/invite/index.tpl
{/block}
=======
</block>
<block name="script">
    <script>
        (function(w){
            w.actionEnable=function(ids){
                dialog.confirm('确定将选中激活码解锁？',function() {
                    $.ajax({
                        url:"{:url('invite/lock',['id'=>'__id__','is_lock'=>0])}".replace('__id__',ids.join(',')),
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
                dialog.confirm('确定锁定选中激活码？',function() {
                    $.ajax({
                        url:"{:url('invite/lock',['id'=>'__id__','is_lock'=>1])}".replace('__id__',ids.join(',')),
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
            w.actionTransfer=function (ids) {
                dialog.pickUser(function (user) {
                    $.ajax({
                        url:"{:url('invite/transfer',['uid'=>'__UID__','id'=>'__ID__'])}".replace('__UID__',user.id).replace('__ID__',ids.join(',')),
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
                },'请选择接收会员')
            }
        })(window);
        jQuery(function(){
            $('.btn-transfer').click(function(e){
                e.preventDefault();
                var id = $(this).data('id');
                dialog.pickUser(function (user) {
                    $.ajax({
                        url:"{:url('invite/transfer',['uid'=>'__UID__','id'=>'__ID__'])}".replace('__UID__',user.id).replace('__ID__',id),
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
                },'请选择接收会员')
            })
        })
    </script>
</block>
>>>>>>> v2:src/application/admin/view/invite/index.tpl
