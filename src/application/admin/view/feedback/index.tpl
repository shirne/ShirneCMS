<extend name="public:base" />

<block name="body">

<include file="public/bread" menu="feedback_index" title="留言列表" />

<div id="page-wrapper">
    
    <div class="row list-header">
        <div class="col-6">

        </div>
        <div class="col-6">
            <form action="{:url('feedback/index')}" method="post">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" name="key" placeholder="输入邮箱或者关键词搜索">
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
                <th>会员</th>
                <th>联系</th>
                <th>类型</th>
                <th>IP</th>
                <th>日期</th>
                <th>状态</th>
                <th width="160">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        <php>$empty=list_empty(8);</php>
        <volist name="lists" id="v" empty="$empty">
            <tr>
                <td>{$v.id}</td>
                <td>
                    <if condition="empty($v['member_id'])">
                        -
                    <else/>
                        <div class="media">
                            <if condition="!empty($v['avatar'])">
                                <img src="{$v.avatar}" class="mr-2 rounded" width="30"/>
                            </if>
                            <div class="media-body">
                                <h5 class="mt-0 mb-1" style="font-size:13px;">
                                    <if condition="!empty($v['nickname'])">
                                        {$v.nickname}
                                        <else/>
                                        {$v.username}
                                    </if>
                                </h5>
                                <div style="font-size:12px;">
                                    [{$v.member_id} {$levels[$v['level_id']]['level_name']}]
                                </div>
                            </div>
                        </div>
                    </if>
                </td>
                <td>{$v.realname}<br />{$v.mobile}<br />{$v.email}</td>
                <td>{$v.type}</td>
                <td>{$v.ip}</td>
                <td>{$v.create_time|showdate}</td>
                <td>{$v.status|feedback_status|raw}</td>
                <td class="operations">
                    <a class="btn btn-outline-primary" title="回复" href="{:url('feedback/reply',array('id'=>$v['id']))}"><i class="ion-md-chatboxes"></i> </a>
                    <a class="btn btn-outline-danger link-confirm" title="删除" data-confirm="您真的确定要删除吗？\n删除后将不能恢复!" href="{:url('feedback/delete',array('id'=>$v['id']))}" ><i class="ion-md-trash"></i> </a>
                </td>
            </tr>
        </volist>
        </tbody>
    </table>
    {$page|raw}
</div>

</block>