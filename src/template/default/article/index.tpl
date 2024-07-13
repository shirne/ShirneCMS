{extend name="public:base"/}

{block name="body"}
<div class="main">
    <div class="subbanner">
        <div class="inner" style="background-image:url({:getAdImage('news')})"></div>
    </div>

    <div class="nav-row">
        <div class="container">
            <div class="row">
                {php}
                if(empty($category)){ $catelist=$categories[0]; }
                else { $catelist=$categories[$category['pid']];}
                {/php}
                {volist name="catelist" id="c"}
                <a class="col row-item {$c['name']==$category['name']?'active':''}"
                    href="{:url('index/article/index',['name'=>$c['name']])}">{$c.title}</a>
                {/volist}
            </div>
        </div>
    </div>

    <div class="container">
        <div class="article-list">
            <ul class="row list-unstyled">
                {php}$empty='<li class="col-12 empty">暂时没有内容</li>';{/php}
                {volist name="lists" id="article" empty="$empty"}
                <li class="col-6">
                    <div class="media">
                        {if !empty($article['cover'])}
                        <img class="media-img" src="{$article.cover}" alt="{$article.title}">
                        {/if}
                        <div class="media-body">
                            <h5 class="mt-0 mb-1">
                                <a target="_blank"
                                    href="{:url('index/article/view',array('id'=>$article['id']))}">{$article.title}</a>
                            </h5>
                            <div>
                                <p>{$article.content|cutstr=80}</p>
                                <p><span class="float-right"><i class="ion-md-calendar"></i>
                                        {$article.create_time|showdate}</span> </p>
                            </div>
                        </div>
                    </div>
                </li>
                {/volist}
            </ul>
            {$page|raw}
        </div>
    </div>
</div>
{/block}