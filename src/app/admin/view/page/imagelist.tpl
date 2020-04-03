{extend name="public:base" /}

{block name="body"}

<include file="public/bread" menu="page_index" title="图集列表" />

<div id="page-wrapper">
    
    <div class="row list-header">
        <div class="col-6">
            <a href="{:url('page/index')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-arrow-back"></i> 返回列表</a>
            <a href="{:url('page/imageadd',array('aid'=>$aid))}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加图片</a>
        </div>
        <div class="col-6">
            <form action="{:url('page/imagelist')}" method="post">
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
                <th>排序</th>
                <th width="160">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        {foreach name="lists" item="v"}
            <tr>
                <td>{$v.id}</td>
                <td><figure class="figure" >
                        <img src="{$v.image}?w=100" class="figure-img img-fluid rounded" alt="image">
                    </figure></td>
                <td>{$v.title}</td>
                <td>{$v.sort}</td>
                <td class="operations">
                    <a class="btn btn-outline-primary" title="编辑" href="{:url('page/imageupdate',array('id'=>$v['id'],'aid'=>$aid))}"><i class="ion-md-create"></i> </a>
                    <a class="btn btn-outline-danger link-confirm" title="删除" data-confirm="您真的确定要删除吗？\n删除后将不能恢复!" href="{:url('page/imagedelete',array('id'=>$v['id'],'aid'=>$aid))}" ><i class="ion-md-trash"></i> </a>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    {$page|raw}
</div>
{/block}