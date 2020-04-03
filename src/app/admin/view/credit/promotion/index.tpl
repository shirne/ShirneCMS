{extend name="public:base" /}

{block name="body"}

    {include  file="public/bread" menu="credit_promotion_index" title="积分策略"  /}

    <div id="page-wrapper">
    <div class="page-header">积分商城配置</div>
    <div id="page-content">
    <form method="post" action="" enctype="multipart/form-data">
        <div class="form-row form-group">
            <label for="v-credit_pagetitle" class="form-label w-100px text-right align-middle">{$setting['credit_pagetitle']['title']}</label>
            <div class="col-5">
                <div class="input-group">
                    <input type="text" class="form-control" name="v-credit_pagetitle" value="{$setting['credit_pagetitle']['value']}" placeholder="">
                </div>
            </div>
            <div class="col">
                <div class="text-muted">{$setting['credit_pagetitle']['description']}</div>
            </div>
        </div>
        <div class="form-row form-group">
            <label for="v-credit_keyword" class="form-label w-100px text-right align-middle">{$setting['credit_keyword']['title']}</label>
            <div class="col-5">
                <div class="input-group">
                    <input type="text" class="form-control" name="v-credit_keyword" value="{$setting['credit_keyword']['value']}" placeholder="">
                </div>
            </div>
            <div class="col">
                <div class="text-muted">{$setting['credit_keyword']['description']}</div>
            </div>
        </div>
        <div class="form-row form-group">
            <label for="v-credit_description" class="form-label w-100px text-right align-middle">{$setting['credit_description']['title']}</label>
            <div class="col-5">
                <div class="input-group">
                    <input type="text" class="form-control" name="v-credit_description" value="{$setting['credit_description']['value']}" placeholder="">
                </div>
            </div>
            <div class="col">
                <div class="text-muted">{$setting['credit_description']['description']}</div>
            </div>
        </div>
        <div class="form-row form-group">
            <label for="v-credit_rate" class="form-label w-100px text-right align-middle">{$setting['credit_rate']['title']}</label>
            <div class="col-5">
                <div class="input-group">
                    <input type="text" class="form-control" name="v-credit_rate" value="{$setting['credit_rate']['value']}" placeholder="">
                    <div class="input-group-append">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="text-muted">{$setting['credit_rate']['description']}</div>
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