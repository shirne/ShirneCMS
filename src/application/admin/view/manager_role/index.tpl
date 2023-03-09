{extend name="public:base" /}

{block name="body"}
{include file="public/bread" menu="manager_index" title="角色列表" /}

<div id="page-wrapper">
    <div class="row list-header">
        <div class="col-md-6">
            <a href="{:url('manager/index')}" class="btn btn-outline-secondary btn-sm"><i class="ion-md-people"></i> 管理员管理</a>
            <a href="{:url('manager_role/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加角色</a>
        </div>
        <div class="col-md-6">
            <form action="{:url('manager_role/index')}" method="post">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" name="key" placeholder="输入角色名或者邮箱关键词搜索">
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
                <th>角色名</th>
                <th>角色等级</th>
                <th>人数</th>
                <th width="160">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        {foreach $lists as $key => $v}
            <tr>
                <td>{$v.id}</td>
                <td>{$v.role_name}</td>
                <td>{$v.type}</td>
                <td>{$counts[$v.type]?:0}</td>
                <td class="operations">
                    <a class="btn btn-outline-primary" title="编辑" href="{:url('manager_role/update',array('id'=>$v['id']))}"><i class="ion-md-create"></i> </a>
                    {if $v['type']!=1}
                    <a class="btn btn-outline-danger" title="删除" href="{:url('manager_role/delete',array('id'=>$v['id']))}" ><i class="ion-md-trash"></i> </a>
                    {/if}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>

{/block}