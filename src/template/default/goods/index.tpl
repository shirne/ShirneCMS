{extend name="public:base"/}

{block name="body"}
<div class="main">
    {php}$image = getAdImage('goods');{/php}
    {if !empty($image)}
    <div class="subbanner">
        <div class="inner" style="background-image:url({$image})"></div>
    </div>
    {/if}

    <div class="container" style="padding:0;">
        <div class="goods-list">
            {volist name="categories[0]" id="cate"}
            <goods:list var="prodlist" category="$cate['id']" limit="100" />
            {if !empty($prodlist)}
            <div class="card mt-3">
                <div class="card-header">{$cate.title}</div>
                <div class="card-body">

                    <div class="row">
                        {php}$empty='<span class="col-12 empty">暂时没有内容</span>';{/php}
                        {volist name="prodlist" id="prod" empty="$empty"}
                        <a class="col-6" href="{:url('index/goods/view',['id'=>$prod['id']])}">
                            <div class="card proditem">
                                <img class="card-img-top" src="{$prod.image|media}" alt="{$prod.title}">
                                <div class="card-body">
                                    <h3 class="card-text">{$prod.title}</h3>
                                    <p class="card-text price">
                                        <span class="float-right"> </span>
                                        <span>{$prod.price|number_format} 积分/{$prod.vice_title|default='件'}</span>
                                    </p>
                                </div>
                            </div>
                        </a>
                        {/volist}
                    </div>
                </div>
            </div>
            {/if}
            {/volist}
        </div>
    </div>
</div>
{/block}