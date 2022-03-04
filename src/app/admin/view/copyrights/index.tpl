{extend name="public:base" /}

{block name="body"}

{include  file="public/bread" menu="copyrights_index" title="版权署名列表"  /}

<div id="page-wrapper" class="container-fluid">
    
    <div class="row list-header">
        <div class="col-6">
            <a href="{:url('copyrights/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加版权署名</a>
        </div>
        <div class="col-6">
            <form action="{:url('copyrights/index')}" method="post">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" name="key" placeholder="输入版权署名或者说明搜索">
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
                <th>版权署名</th>
                <th>排序</th>
                <th width="160">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            {empty name="lists"}{:list_empty(6)}{/empty}
        {volist name="lists" id="v" }
            <tr>
                <td>{$v.id}</td>
                <td>{$v.title}</td>
                <td>{$v.sort}</td> 
                <td class="operations">
                    <a class="btn btn-outline-primary" title="编辑" href="{:url('copyrights/edit',array('id'=>$v['id']))}"><i class="ion-md-create"></i> </a>
                    <a class="btn btn-outline-danger link-confirm" title="删除" data-configm="您真的确定要删除吗？\n删除后将不能恢复!" href="{:url('copyrights/delete',array('id'=>$v['id']))}" ><i class="ion-md-trash"></i> </a>
                </td>
            </tr>
            {/volist}
        </tbody>
    </table>
    {$page|raw}
</div>
{/block}