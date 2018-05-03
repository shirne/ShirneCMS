<extend name="public:base" />

<block name="body">

<include file="public/bread" menu="links_index" title="链接信息" />

<div id="page-wrapper">
    <div class="page-header">{$id>0?'编辑':'添加'}链接</div>
    <div class="page-content">
    <form method="post" action="">
        <div class="form-group">
            <label for="title">链接标题</label>
            <input type="text" name="title" class="form-control" value="{$model.title}" placeholder="输入链接标题">
        </div>
        <div class="form-group">
            <label for="url">链接地址</label>
            <input type="text" name="url" class="form-control" value="{$model.url}" placeholder="输入链接标题">
        </div>
        <div class="form-group">
            <label for="sort">优先级</label>
            <input type="text" name="sort" class="form-control" value="{$model.sort}" placeholder="越小越靠前" >
        </div>
        <div class="form-group">
            <input type="hidden" name="id" value="{$model.id}">
            <button type="submit" class="btn btn-primary">{$id>0?'保存':'添加'}</button>
        </div>
    </form>
    </div>
</div>
</block>