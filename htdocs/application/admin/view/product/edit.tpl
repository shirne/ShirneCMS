<extend name="public:base" />

<block name="body">
<include file="public/bread" menu="product_index" title="商品详情" />
<div id="page-wrapper">
    <div class="page-header">{$id>0?'编辑':'添加'}商品</div>
    <div id="page-content">
    <form method="post" action="" enctype="multipart/form-data">
        <div class="form-row">
            <div class="col form-group">
                <label for="article-title">商品名称</label>
                <input type="text" name="title" class="form-control" value="{$article.title}" id="article-title" placeholder="输入商品名称">
            </div>
            <div class="col form-group">
                <label for="article-title">副标题</label>
                <input type="text" name="vice_title" class="form-control" value="{$article.vice_title}" id="article-title" >
            </div>
        </div>
        <div class="form-group">
            <label for="article-cate">商品分类</label>
            <select name="cate_id" id="article-cate" class="form-control">
                <foreach name="category" item="v">
                    <option value="{$v.id}" {$article['cate_id'] == $v['id']?'selected="selected"':""}>{$v.html} {$v.title}</option>
                </foreach>
            </select>
        </div>
        <div class="form-group">
            <label for="image">封面图</label>
            <div class="input-group">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" name="upload_cover"/>
                    <label class="custom-file-label" for="upload_cover">选择文件</label>
                </div>
            </div>
            <if condition="$article['cover']">
                <figure class="figure">
                    <img src="{$article.cover}" class="figure-img img-fluid rounded" alt="image">
                    <figcaption class="figure-caption text-center">{$article.cover}</figcaption>
                </figure>
                <input type="hidden" name="delete_cover" value="{$article.cover}"/>
            </if>
        </div>
        <div class="form-group">
            <label for="article-content">商品介绍</label>
            <script id="article-content" name="content" type="text/plain">{$article.content|raw}</script>
        </div>
        <div class="form-group">
            <label>商品类型</label>
            <label class="radio-inline">
              <input type="radio" name="type" value="1" <if condition="$article.type eq 1">checked="checked"</if> >普通
            </label>
            <label class="radio-inline">
              <input type="radio" name="type" value="2" <if condition="$article.type eq 2">checked="checked"</if>>置顶
            </label>
            <label class="radio-inline">
              <input type="radio" name="type" value="3" <if condition="$article.type eq 3">checked="checked"</if>>热门
            </label>
            <label class="radio-inline">
              <input type="radio" name="type" value="4" <if condition="$article.type eq 4">checked="checked"</if>>推荐
            </label>
        </div>
        <input type="hidden" name="id" value="{$article.id}">
        <button type="submit" class="btn btn-primary">{$id>0?'保存':'添加'}</button>
    </form>
        </div>
</div>
    </block>
<block name="script">
<!-- 配置文件 -->
<script type="text/javascript" src="__STATIC__/ueditor/ueditor.config.js"></script>
<!-- 编辑器源码文件 -->
<script type="text/javascript" src="__STATIC__/ueditor/ueditor.all.min.js"></script>
<!-- 实例化编辑器 -->
<script type="text/javascript">
    var ue = UE.getEditor('article-content',{
        toolbars: Toolbars.normal,
        initialFrameHeight:500,
        zIndex:100
    });
</script>
</block>