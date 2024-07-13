{extend name="public:base"/}

{block name="body"}
<div class="page__hd">
    <div class="page__title">{$page.title}</div>
    <div class="page__desc">
    </div>
</div>
<div class="weui-article">
    {$page.content|raw}
</div>
{/block}
{block name="script"}
<script type="text/javascript">

</script>
{/block}