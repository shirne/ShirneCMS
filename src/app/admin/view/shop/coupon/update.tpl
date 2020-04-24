{extend name="public:base" /}

{block name="body"}

{include  file="public/bread" menu="shop_coupon_index" title="优惠券设置"  /}

<div id="page-wrapper">
    <div class="page-header">{$id>0?'编辑':'添加'}优惠券</div>
    <div class="page-content">
    <form method="post" class="page-form" action="">
        <div class="form-group">
            <label for="title">优惠券名称</label>
            <input type="text" name="title" class="form-control" value="{$model.title|default=''}" placeholder="优惠券名称">
        </div>
        <div class="form-row">
            <label for="title" class="col-3 col-md-2 col-lg-1">类型</label>
            <div class="form-group col">
                <div class="btn-group btn-group-toggle btn-group-sm" data-toggle="buttons">
                    <label class="btn btn-outline-secondary{$model['bind_type']=='0'?' active':''}">
                        <input type="radio" name="bind_type" value="0" autocomplete="off" {$model['bind_type']=='0'?'checked':''}>通用
                    </label>
                    <label class="btn btn-outline-secondary{$model['bind_type']=='1'?' active':''}">
                        <input type="radio" name="bind_type" value="1" autocomplete="off" {$model['bind_type']=='1'?'checked':''}>类目
                    </label>
                    <label class="btn btn-outline-secondary{$model['bind_type']=='2'?' active':''}">
                        <input type="radio" name="bind_type" value="2" autocomplete="off" {$model['bind_type']=='2'?'checked':''}>品牌
                    </label>
                    <label class="btn btn-outline-secondary{$model['bind_type']=='3'?' active':''}">
                        <input type="radio" name="bind_type" value="3" autocomplete="off" {$model['bind_type']=='3'?'checked':''}>商品
                    </label>
                    <label class="btn btn-outline-secondary{$model['bind_type']=='4'?' active':''}">
                        <input type="radio" name="bind_type" value="4" autocomplete="off" {$model['bind_type']=='4'?'checked':''}>SKU
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group bindtype btype_1">
            <label for="title">类目</label>
            <div class="input-group">
                <select name="cate_id" class="form-control" >
                    {foreach name="category" item="v"}
                        <option value="{$v.id}" data-pid="{$v['pid']}" {$model['cate_id']??0 == $v['id']?'selected="selected"':""}>{$v.html} {$v.title}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="form-group bindtype btype_2">
            <label for="title">品牌</label>
            <div class="input-group">
                <span class="form-control">{$brand.title|default=''}</span>
                <input type="hidden" name="brand_id" value="{$model.brand_id|default=''|number_empty}">
                <div class="input-group-append">
                    <a href="javascript:" class="btn btn-outline-secondary btn-pick-brand">选择品牌</a>
                </div>
            </div>
        </div>
        <div class="form-group bindtype btype_3">
            <label for="title">商品</label>
            <div class="input-group">
                <span class="form-control">{$product.title|default=''}</span>
                <input type="hidden" name="product_id" value="{$model.product_id|default=''|number_empty}">
                <div class="input-group-append">
                    <a href="javascript:" class="btn btn-outline-secondary btn-pick-product">选择商品</a>
                </div>
            </div>
        </div>
        <div class="form-group bindtype btype_4">
            <label for="title">SKU</label>
            <div class="input-group">
                <span class="form-control">{$product.title|default=''}/{$sku.goods_no|default=''}</span>
                <input type="hidden" name="sku_id" value="{$model.sku_id|default=''|number_empty}">
                <div class="input-group-append">
                    <a href="javascript:" class="btn btn-outline-secondary btn-pick-sku">选择SKU</a>
                </div>
            </div>
        </div>
        <div class="form-row">
            <label for="type" class="col-3 col-md-2 col-lg-1">优惠类型</label>
            <div class="form-group col">
                <div class="btn-group btn-group-toggle btn-group-sm" data-toggle="buttons">
                    <label class="btn btn-outline-secondary{$model['type']=='1'?' active':''}">
                        <input type="radio" name="type" value="1" autocomplete="off" {$model['type']=='1'?'checked':''}>折扣
                    </label>
                    <label class="btn btn-outline-secondary{$model['type']=='0'?' active':''}">
                        <input type="radio" name="type" value="0" autocomplete="off" {$model['type']=='0'?'checked':''}>满减
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group cptype cptype_0">
            <label for="limit">优惠额度</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">满</div>
                </div>
                <input type="text" class="form-control" name="limit" value="{$model.limit|default=''|number_empty}">
                <div class="input-group-middle">
                    <div class="input-group-text">减</div>
                </div>
                <input type="text" class="form-control" name="amount" value="{$model.amount|default=0|showmoney|number_empty}">
            </div>
        </div>
        <div class="form-group cptype cptype_1">
            <label for="disount">优惠折扣</label>
            <div class="input-group">
                <input type="text" class="form-control" name="discount" value="{$model.discount|default=''|number_empty}">
                <div class="input-group-append">
                    <div class="input-group-text">%</div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="start_date">有效期</label>
            <div class="form-row date-range">
                <div class="input-group col">
                    <div class="input-group-prepend">
                        <span class="input-group-text">从</span>
                    </div>
                    <input type="text" name="start_date" class="form-control fromdate" value="{$model.start_date|default=''|showdate=''}" />
                </div>
                <div class="input-group col">
                    <div class="input-group-prepend">
                        <span class="input-group-text">至</span>
                    </div>
                    <input type="text" name="end_date" class="form-control todate" value="{$model.end_date|default=''|showdate=''}" />
                </div>
            </div>
        </div>

        <div class="form-row">
            <label for="level_1" class="col-3 col-md-2 col-lg-1">级别限制</label>
            <div class="form-group col">
                <div class="btn-group btn-group-toggle btn-group-sm" data-toggle="buttons">
                    {volist name="levels" id="lv" key="k"}
                        <label class="btn btn-outline-secondary{:fix_in_array($k,$model['levels_limit']??[])?' active':''}">
                            <input type="checkbox" id="level_{$k}" name="levels_limit[]" value="{$k}" autocomplete="off" {:fix_in_array($k,$model['levels_limit']??[])?'checked':''}>{$lv.level_name}
                        </label>
                    {/volist}
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="count_limit">数量限制</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">每会员最多领取</span>
                </div>
                <input type="text" name="count_limit" class="form-control" value="{$model.count_limit|default=''|number_empty}" placeholder="不填写(或0)则不限制领取数量" />
            </div>
        </div>

        <div class="form-row">
            <div class="col form-group">
                <label for="stock">数量</label>
                <input type="text" name="stock" class="form-control" value="{$model.stock|default=''}" placeholder="填写 -1 不限制数量" />
            </div>
            <div class="col form-group">
                <label for="sort">排序</label>
                <input type="text" name="sort" class="form-control" value="{$model.sort|default=''}" />
            </div>
        </div>

        <div class="form-row">
            <label for="expiry_type" class="col-3 col-md-2 col-lg-1">失效方式</label>
            <div class="form-group col">
                <div class="btn-group btn-group-toggle btn-group-sm" data-toggle="buttons">
                    <label class="btn btn-outline-secondary{$model['expiry_type']=='1'?' active':''}">
                        <input type="radio" name="expiry_type" value="1" autocomplete="off" {$model['expiry_type']=='1'?'checked':''}>固定日期
                    </label>
                    <label class="btn btn-outline-secondary{$model['expiry_type']=='0'?' active':''}">
                        <input type="radio" name="expiry_type" value="0" autocomplete="off" {$model['expiry_type']=='0'?'checked':''}>领取后计天数
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group exptype exptype_1">
            <label for="expiry_time">有效期</label>
            <div class="input-group">
                <input type="text" class="form-control datepicker"  name="expiry_time" value="{$model.expiry_time|default=''|showdate}">
                <div class="input-group-append">
                    <div class="input-group-text">前</div>
                </div>
            </div>
        </div>
        <div class="form-group exptype exptype_0">
            <label for="expiry_day">有效期</label>
            <div class="input-group">
                <input type="number" class="form-control " name="expiry_day" value="{$model.expiry_day|default=''}">
                <div class="input-group-append">
                    <div class="input-group-text">天</div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="cc">状态</label>
            <label class="radio-inline">
                <input type="radio" name="status" value="1" {$model['status']==1?'checked="checked"':''} >显示
            </label>
            <label class="radio-inline">
                <input type="radio" name="status" value="0" {$model['status']==0?'checked="checked"':''}>隐藏
            </label>
        </div>
        <div class="form-group submit-btn">
            <input type="hidden" name="id" value="{$model.id|default=''}">
            <button type="submit" class="btn btn-primary">{$id>0?'保存':'添加'}</button>
        </div>
    </form>
    </div>
