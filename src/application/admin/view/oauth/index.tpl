{extend name="public:base" /}

{block name="body"}

{include file="public/bread" menu="setting_index" title="授权登录接口" /}

<div id="page-wrapper">
    
    <div class="row list-header">
        <div class="col-6">
            <a href="{:url('oauth/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加接口</a>
        </div>
        <div class="col-6">
            &nbsp;
        </div>
    </div>
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th width="50">编号</th>
                <th>名称</th>
                <th>类型</th>
                <th>appid</th>
                <th width="160">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        {foreach $lists as $key => $v}
            <tr>
                <td>{$v.id}</td>
                <td>{$types[$v['type']]}</td>
                <td>{$v.title}</td>
                <td>{$v.appid}</td>
                <td class="operations">
                    <a class="btn btn-outline-primary" title="编辑" href="{:url('oauth/edit',array('id'=>$v['id']))}"><i class="ion-md-create"></i> </a>
                    <a class="btn btn-outline-danger link-confirm" title="删除" data-confirm="您真的确定要删除吗？\n删除后将不能恢复!" href="{:url('oauth/delete',array('id'=>$v['id']))}" ><i class="ion-md-trash"></i> </a>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    {$page|raw}
</div>
{/block}