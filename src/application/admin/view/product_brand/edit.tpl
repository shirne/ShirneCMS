<extend name="public:base" />

<block name="body">

<include file="public/bread" menu="product_brand_index" title="品牌资料" />

<div id="page-wrapper">
    <div class="page-header">{$id>0?'编辑':'添加'}品牌</div>
    <div class="page-content">
    <form method="post" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">品牌名称</label>
            <input type="text" name="title" class="form-control" value="{$model.title}" placeholder="输入品牌名称">
        </div>
        <div class="form-group">
            <label for="url">官网地址</label>
            <input type="text" name="url" class="form-control" value="{$model.url}" placeholder="输入官网地址">
        </div>
        <div class="form-group">
            <label for="image">LOGO</label>
            <div class="input-group">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" name="upload_logo"/>
                    <label class="custom-file-label" for="upload_logo">选择文件</label>
                </div>
            </div>
            <if condition="$model['logo']">
                <figure class="figure">
                    <img src="{$model.logo}" class="figure-img img-fluid rounded" alt="image">
                    <figcaption class="figure-caption text-center">{$model.logo}</figcaption>
                </figure>
                <input type="hidden" name="delete_logo" value="{$model.logo}"/>
            </if>
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