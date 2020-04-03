{extend name="public:base" /}
{block name="body"}
    {include  file="member/side"  /}
    <div class="container">

        <ul class="list-group">
            {foreach name="carts" item="cart"}
            <li class="list-group-item">
                <div class="imgbox"><img src="{$cart.product_image}"/> </div>
                <div class="prod-info">
                    <h4>{$cart.product_title}</h4>
                    <div>{$cart.product_price} &times; {$cart.count}</div>
                </div>
            </li>
            {/foreach}
        </ul>
    </div>
{/block}