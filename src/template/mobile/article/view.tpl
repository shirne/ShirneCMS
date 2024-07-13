{extend name="public:base"/}

{block name="body"}
<div class="page__hd">
    <div class="page__title">{$article.title}</div>
    <div class="page__desc">
        <a href="{:url('index/article/index',array('name'=>$category['name']))}">{$category.title}</a>
        &nbsp;&nbsp;
        <i class="ion-md-calendar"></i>&nbsp;{$article.create_time|showdate}
    </div>
</div>
<div class="weui-article">
    {$article.content|raw}
</div>
{/block}
{block name="script"}
<script type="text/javascript">

</script>
{/block}