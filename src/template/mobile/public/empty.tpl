<extend name="public:base" />

<block name="body">
    <div class="page msg_warn js_show">

        <div class="weui-msg">
            <div class="jumbotron">
                <h1>{$error}</h1>
                <p class="lead">{$description}</p>
                <hr class="my-4">
                <p>点击链接去看一下其它内容吧^</p>
                <a class="btn btn-primary btn-lg" href="{$redirect}" role="button">回到首页</a>
            </div>
        </div>
    </div>
</block>
