{extend name="public:base" /}

{block name="body"}
<div class="main">

    <div class="container view-body">
        <div class="jumbotron">
            <h1>{$error}</h1>
            <p class="lead">{$description}</p>
            <hr class="my-4">
            <p>去看一下其它内容吧^</p>
            <a class="btn btn-info btn-lg" href="{$redirect}" role="button">回到首页</a>
        </div>
    </div>
</div>
{/block}