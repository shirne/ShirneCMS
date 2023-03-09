{extend name="public:base"/}

{block name="body"}
    <div class="weui-flex full-container">
        <div class="left-cate">
            <ul>
                {volist name="categories[0]" id="c"}
                    <li><a class="cate-link{$c['id']==$category['id']?' active':''}" href="{:url('index/product/index',['name'=>$c['name']])}">{$c.short}</a> </li>
                {/volist}
            </ul>
        </div>
        <div class="weui-flex__item main-list">
            {if !empty($category['image'])}
                <div class="cate-banner">
                    <img class="media-img" src="{$category.image}" alt="{$category.name}">
                </div>
            {/if}
            <ul class="weui-flex">
                {php}$empty='<li class="weui-flex__item empty">暂时没有内容</li>';{/php}
                {volist name="lists" id="product" empty="$empty"}
                    <li class="weui-flex__item">
                        <div class="prod-item">
                            {if !empty($product['image'])}
                                <a class="d-view" href="{:url('index/product/view',['id'=>$product['id']])}">
                                    <img class="media-img" src="{$product.image}?w=136&h=90" alt="{$product.title}">
                                </a>
                            {/if}
                            <div class="d-info">
                                <h3><a href="{:url('index/product/view',['id'=>$product['id']])}">{$product.title}</a></h3>
                            </div>
                        </div>
                    </li>
                {/volist}
            </ul>
        </div>
    </div>
{/block}
{block name="script"}
    <script type="text/javascript">

    </script>
{/block}