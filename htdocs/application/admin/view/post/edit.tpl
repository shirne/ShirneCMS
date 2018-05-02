<extend name="public:base" />

<block name="body">
<include file="public/bread" menu="post_index" section="内容" title="文章管理" />
<div id="page-wrapper">
    <div class="page-header">{$id>0?'编辑':'添加'}文章</div>
    <div id="page-content">
    <form method="post" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="post-title">文章标题</label>
            <input type="text" name="title" class="form-control" value="{$post.title}" id="post-title" placeholder="输入文章标题">
        </div>
        <div class="form-group">
            <label for="post-cate">文章分类</label>
            <select name="cate_id" id="post-cate" class="form-control">
                <foreach name="category" item="v">
                    <option value="{$v.id}" {$post['cate_id'] == $v['id']?'selected="selected"':""}>{$v.html} {$v.title}</option>
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
            <if condition="$post['cover']">
                <figure class="figure">
                    <img src="{$post.cover}" class="figure-img img-fluid rounded" alt="image">
                    <figcaption class="figure-caption text-center">{$post.cover}</figcaption>
                </figure>
                <input type="hidden" name="delete_cover" value="{$post.cover}"/>
            </if>
        </div>
        <div class="form-group">
            <label for="post-content">文章内容</label>
            <script id="post-content" name="content" type="text/plain">{$post.content|raw}</script>
        </div>
        <div class="form-group">
            <label>文章类型</label>
            <label class="radio-inline">
              <input type="radio" name="type" id="type" value="1" <if condition="$post.type eq 1">checked="checked"</if> >普通
            </label>
            <label class="radio-inline">
              <input type="radio" name="type" id="type" value="2" <if condition="$post.type eq 2">checked="checked"</if>>置顶
            </label>
            <label class="radio-inline">
              <input type="radio" name="type" id="type" value="3" <if condition="$post.type eq 3">checked="checked"</if>>热门
            </label>
            <label class="radio-inline">
              <input type="radio" name="type" id="type" value="4" <if condition="$post.type eq 4">checked="checked"</if>>推荐
            </label>
        </div>
        <input type="hidden" name="id" value="{$post.id}">
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
    var ue = UE.getEditor('post-content',{
        toolbars: Toolbars.normal,
        initialFrameHeight:500,
        zIndex:100
    });
</script>
</block>