<extend name="public:base" />

<block name="body">

<include file="public/bread" menu="product_coupon_index" title="优惠券设置" />

<div id="page-wrapper">
    <div class="page-header">{$id>0?'编辑':'添加'}优惠券</div>
    <div class="page-content">
    <form method="post" class="page-form" action="">
        <div class="form-group">
            <label for="title">优惠券名称</label>
            <input type="text" name="title" class="form-control" value="{$model.title}" placeholder="优惠券名称">
        </div>
        <div class="form-row">
            <label for="title" class="col-3">类型</label>
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
                <input type="text" class="form-control" name="cate_id" value="{$model.cate_id}">
                <div class="input-group-append">
                    <a href="" class="btn btn-outline-secondary">选择类目</a>
                </div>
            </div>
        </div>
        <div class="form-group bindtype btype_2">
            <label for="title">品牌</label>
            <div class="input-group">
                <input type="text" class="form-control" name="brand_id" value="{$model.brand_id}">
                <div class="input-group-append">
                    <a href="" class="btn btn-outline-secondary">选择品牌</a>
                </div>
            </div>
        </div>
        <div class="form-group bindtype btype_3">
            <label for="title">商品</label>
            <div class="input-group">
                <input type="text" class="form-control" name="product_id" value="{$model.product_id}">
                <div class="input-group-append">
                    <a href="" class="btn btn-outline-secondary">选择商品</a>
                </div>
            </div>
        </div>
        <div class="form-group bindtype btype_4">
            <label for="title">SKU</label>
            <div class="input-group">
                <input type="text" class="form-control" name="sku_id" value="{$model.sku_id}">
                <div class="input-group-append">
                    <a href="" class="btn btn-outline-secondary">选择SKU</a>
                </div>
            </div>
        </div>
        <div class="form-row">
            <label for="title" class="col-3">优惠类型</label>
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
        <div class="form-group cptype cptype_1">
            <label for="title">优惠额度</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">满</div>
                </div>
                <input type="text" class="form-control" name="limit" value="{$model.limit}">
                <div class="input-group-middle">
                    <div class="input-group-text">减</div>
                </div>
                <input type="text" class="form-control" name="amount" value="{$model.amount|showmoney}">
            </div>
        </div>
        <div class="form-group cptype cptype_0">
            <label for="title">优惠折扣</label>
            <div class="input-group">
                <input type="text" class="form-control" name="disount" value="{$model.disount}">
                <div class="input-group-append">
                    <div class="input-group-text">%</div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="image">有效期</label>
            <div class="form-row date-range">
                <div class="input-group col">
                    <div class="input-group-prepend">
                        <span class="input-group-text">从</span>
                    </div>
                    <input type="text" name="start_date" class="form-control fromdate" value="{$model.start_date|showdate=''}" />
                </div>
                <div class="input-group col">
                    <div class="input-group-prepend">
                        <span class="input-group-text">至</span>
                    </div>
                    <input type="text" name="end_date" class="form-control todate" value="{$model.end_date|showdate=''}" />
                </div>
            </div>

        </div>
        <div class="form-row">
            <div class="col form-group">
                <label for="url">数量</label>
                <input type="text" name="stock" class="form-control" value="{$model.stock}" />
            </div>
            <div class="col form-group">
                <label for="image">排序</label>
                <input type="text" name="sort" class="form-control" value="{$model.sort}" />
            </div>
        </div>

        <div class="form-row">
            <label for="title" class="col-3">失效方式</label>
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
            <label for="title">有效期</label>
            <div class="input-group">
                <input type="text" class="form-control" name="expiry_time" value="{$model.expiry_time|showdate}">
                <div class="input-group-append">
                    <div class="input-group-text">前</div>
                </div>
            </div>
        </div>
        <div class="form-group exptype exptype_0">
            <label for="title">有效期</label>
            <div class="input-group">
                <input type="number" class="form-control" name="expiry_day" value="{$model.expiry_day}">
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
            <input type="hidden" name="id" value="{$model.id}">
            <button type="submit" class="btn btn-primary">{$id>0?'保存':'添加'}</button>
        </div>
    </form>
    </div>
</div>
</block>
<block name="script">
    <script type="text/javascript">
        radio_tab('[name=bind_type]','.bindtype','btype_');
        radio_tab('[name=type]','.cptype','cptype_');
        radio_tab('[name=expiry_type]','.exptype','exptype_');
    </script>
</block>