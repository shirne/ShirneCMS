<extend name="public:base"/>

<block name="body">
    <include file="public/bread" menu="page_index" title="单页详情"/>

    <div id="page-wrapper">
        <div class="page-header">{$id>0?'编辑':'添加'}页面</div>
        <div id="page-content">
            <form method="post" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="page-title">页面标题</label>
                    <input type="text" name="title" class="form-control" value="{$page.title}" id="page-title"
                           placeholder="输入单页标题">
                </div>
                <div class="form-row">
                    <div class="col form-group">
                        <label for="page-name">别名</label>
                        <input type="text" name="name" class="form-control" value="{$page.name}" id="page-name"
                               placeholder="输入单页别名,不能和其他单页别名重复">
                    </div>
                    <div class="col form-group">
                        <label for="page-title">分组</label>
                        <div class="input-group">
                        <input type="text" name="group" class="form-control" value="{$page.group}" placeholder="从右侧选择或填写一个新的分组" >
                            <select class="form-control" onchange="var val=$(this).val();if(val)this.form.group.value=val;">
                                <option value="">选择分组</option>
                                <volist name="groups" id="group">
                                    <option value="{$group.group}">{$group.group_name}</option>
                                </volist>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                <div class="col form-group">
                    <label for="image">图标</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="upload_icon"/>
                            <label class="custom-file-label" for="upload_icon">选择文件</label>
                        </div>
                    </div>
                    <if condition="$page['icon']">
                        <figure class="figure">
                            <img src="{$page.icon}" class="figure-img img-fluid rounded" alt="image">
                            <figcaption class="figure-caption text-center">{$page.icon}</figcaption>
                        </figure>
                        <input type="hidden" name="delete_icon" value="{$page.icon}"/>
                    </if>
                </div>
                <div class="col form-group">
                    <label for="page-title">排序</label>
                    <input type="text" name="sort" class="form-control" value="{$page.sort}" >
                </div>
                </div>
                <div class="form-group">
                    <label>页面状态</label>
                    <label class="radio-inline">
                        <input type="radio" name="status" value="1" <if condition="$page.status eq 1">checked="checked"</if> >显示
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="status" value="0" <if condition="$page.status eq 2">checked="checked"</if> >隐藏
                    </label>
                </div>
                <div class="form-group">
                    <label for="p-content">单页内容</label>
                    <script id="p-content" name="content" type="text/plain">{$page.content|raw}</script>
                </div>
                <div class="form-group">
                    <input type="hidden" name="id" value="{$page.id}">
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
        var ue = UE.getEditor('p-content', {
            toolbars: [
                ['fullscreen', 'source', 'undo', 'redo', 'bold', 'italic', 'underline', 'fontborder', 'strikethrough', '|', 'simpleupload', 'insertimage', 'attachment', 'emotion', 'link', 'unlink', '|', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'searchreplace', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc'],
                ['inserttable', 'insertrow', 'insertcol', 'mergeright', 'mergedown', 'deleterow', 'deletecol', 'splittorows', 'splittocols', 'splittocells', 'deletecaption', 'inserttitle', 'mergecells', 'deletetable', 'insertparagraphbeforetable', 'paragraph', 'fontsize', 'fontfamily']
            ],
            initialFrameHeight: 500,
            zIndex: 100
        });
    </script>
</block>