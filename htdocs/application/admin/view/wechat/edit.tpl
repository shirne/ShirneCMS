<extend name="public:base" />

<block name="body">

    <include file="public/bread" menu="wechat_index" title="公众号信息" />

    <div id="page-wrapper">
        <div class="page-header">{$id>0?'编辑':'添加'}公众号</div>
        <div class="page-content">
            <form method="post" action="">
                <div class="form-group">
                    <label for="title">名称</label>
                    <input type="text" name="title" class="form-control" value="{$model.title}" placeholder="输入公众号名称">
                </div>
                <div class="form-group">
                    <label for="url">APPID</label>
                    <input type="text" name="appid" class="form-control" value="{$model.appid}">
                </div>
                <div class="form-group">
                    <label for="sort">APPSecret</label>
                    <input type="text" name="appsecret" class="form-control" value="{$model.appsecret}">
                </div>
                <div class="form-group">
                    <label for="sort">Token</label>
                    <div class="input-group">
                    <input type="text" name="token" class="form-control" value="{$model.token}">
                    <div class="input-group-append"><a href="javascript:" class="btn btn-outline-secondary">随机生成</a> </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="sort">AESKey</label>
                    <div class="input-group">
                    <input type="text" name="encodingaeskey" class="form-control" value="{$model.encodingaeskey}">
                        <div class="input-group-append"><a href="javascript:" class="btn btn-outline-secondary">随机生成</a> </div>
                    </div>
                </div>
                <div class="form-group">
                    <input type="hidden" name="id" value="{$model.id}">
                    <button type="submit" class="btn btn-primary">{$id>0?'保存':'添加'}</button>
                </div>
            </form>
        </div>
    </div>
</block>