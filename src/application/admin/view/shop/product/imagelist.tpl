{extend name="public:base" /}

{block name="body"}

{include file="public/bread" menu="shop_product_index" title="图集列表" /}

<div id="page-wrapper">

    <div class="row list-header">
        <div class="col-6">
            <a href="{:url('shop.product/index')}" class="btn btn-outline-primary btn-sm"><i
                    class="ion-md-arrow-back"></i> 返回列表</a>
            <a href="{:url('shop.product/imageadd',array('aid'=>$aid))}" class="btn btn-outline-primary btn-sm"><i
                    class="ion-md-add"></i> 添加图片</a>
        </div>
        <div class="col-6">
            <form action="{:url('product/imagelist')}" method="post">
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
                <th width="200">操作</th>
            </tr>
        </thead>
        <tbody>
            {foreach $lists as $key => $v}
            <tr>
                <td>{$v.id}</td>
                <td>
                    <figure class="figure">
                        <img src="{$v.image|default='/static/images/nopic.png'}?w=100"
                            class="figure-img img-fluid rounded" alt="image">
                    </figure>
                </td>
                <td>{$v.title}</td>
                <td>{$v.sort}</td>
                <td>
                    <a class="btn btn-outline-dark btn-sm"
                        href="{:url('shop.product/imageupdate',array('id'=>$v['id'],'aid'=>$aid))}"><i
                            class="ion-md-create"></i> 编辑</a>
                    <a class="btn btn-outline-dark btn-sm"
                        href="{:url('shop.product/imagedelete',array('id'=>$v['id'],'aid'=>$aid))}"
                        onclick="javascript:return del(this,'您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="ion-md-trash"></i>
                        删除</a>
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
    {$page|raw}
</div>
{/block}