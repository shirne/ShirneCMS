<extend name="Public:Base" />

<block name="body">

<include file="Public/bread" menu="links_index" section="其它" title="链接管理" />

<div id="page-wrapper">
    <div class="page-header">修改链接</div>
    <div class="page-content">
    <form method="post" action="{:url('links/update')}">
        <div class="form-group">
            <label for="aa">链接标题</label>
            <input type="text" name="title" class="form-control" id="aa" value="{$model.title}" placeholder="输入链接标题">
        </div>
        <div class="form-group">
            <label for="bb">链接地址</label>
            <input type="text" name="url" class="form-control" id="bb" value="{$model.url}" placeholder="输入链接标题">
        </div>
        <div class="form-group">
            <label for="cc">优先级</label>
            <input type="text" name="sort" class="form-control" id="cc" value="{$model.sort}" placeholder="越大越靠前" value="100">
        </div>
        <div class="form-group">
            <input type="hidden" name="id" value="{$model.id}">
            <button type="submit" class="btn btn-primary">更新</button>
        </div>
    </form>
    </div>
</div>
</block>