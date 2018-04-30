<extend name="Public:Base" />

<block name="body">

<include file="Public/bread" menu="category_index" section="内容" title="分类管理" />

<div id="page-wrapper">
    <div class="page-header">{$id>0?'编辑':'添加'}分类</div>
    <div class="page-content">
    <form method="post" action="{:U('category/edit',array('id'=>$id))}" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">分类名称</label>
            <div class="input-group">
            <input type="text" name="title" class="form-control" value="{$model.title}" placeholder="输入分类名称">
                <span class="input-group-addon">简称</span>
                <input type="text" name="short" class="form-control" value="{$model.short}" >
            </div>
        </div>
        <div class="form-group">
            <label for="bb">父分类</label>
            <select name="pid" id="bb" class="form-control">
                <option value="">顶级分类</option>
                <foreach name="cate" item="v">
                    <option value="{$v.id}" <?php if($model['pid'] == $v['id']) {echo 'selected="selected"' ;}?>>{$v.html} {$v.title}</option>
                </foreach>
            </select>
        </div>
        <div class="form-group">
            <label for="name">分类别名</label>
            <input type="text" name="name" class="form-control"  value="{$model.name}" placeholder="输入分类别名,不能和其他分类别名重复">
        </div>
        <div class="form-group">
            <label for="post-title">分类图标</label>
            <input type="file" name="upload_icon" />
            <if condition="$model['icon']">
                <img src="{$model.icon}" style="max-width:100px;" />
                <input type="hidden" name="deleteimages[]" value="{$model.icon}"/>
            </if>
        </div>
        <div class="form-group">
            <label for="post-title">分类图片</label>
            <input type="file" name="upload_image" />
            <if condition="$model['image']">
                <img src="{$model.image}" style="max-width:80%" />
                <input type="hidden" name="deleteimages[]" value="{$model.image}"/>
            </if>
        </div>
        <div class="form-group">
            <label for="sort">排序</label>
            <input type="text" name="sort" class="form-control"  value="{$model.sort}" placeholder="排序按从小到大">
        </div>
        <div class="form-group">
            <label for="keywords">关键词</label>
            <input type="text" name="keywords" class="form-control" value="{$model.keywords}" placeholder="请输入SEO关键词(选填)">
        </div>
        <div class="form-group">
            <label for="description">描述信息</label>
            <textarea name="description" cols="30" rows="10" class="form-control" placeholder="请输入分类描述(选填)">{$model.description}</textarea>
        </div>
        <input type="hidden" name="id" value="{$model.id}">
        <button type="submit" class="btn btn-primary">{$id>0?'保存':'添加'}</button>
    </form>
        </div>
</div>
</block>