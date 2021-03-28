<extend name="public:base" />

<block name="body">

<include file="channel/_bread" title="图片详情" />

<div id="page-wrapper">
    <div class="page-header"><a href="{:url('channel/imagelist',array('channel_id'=>$channel_id,'aid'=>$aid))}" class="btn btn-outline-primary btn-sm"><i class="ion-md-arrow-back"></i> 返回</a>&nbsp;{$id>0?'编辑':'添加'}图片 </div>
    <div class="page-content">
    <form method="post" class="page-form" action="" enctype="multipart/form-data">
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
                <if condition="$model['image']">
                    <figure class="figure">
                        <img src="{$model.image}" class="figure-img img-fluid rounded" alt="image">
                        <figcaption class="figure-caption text-center">{$model.image}</figcaption>
                    </figure>
                    <input type="hidden" name="delete_image" value="{$model.image}"/>
                </if>
            </div>
            <div class="col form-group">
                <label for="image">排序</label>
                <input type="text" name="sort" class="form-control" value="{$model.sort}" />
            </div>
        </div>
        <div class="form-group submit-btn">
            <input type="hidden" name="article_id" value="{$model.article_id}">
            <button type="submit" class="btn btn-primary">{$id>0?'编辑':'添加'}</button>
        </div>
    </form>
    </div>
</div>
</block>