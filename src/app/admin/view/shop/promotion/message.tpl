{extend name="public:base" /}

{block name="body"}
{include file="public/bread" menu="shop_promotion_index" title="商城配置" /}
<div id="page-wrapper">
    <div class="page-header">消息配置</div>
    <div id="page-content">
    <form method="post" action="" enctype="multipart/form-data">
        <div class="form-row form-group">
            <label for="v-message_bind_agent" class="form-label w-100px text-right align-middle">{$setting['message_bind_agent']['title']}</label>
            <div class="col-5">
                <textarea name="v-message_bind_agent" class="form-control">{$setting['message_bind_agent']['value']}</textarea>
            </div>
            <div class="col-5">
                <span class="text-muted">{$setting['message_bind_agent']['description']}</span>
            </div>
        </div>
        <div class="form-row form-group">
            <label for="v-message_become_agent" class="form-label w-100px text-right align-middle">{$setting['message_become_agent']['title']}</label>
            <div class="col-5">
                <textarea name="v-message_become_agent" class="form-control">{$setting['message_become_agent']['value']}</textarea>
            </div>
            <div class="col-5">
                <span class="text-muted">{$setting['message_become_agent']['description']}</span>
            </div>
        </div>
        <div class="form-row form-group">
            <label for="v-message_upgrade_agent" class="form-label w-100px text-right align-middle">{$setting['message_upgrade_agent']['title']}</label>
            <div class="col-5">
                <textarea name="v-message_upgrade_agent" class="form-control">{$setting['message_upgrade_agent']['value']}</textarea>
            </div>
            <div class="col-5">
                <span class="text-muted">{$setting['message_upgrade_agent']['description']}</span>
            </div>
        </div>
        <div class="form-row form-group">
            <label for="v-message_commission" class="form-label w-100px text-right align-middle">{$setting['message_commission']['title']}</label>
            <div class="col-5">
                <textarea name="v-message_commission" class="form-control">{$setting['message_commission']['value']}</textarea>
            </div>
            <div class="col-5">
                <span class="text-muted">{$setting['message_commission']['description']}</span>
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