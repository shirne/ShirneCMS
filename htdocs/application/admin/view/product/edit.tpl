<extend name="public:base" />

<block name="body">
<include file="public/bread" menu="product_index" title="商品详情" />
<div id="page-wrapper">
    <div class="page-header">{$id>0?'编辑':'添加'}商品</div>
    <div id="page-content">
    <form method="post" action="" enctype="multipart/form-data">
        <div class="form-row">
            <div class="col">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">商品名称</span> </div>
                        <input type="text" name="title" class="form-control" value="{$product.title}" id="product-title" placeholder="输入商品名称">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">商品货号</span> </div>
                        <input type="text" name="goods_no" class="form-control" value="{$product.goods_no}" id="product-goods_no" placeholder="输入商品货号">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">商品分类</span> </div>
                        <select name="cate_id" id="product-cate" class="form-control">
                            <foreach name="category" item="v">
                                <option value="{$v.id}" {$product['cate_id'] == $v['id']?'selected="selected"':""}>{$v.html} {$v.title}</option>
                            </foreach>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">商品主图</span>
                        </div>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="upload_cover"/>
                            <label class="custom-file-label" for="upload_cover">选择文件</label>
                        </div>
                    </div>
                    <if condition="$product['cover']">
                        <figure class="figure">
                            <img src="{$product.cover}" class="figure-img img-fluid rounded" alt="image">
                            <figcaption class="figure-caption text-center">{$product.cover}</figcaption>
                        </figure>
                        <input type="hidden" name="delete_cover" value="{$product.cover}"/>
                    </if>
                </div>
            </div>
            <div class="col-4">
                <div class="card form-group">
                    <div class="card-header">商品属性</div>
                    <div class="card-body">
                        <div class="form-row">
                            <label class="col-3">商品类型</label>
                            <div class="form-group col">
                                <div class="btn-group btn-group-toggle btn-group-sm" data-toggle="buttons">
                                    <volist name="types" id="type" key="k">
                                        <label class="btn btn-outline-secondary{$k==$product['type']?' active':''}">
                                            <input type="radio" name="type" value="{$k}" autocomplete="off" {$k==$product['type']?'checked':''}>{$type}
                                        </label>
                                    </volist>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <label class="col-3">支持折扣</label>
                            <div class="form-group col">
                                <div class="btn-group btn-group-toggle btn-group-sm" data-toggle="buttons">
                                    <label class="btn btn-outline-secondary{$product['is_discount']==1?' active':''}">
                                        <input type="radio" name="is_discount" value="1" autocomplete="off" {$product['is_discount']==1?'checked':''}>支持
                                    </label>
                                    <label class="btn btn-outline-secondary{$product['is_discount']==0?' active':''}">
                                        <input type="radio" name="is_discount" value="0" autocomplete="off" {$product['is_discount']==0?'checked':''}>不支持
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <label class="col-3">支持分佣</label>
                            <div class="form-group col">
                                <div class="btn-group btn-group-toggle btn-group-sm" data-toggle="buttons">
                                    <label class="btn btn-outline-secondary{$product['is_commission']==1?' active':''}">
                                        <input type="radio" name="is_commission" value="1" autocomplete="off" {$product['is_commission']==1?'checked':''}>支持
                                    </label>
                                    <label class="btn btn-outline-secondary{$product['is_commission']==0?' active':''}">
                                        <input type="radio" name="is_commission" value="0" autocomplete="off" {$product['is_commission']==0?'checked':''}>不支持
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <label class="col-3">限制购买</label>
                            <div class="form-group col">
                                <div class="btn-group btn-group-toggle btn-group-sm" data-toggle="buttons">
                                    <volist name="levels" id="lv" key="k">
                                        <label class="btn btn-outline-secondary{:fix_in_array($k,$product['levels'])?' active':''}">
                                            <input type="checkbox" name="levels[]" value="{$k}" autocomplete="off" {:fix_in_array($k,$product['levels'])?'checked':''}>{$lv.level_name}
                                        </label>
                                    </volist>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th scope="col">规格货号</th>
                    <th scope="col">重量(克)</th>
                    <th scope="col">销售价</th>
                    <th scope="col">市场价</th>
                    <th scope="col">成本价</th>
                    <th scope="col">库存</th>
                </tr>
                </thead>
                <tbody>
                    <foreach name="skus" item="sku" key="k">
                    <tr>
                        <td>
                            <input type="hidden" name="skus[{$k}][sku_id]" value="{$sku.sku_id}"/>
                            <input type="text" class="form-control" name="skus[{$k}][goods_no]" value="{$sku.goods_no}">
                        </td>
                        <td><input type="text" class="form-control" name="skus[{$k}][weight]" value="{$sku.weight}"> </td>
                        <td><input type="text" class="form-control" name="skus[{$k}][price]" value="{$sku.price}"> </td>
                        <td><input type="text" class="form-control" name="skus[{$k}][market_price]" value="{$sku.market_price}"> </td>
                        <td><input type="text" class="form-control" name="skus[{$k}][cost_price]" value="{$sku.cost_price}"> </td>
                        <td><input type="text" class="form-control" name="skus[{$k}][storage]" value="{$sku.storage}"> </td>
                    </tr>
                    </foreach>
                </tbody>
            </table>
        </div>

        <div class="form-group">
            <label for="product-content">商品介绍</label>
            <script id="product-content" name="content" type="text/plain">{$product.content|raw}</script>
        </div>

        <input type="hidden" name="id" value="{$product.id}">
        <button type="submit" class="btn btn-primary">{$id>0?'保存':'添加'}</button>
    </form>
        </div>
</div>
    </block>
<block name="script">
<!-- 配置文件 -->
<script type="text/javascript" src="__STATIC__/ueditor/ueditor.config.js"></script>
<!-- 编辑器源码文件 -->
<script type="text/javascript" src="__STATIC__/ueditor/ueditor.all.min.js"></script>
<!-- 实例化编辑器 -->
<script type="text/javascript">
    var ue = UE.getEditor('product-content',{
        toolbars: Toolbars.normal,
        initialFrameHeight:500,
        zIndex:100
    });
</script>
</block>