{extend name="public:base" /}

{block name="body"}

{include file="public/bread" menu="page_index" title="单页列表" /}

<div id="page-wrapper">

    <div class="row list-header">
        <div class="col-6">
            <div class="btn-group btn-group-sm mr-2" role="group" aria-label="check action group">
                <a href="javascript:" class="btn btn-outline-secondary checkall-btn" data-toggle="button"
                    aria-pressed="false">全选</a>
                <a href="javascript:" class="btn btn-outline-secondary checkreverse-btn">反选</a>
            </div>
            <div class="btn-group btn-group-sm mr-2" role="group" aria-label="action button group">
                <a href="javascript:" class="btn btn-outline-secondary action-btn" data-action="show">显示</a>
                <a href="javascript:" class="btn btn-outline-secondary action-btn" data-action="hide">隐藏</a>
                <a href="javascript:" class="btn btn-outline-secondary action-btn" data-action="delete">删除</a>
            </div>
            <a href="{:url('page/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加单页</a>
            <a href="{:url('page/groups')}" class="btn btn-outline-secondary btn-sm"><i class="ion-md-bookmarks"></i>
                分组管理</a>
        </div>
        <div class="col-6">
            <form action="{:url('page/index')}" method="post">
                <div class="form-row">
                    <div class="col input-group input-group-sm mr-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text">分组</span>
                        </div>
                        <select name="group" class="form-control">
                            <option value="0">不限分组</option>
                            {foreach $groups as $key => $v}
                            <option value="{$v.group}" {$group==$v['group']?'selected':""}>{$v.group_name}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="col input-group input-group-sm">
                        <input type="text" class="form-control" name="key" value="{$keyword}"
                            placeholder="输入单页标题或者别名关键词搜索">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit"><i
                                    class="ion-md-search"></i></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th width="50">编号</th>
                <th>分组</th>
                <th>别名</th>
                <th>标题</th>
                <th>排序</th>
                <th>状态</th>
                <th width="160">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            {php}$empty=list_empty(7);{/php}
            {volist name="lists" id="v" empty="$empty"}
            <tr>
                <td><input type="checkbox" name="id" value="{$v.id}" /></td>
                <td>{$v.group}{if $v['group_use_template'] == 1}&nbsp;<span class="badge badge-warning">独立模板</span>{/if}
                </td>
                <td>{$v.name}{if $v['use_template'] == 1}&nbsp;<span class="badge badge-warning">独立模板</span>{/if}</td>
                <td>{$v.title}</td>
                <td>{$v.sort}</td>
                <td data-id="{$v.id}" data-url="{:url('status')}">
                    {if $v['status'] == 1}
                    <span class="chgstatus" data-status="0" title="点击隐藏">显示</span>
                    {else/}
                    <span class="chgstatus off" data-status="1" title="点击显示">隐藏</span>
                    {/if}
                </td>
                <td class="operations">
                    <a class="btn btn-outline-primary" title="编辑" href="{:url('page/edit',array('id'=>$v['id']))}"><i
                            class="ion-md-create"></i> </a>
                    <a class="btn btn-outline-danger link-confirm" title="删除" data-confirm="您真的确定要删除吗？\n删除后将不能恢复!"
                        href="{:url('page/delete',array('id'=>$v['id']))}"><i class="ion-md-trash"></i> </a>
                </td>
            </tr>
            {/volist}
        </tbody>
    </table>
    {$page|raw}
</div>

{/block}
{block name="script"}
<script type="text/javascript">
    (function (w) {
        w.actionShow = function (ids) {
            dialog.confirm('确定将选中页面显示？', function () {
                $.ajax({
                    url: "{:url('page/status',['id'=>'__id__','status'=>1])}".replace('__id__', ids.join(',')),
                    type: 'GET',
                    dataType: 'JSON',
                    success: function (json) {
                        if (json.code == 1) {
                            dialog.alert(json.msg, function () {
                                location.reload();
                            });
                        } else {
                            dialog.warning(json.msg);
                        }
                    }
                });
            });
        };
        w.actionHide = function (ids) {
            dialog.confirm('确定将选中页面隐藏？', function () {
                $.ajax({
                    url: "{:url('page/status',['id'=>'__id__','status'=>0])}".replace('__id__', ids.join(',')),
                    type: 'GET',
                    dataType: 'JSON',
                    success: function (json) {
                        if (json.code == 1) {
                            dialog.alert(json.msg, function () {
                                location.reload();
                            });
                        } else {
                            dialog.warning(json.msg);
                        }
                    }
                });
            });
        };
        w.actionDelete = function (ids) {
            dialog.confirm('确定删除选中的页面？', function () {
                $.ajax({
                    url: "{:url('page/delete',['id'=>'__id__'])}".replace('__id__', ids.join(',')),
                    type: 'GET',
                    dataType: 'JSON',
                    success: function (json) {
                        if (json.code == 1) {
                            dialog.alert(json.msg, function () {
                                location.reload();
                            });
                        } else {
                            dialog.warning(json.msg);
                        }
                    }
                });
            });
        };
    })(window)
</script>
{/block}