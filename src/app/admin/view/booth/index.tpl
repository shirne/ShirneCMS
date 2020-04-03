{extend name="public:base" /}

{block name="body"}

<include file="public/bread" menu="booth_index" title="展位管理" />

<div id="page-wrapper">
    
    <div class="row list-header">
        <div class="col-6">
            <a href="{:url('booth/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加展位</a>
        </div>
        <div class="col-6">
            <form action="{:url('booth/index')}" method="post">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" name="key" placeholder="输入标题或者地址关键词搜索">
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
                <th>调用标识</th>
                <th>数据类型</th>
                <th width="180">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        {volist name="lists" id="v" }
            <tr>
                <td>{$v.id}</td>
                <td>{$v.title}</td>
                <td>{$v.flag}</td>
                <td>{$v.type}</td>
                <td class="operations">
                    <a class="btn btn-outline-primary" title="编辑" href="{:url('booth/update',array('id'=>$v['id']))}"><i class="ion-md-create"></i></a>
                    {if $v['locked']}
                        <a class="btn btn-outline-primary" title="解锁" href="{:url('booth/unlock',array('id'=>$v['id']))}"><i class="ion-md-unlock"></i></a>
                        {else/}
                        <a class="btn btn-outline-primary" title="锁定" href="{:url('booth/lock',array('id'=>$v['id']))}"><i class="ion-md-lock"></i></a>
                    <a class="btn btn-outline-danger link-confirm" data-confirm="您真的确定要删除吗？\n删除后将不能恢复!" title="删除" href="{:url('booth/delete',array('id'=>$v['id']))}" ><i class="ion-md-trash"></i></a>
                    {/if}
                </td>
            </tr>
        {/volist}
        </tbody>
    </table>
    {$page|raw}
</div>
{/block}