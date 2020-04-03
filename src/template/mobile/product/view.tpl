{extend name="public:base"/}
{block name="head"}
    <link rel="stylesheet" href="__STATIC__/swiper/css/swiper.min.css">
{/block}
{block name="body"}
    <div class="swiper-container swiper-container-horizontal">
        <div class="swiper-wrapper" >
            {volist name="images" id="item" key="k"}
                <div class="swiper-slide" style="background-image:url({$item.image})" >
                </div>
            {/volist}
        </div>
        <!-- Add Arrows -->
        <div class="swiper-button-next" tabindex="0" role="button" aria-label="Next slide" aria-disabled="false"></div>
        <div class="swiper-button-prev" tabindex="0" role="button" aria-label="Previous slide" aria-disabled="false"></div>
        <div class="swiper-pagination swiper-pagination-bullets"></div>
        <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
    </div>
    <div class="weui-form-preview">
        <div class="weui-form-preview__hd">
            <div class="weui-form-preview__item" style="text-align: left;font-size:20px;">
                {$product.title}
            </div>
            <div class="weui-form-preview__item">
                <label class="weui-form-preview__label">商品价格</label>
                <em class="weui-form-preview__value">¥{$product['min_price']}</em>
            </div>
        </div>
        <div class="weui-form-preview__ft">
            <a class="weui-form-preview__btn weui-form-preview__btn_primary" href="{:url('index/order/confirm',['sku_ids'=>implode(',',array_column($skus,'sku_id'))])}">立即购买</a>
        </div>
    </div>
    <div class="weui-cells">
        {foreach name="$product['prop_data']" key="pkey" item="pval"}
        <a class="weui-cell weui-cell_access" href="javascript:;">
            <div class="weui-cell__bd">
                <p>{$pkey}</p>
            </div>
            <div class="weui-cell__ft">{$pval}</div>
        </a>
        {/foreach}
    </div>
    <div class="page__bd">
        <article class="weui-article">
            {$product.content|raw}
        </article>
    </div>
{/block}
{block name="script"}
    <script type="text/javascript" src="__STATIC__/swiper/js/swiper.min.js"></script>
    <script type="text/javascript">
        window.share_imgurl = '{$product.image|local_media}';
        var swiper = new Swiper('.swiper-container', {
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev'
            },
            pagination: {
                el: '.swiper-pagination'
            }
        });
    </script>
{/block}