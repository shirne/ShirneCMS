<extend name="public:base" />

<block name="body">

<include file="channel/_bread" title="Banner列表" />

<div id="page-wrapper">
    
    <div class="row list-header">
        <div class="col-6">
            <a href="{:url('channel/index',array('channel_id'=>$channel_id))}" class="btn btn-outline-primary btn-sm"><i class="ion-md-arrow-back"></i> 返回</a>
        </div>
        <div class="col-6 text-right">
            <a href="{:url('channel/banneradd',array('channel_id'=>$channel_id, 'gid'=>$gid))}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加</a>
        </div>
    </div>
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th width="50">编号</th>
                <th>图片</th>
                <th>标题</th>
                <th>链接</th>
                <th>有效期</th>
                <th>排序</th>
                <th>状态</th>
                <th width="160">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <empty name="lists">{:list_empty(8)}</empty>
        <foreach name="lists" item="v">
            <tr>
                <td>{$v.id}</td>
                <td><figure class="figure" >
                        <img src="{$v.image|default='/static/images/nopic.png'}?w=100" class="figure-img img-fluid rounded" alt="image">
                    </figure></td>
                <td>{$v.title}</td>
                <td>{$v.url}</td>
                <td>{$v.start_date|showdate}<br />{$v.end_date|showdate}</td>
                <td>{$v.sort}</td>
                <td>{$v.status|show_status|raw}</td>
                <td class="operations">
                    <a class="btn btn-outline-primary" title="编辑" href="{:url('channel/bannerupdate',array('channel_id'=>$channel_id,'id'=>$v['id']))}"><i class="ion-md-create"></i> </a>
                    <a class="btn btn-outline-danger link-confirm" title="删除" data-confirm="您真的确定要删除吗？\n\n删除后将不能恢复!" href="{:url('channel/bannerdelete',array('id'=>$v['id']))}" ><i class="ion-md-trash"></i> </a>
                </td>
            </tr>
        </foreach>
        </tbody>
    </table>
    {$page|raw}
</div>
</block>