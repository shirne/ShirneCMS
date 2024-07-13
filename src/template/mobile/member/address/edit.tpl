{extend name="public:base" /}
{block name="body"}
<div class="page">
    <div class="page__hd">
        <h1>收货地址</h1>
    </div>
    <div class="page__bd">
        <form role="form" method="post" action="">
            <div class="weui-cells weui-cells_form">
                <div class="weui-cell">
                    <div class="weui-cell__hd"><label class="weui-label">收货人</label></div>
                    <div class="weui-cell__bd">
                        <input class="weui-input" type="number" name="receive_name" value="{$address.receive_name}">
                    </div>
                </div>
                <div class="weui-cell weui-cells_form">
                    <div class="weui-cell__hd">
                        <label class="weui-label">手机号</label>
                    </div>
                    <div class="weui-cell__bd">
                        <input class="weui-input" type="tel" name="mobile" value="{$address.mobile}"
                            placeholder="请输入手机号">
                    </div>
                </div>
                <div class="weui-cell weui-cell_select weui-cell_select-after">
                    <div class="weui-cell__hd"><label for="" class="weui-label">所在地区</label></div>
                    <div class="weui-cell__bd">
                        <select class="weui-select" name=""></select>
                    </div>
                </div>
                <div class="weui-cell weui-cells_form">
                    <div class="weui-cell__hd">
                        <label class="weui-label">邮政编码</label>
                    </div>
                    <div class="weui-cell__bd">
                        <input class="weui-input" type="text" name="code" value="{$address.code}">
                    </div>
                </div>
                <div class="weui-cell weui-cells_form">
                    <div class="weui-cell__hd">
                        <label class="weui-label">详细地址</label>
                    </div>
                    <div class="weui-cell__bd">
                        <input class="weui-input" type="text" name="address" value="{$address.address}">
                    </div>
                </div>
            </div>
            <div class="weui-cells weui-cells_checkbox">
                <label class="weui-cell weui-check__label" for="s11">
                    <div class="weui-cell__hd">
                        <input type="checkbox" class="weui-check" name="is_default" id="s11"
                            {$address['is_default']?'checked':''}>
                        <i class="weui-icon-checked"></i>
                    </div>
                    <div class="weui-cell__bd">
                        <p>设为默认</p>
                    </div>
                </label>
            </div>
            <div class="weui-btn-area">
                <button class="weui-btn weui-btn_primary" type="submit" id="showTooltips">提交保存</button>
            </div>
        </form>
    </div>
</div>
{/block}
{block name="script"}
<script type="text/javascript" src="__STATIC__/js/location.js"></script>
<script type="text/javascript" src="__STATIC__/js/ChinaArea.js"></script>
<script type="text/javascript">
    jQuery(function ($) {
        $("#ChinaArea").jChinaArea({
            aspnet: true,
            s1: "{$model.province}",
            s2: "{$model.city}",
            s3: "{$model.area}"
        });
    })
</script>
{/block}