{extend name="public:base" /}

{block name="body"}
{include  file="public/bread" menu="shop_promotion_index" title="商城配置"  /}
<div id="page-wrapper">
    <div class="page-header">商城配置</div>
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