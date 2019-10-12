<extend name="public:base"/>

<block name="body">
    <div class="page__hd">
        <h1 class="page__title">{$page['title']}</h1>
        <div class="page__bd">
            <div class="weui-article">{$page.content|raw}</div>
        </div>
    </div>
</block>
<block name="script">
    <script type="text/javascript">

    </script>
</block>