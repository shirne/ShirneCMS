<extend name="Public:Base" />

<block name="body">

<include file="Public/bread" menu="feedback_index" section="其它" title="留言管理" />

<div id="page-wrapper">
    <div class="page-header">查看留言</div>
    <div class="page-content">
        <form method="post" action="{:url('feedback/reply')}">
            <div class="form-group">
                <label for="bb">留言内容</label>
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