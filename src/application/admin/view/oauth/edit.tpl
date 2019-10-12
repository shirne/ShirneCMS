<extend name="public:base" />

<block name="body">

<include file="public/bread" menu="setting_index" title="接口信息" />

<div id="page-wrapper">
    <div class="page-header">{$id>0?'编辑':'添加'}接口</div>
    <div class="page-content">
    <form method="post" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">标题</label>
            <input type="text" name="title" class="form-control" value="{$model.title}" placeholder="输入标题">
        </div>
        <div class="form-group">
            <label for="type">类型</label>
            <select name="type" class="form-control">
                <option value="">请选择类型</option>
                <foreach name="types" key="k" item="v">
                    <option value="{$k}"
                    <?php if($model['type'] == $k) {echo 'selected="selected"' ;}?>
                    >{$v}</option>
                </foreach>
            </select>
        </div>
        <div class="form-group">
            <label for="appid">appid</label>
            <input type="text" name="appid" class="form-control" value="{$model.appid}" >
        </div>
        <div class="form-group">
            <label for="appkey">appkey</label>
            <input type="text" name="appkey" class="form-control" value="{$model.appkey}"  >
        </div>
        <div class="form-group">
            <label for="image">图标</label>
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
            <input type="hidden" name="id" value="{$model.id}">
            <button type="submit" class="btn btn-primary">{$id>0?'保存':'添加'}</button>
        </div>
    </form>
    </div>
</div>
</block>