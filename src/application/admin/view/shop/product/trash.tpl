{extend name="public:base" /}

{block name="body"}
{include file="public/bread" menu="shop_product_index" title="回收站" /}
<div id="page-wrapper">

    <div class="row list-header">
        <div class="col-md-6">
            <div class="btn-toolbar list-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                <a href="{:murl('shop.product/index')}" class="btn btn-outline-primary btn-sm mr-2"><i
                        class="ion-md-arrow-back"></i> 返回</a>
                <div class="btn-group btn-group-sm mr-2" role="group" aria-label="check action group">
                    <a href="javascript:" class="btn btn-outline-secondary checkall-btn" data-toggle="button"
                        aria-pressed="false">全选</a>
                    <a href="javascript:" class="btn btn-outline-secondary checkreverse-btn">反选</a>
                </div>
                <div class="btn-group btn-group-sm mr-2" role="group" aria-label="action button group">
                    <a href="javascript:" class="btn btn-outline-secondary action-btn" data-action="restore">恢复</a>
                    <a href="javascript:" class="btn btn-outline-secondary action-btn" data-action="delete">彻底删除</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <form action="{:url('shop.product/index')}" method="post">
                <div class="form-row">
                    <div class="col input-group input-group-sm mr-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text">分类</span>
                        </div>
                        <select name="cate_id" class="form-control">
                            <option value="0">不限分类</option>
                            {foreach $category as $key => $v}
                            <option value="{$v.id}" "{$cate_id==$v['id']?'selected':''}">{$v.html} {$v.title}
                            </option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="col input-group input-group-sm">
                        <input type="text" class="form-control" name="key" value="{$keyword}" placeholder="搜索标题、作者或分类">
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
                <th>图片</th>
                <th>产品名称</th>
                <th>SKU</th>
                <th>发布时间</th>
                <th>分类</th>
                <th>回收日期</th>
                <th width="200">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            {empty name="lists"}{:list_empty(8)}{/empty}
            {volist name="lists" id="v" }
            <tr>
                <td><input type="checkbox" name="id" value="{$v.id}" /></td>
                <td>
                    <figure class="figure img-view" data-img="{$v.image}">
                        <img src="{$v.image|default=' /static/images/nopic.png'}?w=100"
                            class="figure-img img-fluid rounded" alt="image">
                    </figure>
                </td>
                <td>
                    <div>
                        {if $v['type'] > 1}<span class="badge badge-warning">{$types[$v['type']]}</span>{/if}
                        <a href="{:url('index/product/view',['id'=>$v['id']])}" target="_blank">{$v.title}</a>
                    </div>
                    {if !empty($v['unit'])}<span class="badge badge-info">{$v.unit}</span>{/if}
                    <div>
                        <span class="text-muted">销量: {$v.sale}</span>
                    </div>
                </td>
                <td>
                    {foreach $v['skus'] as $key => $sku}
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-prepend">
                            <span class="input-group-text">{$sku.goods_no}</span>
                        </span>
                        <span class="form-control">￥{$sku.price}</span>
                        <span class="input-group-middle">
                            <span class="input-group-text">库存</span>
                        </span>
                        <span class="form-control">{$sku.storage}</span>
                        <span class="input-group-append">
                            <a href="javascript:" data-price="{$sku.price}" data-skuid="{$sku.sku_id}"
                                data-storage="{$sku.storage}" class="btn btn-outline-primary btn-edit-sku"><i
                                    class="ion-md-create"></i></a>
                        </span>
                    </div>
                    {/foreach}
                </td>
                <td>{$v.create_time|showdate}</td>
                <td>{$v.category_title}</td>
                <td>
                    {$v.delete_time|showdate}
                </td>
                <td class="operations">
                    <a class="btn btn-outline-primary  link-confirm" title="恢复" data-confirm="确定恢复产品上架？"
                        href="{:url('shop.product/trash_restore',array('id'=>$v['id']))}"><i class="ion-md-create"></i>
                    </a>


                    <a class="btn btn-outline-danger link-confirm" title="删除" data-confirm="彻底删除产品？\n删除后将不能恢复!"
                        href="{:url('shop.product/trash_del',array('id'=>$v['id']))}"><i class="ion-md-trash"></i> </a>
                </td>
            </tr>
            {/volist}
        </tbody>
    </table>
    <div class="clearfix"></div>
    {$page|raw}

</div>
{/block}
{block name="script"}
<script type="text/javascript" src="__STATIC__/js/location.min.js"></script>
<script type="text/javascript">
    var locobj = new Location();
    (function (w) {
        w.actionRestore = function (ids) {
            dialog.confirm('确定将选中产品恢复？', function () {
                $.ajax({
                    url: "{:url('shop.product/trash_restore',['id'=>'__id__','status'=>1])}".replace('__id__', ids.join(',')),
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
            dialog.confirm('确定彻底删除选中的产品？', function () {
                $.ajax({
                    url: "{:url('shop.product/trash_del',['id'=>'__id__','status'=>0])}".replace('__id__', ids.join(',')),
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
    })(window);
    jQuery(function ($) {



    })
</script>
{/block}