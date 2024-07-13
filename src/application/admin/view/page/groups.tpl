{extend name="public:base" /}

{block name="body"}

{include file="public/bread" menu="page_index" title="页面分组" /}

<div id="page-wrapper">

    <div class="row list-header">
        <div class="col-6">
            <a href="{:url('page/index')}" class="btn btn-outline-secondary btn-sm">页面管理</a>
            <a href="{:url('page/groupedit')}" class="btn btn-outline-primary btn-sm">添加分组</a>
        </div>
        <div class="col-6">&nbsp;
        </div>
    </div>
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th width="50">编号</th>
                <th>分组</th>
                <th>组名</th>
                <th>排序</th>
                <th width="160">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            {foreach $lists as $key => $v}
            <tr>
                <td>{$v.id}</td>
                <td>{$v.group}{if $v['use_template'] == 1}&nbsp;<span class="badge badge-warning">独立模板</span>{/if}</td>
                <td>{$v.group_name}</td>
                <td>{$v.sort}</td>
                <td class="operations">
                    <a class="btn btn-outline-primary" title="编辑"
                        href="{:url('page/groupedit',array('id'=>$v['id']))}"><i class="ion-md-create"></i> </a>
                    <a class="btn btn-outline-danger link-confirm" title="删除" data-confirm="您真的确定要删除吗？\n删除后将不能恢复!"
                        href="{:url('page/groupdelete',array('id'=>$v['id']))}"><i class="ion-md-trash"></i> </a>
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
    {$page|raw}
</div>

{/block}