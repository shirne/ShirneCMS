{extend name="public:base"/}

{block name="body"}
    <div class="main">
        <div class="subbanner">
            <div class="inner" style="height: 300px;background-image:url({$goods.image|media})"></div>
        </div>

        <div class="goods-container">
            <div class="card main_right news_list">
                <div class="card-body articlebody">
                    <h1>{$goods.title}</h1>
                    <div class="row info">
                        <div class="col">
                            <span class="text-danger price">{$goods.price|number_format} 积分/{$goods.vice_title|default='件'}</span>
                            <div class="text-muted">{$goods.vice_title}</div>
                        </div>
                        <div class="col-auto">
                            <a class="btn btn-primary" href="{:url('index/creditOrder/confirm',['goods_ids'=>$goods['id']])}">立即兑换</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                {$goods.content|raw}
            </div>
        </div>
    </div>
{/block}
{block name="script"}
    <script type="text/javascript">
        window.share_imgurl = '{$goods.image|local_media}';
    </script>
{/block}