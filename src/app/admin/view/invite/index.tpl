{extend name="public:base" /}

{block name="body"}

{include  file="public/bread" menu="invite_index" title="邀请码列表"  /}

<div id="page-wrapper">
    <div class="row list-header">
        <div class="col-6">
            <a href="{:url('Invite/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 生成邀请码</a>
        </div>
        <div class="col-6">
            <form action="{:url('Invite/index')}" method="post">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" name="key" value="{$keyword}" placeholder="输入用户id或邀请码搜索">
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
                <th width="50">编号</th>
                <th>邀请码</th>
                <th>所属会员</th>
                <th>会员组</th>
                <th>创建日期</th>
                <th>使用会员</th>
                <th>使用日期</th>
                <th>有效期</th>
                <th>状态</th>
                <th width="160">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        {php}$empty=list_empty(10);{/php}
        {volist name="lists" id="v" empty="$empty"}
            <tr>
                <td>{$v.id}</td>
                <td>{$v.code}</td>
                <td>[{$v.member_id}]{$v.username}</td>
                <td>
                    {if $v['level_id'] GT 0}
                        {$levels[$v['level_id']]['level_name']}
                        {else/}
                        -
                    {/if}
                </td>
                <td>{$v.create_time|showdate}</td>
                <td>[{$v.member_use}]{$v.use_username}</td>
                <td>{$v.use_at|showdate}</td>
                <td>{$v.valid_at|showdate}</td>
                <td>{if $v.status eq 1}<span class="badge badge-danger">锁定</span>{else/}<span class="badge badge-secondary">正常</span>{/if}</td>
                <td class="operations">
                    <a class="btn btn-outline-primary" title="转赠" href="{:url('Invite/update',array('id'=>$v['id']))}"><i class="ion-md-repeat"></i> </a>
                    {if $v.status eq 0}
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

{/block}