{extend name="public:base" /}

{block name="body"}
{include file="public/bread" menu="shop_promotion_index" title="商城配置" /}
<div id="page-wrapper">
    <div class="page-header">
        <a href="{:url('admin/shop.promotion/poster')}" class="float-right btn btn-sm btn-outline-primary">推广海报配置</a>
        <a href="{:url('admin/shop.promotion/share')}" class="float-right btn btn-sm btn-outline-primary mr-1">产品分享配置</a>
        <a href="{:url('admin/shop.promotion/message')}" class="float-right btn btn-sm btn-outline-primary mr-1">消息配置</a>
        商城配置
    </div>
    <div id="page-content">
    <form method="post" action="" enctype="multipart/form-data">
        
        <div class="form-row form-group">
            <label for="v-shop_pagetitle" class="form-label w-100px text-right align-middle">{$setting['shop_pagetitle']['title']}</label>
            <div class="col-5">
                <div class="input-group">
                    <input type="text" class="form-control" name="v-shop_pagetitle" value="{$setting['shop_pagetitle']['value']}" placeholder="">
                </div>
            </div>
            <div class="col">
                <div class="text-muted">{$setting['shop_pagetitle']['description']}</div>
            </div>
        </div>
        <div class="form-row form-group">
            <label for="v-shop_keyword" class="form-label w-100px text-right align-middle">{$setting['shop_keyword']['title']}</label>
            <div class="col-5">
                <div class="input-group">
                    <input type="text" class="form-control" name="v-shop_keyword" value="{$setting['shop_keyword']['value']}" placeholder="">
                </div>
            </div>
            <div class="col">
                <div class="text-muted">{$setting['shop_keyword']['description']}</div>
            </div>
        </div>
        <div class="form-row form-group">
            <label for="v-shop_description" class="form-label w-100px text-right align-middle">{$setting['shop_description']['title']}</label>
            <div class="col-5">
                <div class="input-group">
                    <input type="text" class="form-control" name="v-shop_description" value="{$setting['shop_description']['value']}" placeholder="">
                </div>
            </div>
            <div class="col">
                <div class="text-muted">{$setting['shop_description']['description']}</div>
            </div>
        </div>
        
        <div class="form-row form-group">
            <label for="v-shop_order_pay_limit" class="form-label w-100px text-right align-middle">{$setting['shop_order_pay_limit']['title']}</label>
            <div class="col-5">
                <div class="input-group">
                    <input type="text" class="form-control" name="v-shop_order_pay_limit" value="{$setting['shop_order_pay_limit']['value']}" placeholder="">
                    <span class="input-group-append"><span class="input-group-text">分钟</span></span>
                </div>
            </div>
            <div class="col">
                <div class="text-muted">{$setting['shop_order_pay_limit']['description']}</div>
            </div>
        </div>
        <div class="form-row form-group">
            <label for="v-shop_order_receive_limit" class="form-label w-100px text-right align-middle">{$setting['shop_order_receive_limit']['title']}</label>
            <div class="col-5">
                <div class="input-group">
                    <input type="text" class="form-control" name="v-shop_order_receive_limit" value="{$setting['shop_order_receive_limit']['value']}" placeholder="">
                    <span class="input-group-append"><span class="input-group-text">天</span></span>
                </div>
            </div>
            <div class="col">
                <div class="text-muted">{$setting['shop_order_receive_limit']['description']}</div>
            </div>
        </div>
        <div class="form-row form-group">
            <label for="v-shop_order_refund_limit" class="form-label w-100px text-right align-middle">{$setting['shop_order_refund_limit']['title']}</label>
            <div class="col-5">
                <div class="input-group">
                    <input type="text" class="form-control" name="v-shop_order_refund_limit" value="{$setting['shop_order_refund_limit']['value']}" placeholder="">
                    <span class="input-group-append"><span class="input-group-text">天</span></span>
                </div>
            </div>
            <div class="col">
                <div class="text-muted">{$setting['shop_order_refund_limit']['description']}</div>
            </div>
        </div>
        <div class="form-row form-group">
            <label for="v-shop_close" class="form-label w-100px text-right align-middle">{$setting['shop_close']['title']}</label>
            <div class="col-9 col-md-8 col-lg-6">
                <div class="btn-group btn-group-toggle mregopengroup" data-toggle="buttons">
                    {foreach $setting['shop_close']['data'] as $k => $value}
                        {if $k==$setting['shop_close']['value']}
                            <label class="btn btn-outline-secondary active">
                                <input type="radio" name="v-shop_close" value="{$k}" autocomplete="off" checked> {$value}
                            </label>
                        {else /}
                            <label class="btn btn-outline-secondary">
                                <input type="radio" name="v-shop_close" value="{$k}" autocomplete="off"> {$value}
                            </label>
                        {/if}
                    {/foreach}
                </div>
            </div>
        </div>
        <div class="form-row form-group">
            <label for="v-shop_close_desc" class="form-label w-100px text-right align-middle">{$setting['shop_close_desc']['title']}</label>
            <div class="col-5">
                <div class="input-group">
                    <input type="text" class="form-control" name="v-shop_close_desc" value="{$setting['shop_close_desc']['value']}" placeholder="">
                </div>
            </div>
            <div class="col">
                <div class="text-muted">{$setting['shop_close_desc']['description']}</div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">保存配置</button>
    </form>
    </div>
</div>
{/block}
{block name="script"}
<script type="text/javascript">
    jQuery(function ($) {


    });
</script>
{/block}