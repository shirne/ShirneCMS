{extend name="public:base"/}

{block name="body"}
    <div class="main">
        <div class="subbanner">
            <div class="inner" style="background-image:url({:getAdImage('product')})"></div>
        </div>

        <div class="container">
            <div class="product-list">
                <div class="row">
                    {php}$empty='<span class="col-12 empty">暂时没有内容</span>';{/php}
                    {volist name="lists" id="prod" empty="$empty"}
                        <a class="col-6" href="{:url('index/product/view',['id'=>$prod['id']])}">
                            <div class="card">
                                <img class="card-img-top" src="{$prod.image}" alt="Card image cap">
                                <div class="card-body">
                                    <h3 class="card-text">{$prod.title}</h3>
                                    <p class="card-text text-muted">
                                        <span class="float-right"> </span>
                                        <span>￥{$prod|show_price}</span>
                                    </p>
                                </div>
                            </div>
                        </a>
                    {/volist}
                </div>
                {$page|raw}
            </div>
        </div>
    </div>
{/block}