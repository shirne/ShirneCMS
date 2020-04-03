{extend name="public:base" /}

{block name="body"}

{include  file="public/bread" menu="page_index" title="页面图集"  /}

<div id="page-wrapper">
    <div class="page-header">{$id>0?'编辑':'添加'}图片</div>
    <div class="page-content">
    <form method="post" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">名称</label>
            <input type="text" name="title" class="form-control" value="{$model.title}" placeholder="名称">
        </div>
        <div class="form-group">
            <label for="text">说明</label>
            <input type="text" name="description" class="form-control" value="{$model.description}" placeholder="图片说明">
        </div>
        <div class="form-row">
            <div class="col form-group">
                <label for="image">图片</label>
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="upload_image"/>
                        <label class="custom-file-label" for="upload_image">选择文件</label>
                    </div>
                </div>
                {if $model['image']}
                    <figure class="figure">
                        <img src="{$model.image}" class="figure-img img-fluid rounded" alt="image">
                        <figcaption class="figure-caption text-center">{$model.image}</figcaption>
                    </figure>
                    <input type="hidden" name="delete_image" value="{$model.image}"/>
                {/if}
            </div>
            <div class="col form-group">
                <label for="image">排序</label>
                <input type="text" name="sort" class="form-control" value="{$model.sort}" />
            </div>
        </div>
        <div class="form-group">
            <input type="hidden" name="page_id" value="{$model.page_id}">
            <button type="submit" class="btn btn-primary">{$id>0?'编辑':'添加'}</button>
        </div>
    </form>
    </div>
</div>
{/block}