</div>
{/block}
{block name="script"}
    <script type="text/javascript">
        radio_tab('[name=bind_type]','.bindtype','btype_');
        radio_tab('[name=type]','.cptype','cptype_');
        radio_tab('[name=expiry_type]','.exptype','exptype_');

        $('.btn-pick-brand').click(function (e) {
            var box = $(this).parents('.input-group');
            var input = box.find('input[type=hidden]');
            var title = box.find('span.form-control');
            dialog.pickList({
                url:"{:url('productBrand/search')}",
                rowTemplate:'<a href="javascript:" data-id="{@id}" class="list-group-item list-group-item-action">[{@id}]&nbsp; {@title}</a>'
            },function (brand) {
                title.text(brand.title);
                input.val(brand.id);
            });
        });
        $('.btn-pick-product').click(function (e) {
            var box = $(this).parents('.input-group');
            var input = box.find('input[type=hidden]');
            var title = box.find('span.form-control');
            dialog.pickProduct(function (product) {
                title.text(product.title);
                input.val(product.id);
            });
        });
        $('.btn-pick-sku').click(function (e) {
            var box = $(this).parents('.input-group');
            var input = box.find('input[type=hidden]');
            var title = box.find('span.form-control');
            dialog.pickProduct(function (product) {
                title.text(product.title+'/'+product.goods_no);
                input.val(product.sku_id);
                $('[name=product_id]').val(product.id)
                    .parents('.input-group').find('span.form-control').text(product.title);
            },{searchtype:'sku'});
        });
    </script>
{/block}