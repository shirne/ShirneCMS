<extend name="public:base" />

<block name="body">
<include file="public/bread" menu="article_index" title="文章详情" />
<div id="page-wrapper">
    <div class="page-header">{$id>0?'编辑':'添加'}文章</div>
    <div id="page-content">
    <form method="post" class="page-form" action="" enctype="multipart/form-data">
        <div class="form-row">
            <div class="col form-group">
                <label for="article-title">文章标题</label>
                <input type="text" name="title" class="form-control" value="{$article.title}" id="article-title" placeholder="输入文章标题">
            </div>
            <div class="col form-group">
                <label for="vice_title">副标题</label>
                <input type="text" name="vice_title" class="form-control" value="{$article.vice_title}" >
            </div>
        </div>
        <div class="form-row">
            <div class="col form-group">
                <label for="article-cate">文章分类</label>
                <select name="cate_id" id="article-cate" class="form-control">
                    <foreach name="category" item="v">
                        <option value="{$v.id}" {$article['cate_id'] == $v['id']?'selected="selected"':""}>{$v.html} {$v.title}</option>
                    </foreach>
                </select>
            </div>
            <div class="col form-group">
                <label for="create_time">发布时间</label>
                <input type="text" name="create_time" class="form-control datepicker" data-format="YYYY-MM-DD hh:mm:ss" value="{$article.create_time|showdate}" placeholder="默认取当前系统时间" >
            </div>
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
        <div class="form-row">
            <label class="col-2" style="max-width: 80px;">自定义字段</label>
            <div class="form-group col">
                <div class="prop-groups">
                    <foreach name="article['prop_data']" item="prop" key="k">
                        <div class="input-group mb-2" >
                            <input type="text" class="form-control" style="max-width:120px;" name="prop_data[keys][]" value="{$k}"/>
                            <input type="text" class="form-control" name="prop_data[values][]" value="{$prop}"/>
                            <div class="input-group-append delete"><a href="javascript:" class="btn btn-outline-secondary"><i class="ion-md-trash"></i> </a> </div>
                        </div>
                    </foreach>
                </div>
                <a href="javascript:" class="btn btn-outline-dark btn-sm addpropbtn"><i class="ion-md-add"></i> 添加属性</a>
            </div>
        </div>
        <div class="form-row">
            <label class="col-2 col-md-1">文章类型</label>
            <div class="form-group col-4 col-md-2">
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <volist name="types" id="type" key="k">
                        <label class="btn btn-outline-secondary{$key==$article['type']?' active':''}">
                            <input type="radio" name="type" value="{$key}" autocomplete="off" {$key==$article['type']?'checked':''}>{$type}
                        </label>
                    </volist>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="description">文章摘要</label>
            <textarea name="description" class="form-control" >{$article.description}</textarea>
        </div>
        <div class="form-group">
            <label for="article-content">文章内容</label>
            <script id="article-content" name="content" type="text/plain">{$article.content|raw}</script>
        </div>
        <div class="form-group submit-btn">
            <input type="hidden" name="id" value="{$article.id}">
            <button type="submit" class="btn btn-primary">{$id>0?'保存':'添加'}</button>
        </div>
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
    jQuery(function ($) {
        $('.addpropbtn').click(function (e) {
            $('.prop-groups').append('<div class="input-group mb-2" >\n' +
                '                            <input type="text" class="form-control" style="max-width:120px;" name="prop_data[keys][]" />\n' +
                '                            <input type="text" class="form-control" name="prop_data[values][]" />\n' +
                '                            <div class="input-group-append delete"><a href="javascript:" class="btn btn-outline-secondary"><i class="ion-md-trash"></i> </a> </div>\n' +
                '                        </div>');
        });
        $('.prop-groups').on('click','.delete .btn',function (e) {
            var self=$(this);
            dialog.confirm('确定删除该属性？',function () {
                self.parents('.input-group').remove();
            })
        });
    });
</script>
</block>