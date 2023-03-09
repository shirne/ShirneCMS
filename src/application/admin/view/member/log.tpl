{extend name="public:base" /}

{block name="body"}
{include file="public/bread" menu="member_log" title="" /}

<div id="page-wrapper">
    <div class="row list-header">
        <div class="col-md-6">
            <a href="{:url('member/logclear')}" class="btn btn-outline-secondary btn-sm"><i class="ion-md-trash"></i> 清理日志</a>
        </div>
        <div class="col-md-6">
            <form action="{:url('member/log')}" method="post">
                <div class="form-group input-group input-group-sm">
                    <input type="text" class="form-control" name="key" value="{$keyword}" placeholder="输入用户名或者关键词搜索">
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
                <th>用户名</th>
                <th>操作</th>
                <th>状态</th>
                <th>时间</th>
                <th>IP</th>
                <th>备注</th>
                <th width="80">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        {empty name="lists"}{:list_empty(8)}{/empty}
        {foreach $lists as $key => $v}
            <tr>
                <td>{$v.id}</td>
                <td>
                    <div class="media">
                        {if !empty($v['avatar'])}
                            <img src="{$v.avatar}" class="mr-2 rounded" width="30"/>
                        {/if}
                        <div class="media-body">
                            <h5 class="mt-0 mb-1" >
                                {if !empty($v['nickname'])}
                                    {$v.nickname}
                                {else/}
                                    {$v.username}
                                {/if}
                            </h5>
                        </div>
                    </div>
                </td>
                <td>{$v.action}</td>
                <td>{if $v['result'] == 1}<span class="badge badge-success">成功</span>{else/}<span class="badge badge-danger">失败</span> {/if}</td>
                <td>{$v.create_time|showdate}</td>
                <td>{$v.ip}</td>
                <td>{$v.remark|print_remark}</td>
                <td class="operations">
                    <a class="btn btn-outline-primary" title="查看" data-title="查看详情" rel="ajax" href="{:url('member/logview',array('id'=>$v['id']))}"><i class="ion-md-document"></i> </a>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    {$page|raw}
</div>

{/block}