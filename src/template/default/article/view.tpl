{extend name="public:base"/}

{block name="body"}
<div class="main">
    <div class="container">
        <div class="article-body">
            <h1 class="article-title">{$article.title}</h1>
            <div class="article-info text-muted text-center">
                <a href="{:url('index/article/index',array('name'=>$category['name']))}">{$category.title}</a>
                &nbsp;&nbsp;
                <i class="ion-md-calendar"></i>&nbsp;{$article.create_time|showdate}
                <i class="ion-md-paper-plane"></i>&nbsp;{$article.views}
            </div>
            <div class="article-content">
                <div>
                    {$article.content|raw}
                </div>
            </div>
        </div>
    </div>
</div>
{/block}