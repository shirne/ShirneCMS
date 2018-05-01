<extend name="Public:Base" />

<block name="body">

<include file="Public/bread" menu="adv_index" section="其它" title="广告编辑" />

<div id="page-wrapper">
    <div class="page-header">{$id>0?'编辑':'添加'}广告</div>
    <div class="page-content">
    <form method="post" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">名称</label>
            <input type="text" name="title" class="form-control" value="{$model.title}" placeholder="名称">
        </div>
        <div class="form-group">
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
        <div class="form-group">
            <label for="url">链接</label>
            <input type="text" name="url" class="form-control" value="{$model.url}" />
        </div>
        <div class="form-group">
            <label for="image">有效期</label>
            <div class="input-group date-range">
                <span class="input-group-addon">从</span>
                <input type="text" name="start_date" class="form-control fromdate" value="{$model.start_date|showdate}" />
                <span class="input-group-addon">至</span>
                <input type="text" name="end_date" class="form-control todate" value="{$model.end_date|showdate}" />
            </div>
        </div>
        <div class="form-group">
            <label for="image">排序</label>
            <input type="text" name="sort" class="form-control" value="{$model.sort}" />
        </div>
        <div class="form-group">
            <label for="cc">状态</label>
            <label class="radio-inline">
                <input type="radio" name="type" value="1" <if condition="$model['status'] eq 1">checked="checked"</if> >显示
            </label>
            <label class="radio-inline">
                <input type="radio" name="type" value="0" <if condition="$model['status'] eq 0">checked="checked"</if>>隐藏
            </label>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">{$id>0?'编辑':'添加'}</button>
        </div>
    </form>
    </div>
</div>
</block>