{extend name="public:base" /}

{block name="body"}

{include file="public/bread" menu="keywords_index" title="关键字信息" /}

<div id="page-wrapper">
    <div class="page-header">{$id>0?'编辑':'添加'}关键字</div>
    <div class="page-content">
    <form method="post" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">关键字</label>
            <input type="text" name="title" class="form-control" value="{$model.title|default=''}" placeholder="输入关键字">
        </div>
        <div class="form-group">
            <label for="description">关键字说明</label>
            <input type="text" name="description" class="form-control" value="{$model.description|default=''}" placeholder="输入关键字说明">
        </div>
        <div class="form-row">
            <div class="form-group col">
                <label for="group">分组</label>
                <div class="input-group">
                <input type="text" name="group" class="form-control" value="{$model.group|default=''}" placeholder="关键字分组" >
                    <select class="form-control" onchange="var val=$(this).val();if(val)this.form.group.value=val;">
                        <option value="">选择分组</option>
                        {volist name="groups" id="group"}
                            <option value="{$group}">{$group}</option>
                        {/volist}
                    </select>
                </div>
            </div>
            <div class="form-group col">
                <label for="v_hot">虚拟热度</label>
                <input type="text" name="v_hot" class="form-control" value="{$model.v_hot|default=''}" placeholder="越大越靠前" >
            </div>
        </div>
        <div class="form-group">
            <label for="image">题图</label>
            <div class="input-group">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" name="upload_image"/>
                    <label class="custom-file-label" for="upload_image">选择文件</label>
                </div>
            </div>
            {if !empty($model['image'])}
                <figure class="figure">
                    <img src="{$model.image}" class="figure-img img-fluid rounded" alt="image">
                    <figcaption class="figure-caption text-center">{$model.image}</figcaption>
                </figure>
                <input type="hidden" name="delete_image" value="{$model.image}"/>
            {/if}
        </div>
        <div class="form-group">
            <input type="hidden" name="id" value="{$model.id|default=''}">
            <button type="submit" class="btn btn-primary">{$id>0?'保存':'添加'}</button>
        </div>
    </form>
    </div>
</div>
{/block}