{extend name="public:base" /}

{block name="body"}
{include  file="public/bread" menu="notice_index" title="公告列表"  /}

<div id="page-wrapper">
    
    <div class="row list-header">
        <div class="col-6">
            <a href="{:url('Notice/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加公告</a>
        </div>
        <div class="col-6">
            <form action="{:url('Notice/index')}" method="post">
                <div class="form-group input-group input-group-sm">
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
                <th>标题</th>
                <th>链接</th>
                <th>状态</th>
                <th width="160">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        {volist name="lists" id="v" empty="$empty"}
            <tr>
                <td>{$v.id}</td>
                <td>{$v.title}</td>
                <td>{$v.url}</td>
                <td data-id="{$v.id}" data-url="{:url('status')}">
                    {if $v['status'] EQ 1}
                        <span class="chgstatus" data-status="0" title="点击隐藏">显示</span>
                        {else/}
                        <span class="chgstatus off" data-status="1" title="点击显示">隐藏</span>
                    {/if}
                </td>
                <td class="operations">
                    <a class="btn btn-outline-primary" title="编辑" href="{:url('Notice/edit',array('id'=>$v['id']))}"><i class="ion-md-create"></i> </a>
                    <a class="btn btn-outline-danger link-confirm" title="删除" data-confirm="您真的确定要删除吗？\n删除后将不能恢复!" href="{:url('Notice/delete',array('id'=>$v['id']))}"><i class="ion-md-trash"></i> </a>
                </td>
            </tr>
        {/volist}
        </tbody>
    </table>
    {$page|raw}
</div>

{/block}