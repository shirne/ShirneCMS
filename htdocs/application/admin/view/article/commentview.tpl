<extend name="public:base" />

<block name="body">

    <include file="public/bread" menu="article_index" title="查看评论" />

    <div id="page-wrapper">
        <div class="page-header">查看评论</div>
        <div class="page-content">
            <form method="post" action="{:url('article/commentview')}">
                <div class="form-group">
                    <label for="bb">评论内容</label>
                    <div class="form-control">{$model.content}</div>
                </div>
                <div class="form-group">
                    <label for="cc">回复内容</label>
                    <textarea class="form-control" name="reply" cols="30" rows="5">{$model.reply}</textarea>
                </div>
                <div class="form-group">
                    <input type="hidden" name="id" value="{$model.id}">
                    <button type="submit" class="btn btn-primary">回复</button>
                </div>
            </form>
        </div>
    </div>
</block>