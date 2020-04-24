{extend name="public:base"/}

{block name="body"}
    <div class="main">
        {php}$adimg=getAdImage($topCategory['name']??'article');{/php}
        {if !empty($adimg)}
            <div class="subbanner">
                <div class="inner" style="background-image:url({$adimg})"></div>
            </div>
        {/if}
        <div class="container list-body">
            <div class="row">
            <div class="col-lg-9">
                <ul class="list-group article-list">
                    {php}$empty='<li class="list-group-item empty-box"><p class="empty">暂时没有内容</p></li>';{/php}
                    {volist name="lists" id="art" empty="$empty"}
                        <li class="list-group-item">
                            {if !empty($art['cover'])}
                                <a class="list-img" href="{:url('index/article/view',['id'=>$art['id']])}" style="background-image:url({$art.cover})">
                                    <img class="card-img-top" src="{$art.cover}" alt="Card image cap">
                                </a>
                            {/if}
                            <div class="art-view">
                                <h3><a href="{:url('index/article/view',['id'=>$art['id']])}">{$art.title}</a></h3>
                                <div class="desc">
                                    {$art.description|raw}
                                </div>
                                <div class="text-muted">
                                    <a href="{:url('index/article/index',['name'=>$art['category_name']])}"><span  class="badge badge-secondary">{$art.category_title}</span></a>
                                    <span class="ml-2"><i class="ion-md-time"></i> {$art.create_time|showdate}</span>
                                    <span class="ml-2"><i class="ion-md-paper-plane"></i> {$art.views}</span>
                                    <span class="ml-2" data-anchor="comment"><i class="ion-md-text"></i> {$art.comment}</span>
                                </div>
                            </div>
                        </li>
                    {/volist}
                </ul>
                {$page|raw}
            </div>
            {include  file="article:_left"  /}
            </div>
        </div>
    </div>
{/block}