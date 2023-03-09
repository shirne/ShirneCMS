{extend name="public:base" /}

{block name="body"}

{include file="public/bread" menu="adv_index" title="广告位" /}

<div id="page-wrapper">
    
    <div class="row list-header">
        <div class="col-6">
            <a href="{:url('adv/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加广告位</a>
        </div>
        <div class="col-6">
            <form action="{:url('adv/index')}" method="post">
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
                <th>推荐尺寸</th>
                <th width="180">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        {empty name="lists"}{:list_empty(5)}{/empty}
        {volist name="lists" id="v" }
            <tr>
                <td>{$v.id}</td>
                <td>{$v.title}</td>
                <td>{$v.flag}</td>
                <td>{$v.width}px &times; {$v.height}px</td>
                <td class="operations">
                    {if $v['locked']}
                        <a class="btn btn-outline-primary" title="解锁" href="{:url('adv/unlock',array('id'=>$v['id']))}"><i class="ion-md-unlock"></i></a>
                        {else/}
                        <a class="btn btn-outline-primary" title="编辑" href="{:url('adv/update',array('id'=>$v['id']))}"><i class="ion-md-create"></i></a>
                        <a class="btn btn-outline-primary" title="锁定" href="{:url('adv/lock',array('id'=>$v['id']))}"><i class="ion-md-lock"></i></a>
                    {/if}

                    <a class="btn btn-outline-primary" title="广告列表" href="{:url('adv/itemlist',array('gid'=>$v['id']))}"><i class="ion-md-menu"></i></a>
                    <a class="btn btn-outline-primary" title="添加广告" href="{:url('adv/itemadd',array('gid'=>$v['id']))}"><i class="ion-md-add"></i></a>
                    {if $v['locked'] == 0}
                    <a class="btn btn-outline-danger link-confirm" data-confirm="您真的确定要删除吗？\n删除后将不能恢复!" title="删除" href="{:url('adv/delete',array('id'=>$v['id']))}" ><i class="ion-md-trash"></i></a>
                    {/if}
                </td>
            </tr>
        {/volist}
        </tbody>
    </table>
    {$page|raw}
</div>
{/block}