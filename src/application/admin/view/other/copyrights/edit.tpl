{extend name="public:base" /}

{block name="body"}

{include file="public/bread" menu="copyrights_index" title="版权署名信息" /}

<div id="page-wrapper">
    <div class="page-header">{$id>0?'编辑':'添加'}版权署名</div>
    <div class="page-content">
        <form method="post" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">版权署名</label>
                <input type="text" name="title" class="form-control" value="{$model.title|default=''}"
                    placeholder="输入版权署名">
            </div>
            <div class="form-group">
                <label for="sort">排序</label>
                <input type="text" name="sort" class="form-control" value="{$model.sort|default=''}">
            </div>
            <div class="form-group">
                <label for="content">版权署名代码</label>
                <textarea name="content" class="form-control">{$model.content|default=''|raw}</textarea>
            </div>
            <div class="form-group">
                <input type="hidden" name="id" value="{$model.id|default=''}">
                <button type="submit" class="btn btn-primary">{$id>0?'保存':'添加'}</button>
                <a class="ml-3" href="https://creativecommons.org/choose/?lang=zh" target="_blank">署名版权代码生成</a>
            </div>
        </form>
    </div>
</div>
{/block}