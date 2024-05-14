{extend name="public:base" /}

{block name="body"}

{include file="public/bread" menu="adv_index" title="广告列表" /}

<div id="page-wrapper">

    <div class="row list-header">
        <div class="col-6">
            <a href="{:url('other.adv/itemadd',array('gid'=>$gid))}" class="btn btn-outline-primary btn-sm"><i
                    class="ion-md-add"></i> 添加广告</a>
        </div>
        <div class="col-6">
            <form action="{:url('other.adv/itemlist')}" method="post">
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
            {foreach $lists as $key => $v}
            <tr>
                <td>{$v.id}</td>
                <td>
                    <figure class="figure img-view" data-img="{$v.image}">
                        <img src="{$v.image|default='/static/images/nopic.png'}?w=100"
                            class="figure-img img-fluid rounded" alt="image">
                    </figure>
                </td>
                <td>{$v.title}</td>
                <td>{$v.url}</td>
                <td>{$v.start_date|showdate}<br />{$v.end_date|showdate}</td>
                <td>{$v.sort}</td>
                <td>{$v.status|show_status|raw}</td>
                <td class="operations">
                    <a class="btn btn-outline-primary" title="编辑"
                        href="{:url('other.adv/itemupdate',array('id'=>$v['id'],'gid'=>$gid))}"><i
                            class="ion-md-create"></i> </a>
                    <a class="btn btn-outline-danger link-confirm" title="删除" data-confirm="您真的确定要删除吗？\n\n删除后将不能恢复!"
                        href="{:url('other.adv/itemdelete',array('id'=>$v['id'],'gid'=>$gid))}"><i
                            class="ion-md-trash"></i> </a>
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
    {$page|raw}
</div>
{/block}