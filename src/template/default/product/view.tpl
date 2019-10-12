<extend name="public:base"/>

<block name="body">
    <div class="main">
        <div class="subbanner">
            <div class="inner" style="background-image:url({$product.image})"></div>
        </div>

        <div class="container">
            <div class="card main_right news_list">
                <div class="card-body articlebody">
                    <h1>{$product.title}</h1>
                    <div class="info text-muted">
                        <span class="price">￥{$product|show_price}</span>
                        <a class="btn btn-primary" href="{:url('index/order/confirm',['sku_ids'=>$skus[0]['sku_id']])}">立即购买</a>
                    </div>
                    <div class="container-fluid">
                        {$product.content|raw}
                    </div>
                </div>
            </div>
        </div>
    </div>
</block>
<block name="script">
    <script type="text/javascript">
        window.share_imgurl = '{$product.image|local_media}';
    </script>
</block>