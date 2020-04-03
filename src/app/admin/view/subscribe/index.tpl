{extend name="public:base" /}

{block name="body"}

<include file="public/bread" menu="subscribe_index" title="订阅列表" />

<div id="page-wrapper">
    
    <div class="row list-header">
        <div class="col-6">
            <a href="{:url('subscribe/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加订阅</a>
        </div>
        <div class="col-6">
            <form action="{:url('subscribe/index')}" method="post">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" name="key" placeholder="输入名称或邮箱搜索">
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
                <th>名称</th>
                <th>邮箱地址</th>
                <th>状态</th>
                <th width="160">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <php>$empty=list_empty(5);</php>
        {volist name="lists" id="v" empty="$empty"}
            <tr>
                <td>{$v.id}</td>
                <td>{$v.title}</td>
                <td>{$v.email}</td>
                <td>{$v.status}</td> 
                <td class="operations">
                    <a class="btn btn-outline-primary" title="编辑" href="javascript:"><i class="ion-md-create"></i> </a>
                    <a class="btn btn-outline-danger link-confirm" title="删除" data-configm="您真的确定要删除吗？\n删除后将不能恢复!" href="{:url('subscribe/delete',array('id'=>$v['id']))}" ><i class="ion-md-trash"></i> </a>
                </td>
            </tr>
        {/volist}
        </tbody>
    </table>
    {$page|raw}
</div>
{/block}