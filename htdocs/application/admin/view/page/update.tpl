<extend name="Public:Base" />

<block name="body">
<include file="Public/bread" menu="page_index" section="内容" title="单页管理" />

<div id="page-wrapper">
    <div class="page-header">修改页面</div>
    <div id="page-content">
    <form method="post" action="{:U('page/update',array('id'=>$page.id))}">
        <div class="form-group">
            <label for="page-title">单页标题</label>
            <input type="text" name="title" class="form-control" value="{$page.title}" id="page-title" placeholder="输入单页标题">
        </div>
        <div class="form-group">
            <label for="page-name">单页别名</label>
            <input type="text" name="name" class="form-control" value="{$page.name}"id="page-name" placeholder="输入单页别名,不能和其他单页别名重复">
        </div>
        <div class="form-group">
            <label for="p-content">单页内容</label>
            <script id="p-content" name="content" type="text/plain">{$page.content|htmlspecialchars_decode}</script>
        </div>
        <div class="form-group">
        <input type="hidden" name="id" value="{$page.id}">
        <button type="submit" class="btn btn-primary">提交</button>
            </div>
    </form>
        </div>
</div>
    </block>
<block name="script">
<!-- 配置文件 -->
<script type="text/javascript" src="__PUBLIC__/ueditor/ueditor.config.js"></script>
<!-- 编辑器源码文件 -->
<script type="text/javascript" src="__PUBLIC__/ueditor/ueditor.all.js"></script>
<!-- 实例化编辑器 -->
<script type="text/javascript">
    var ue = UE.getEditor('p-content',{
        toolbars: [
            ['fullscreen', 'source', 'undo', 'redo','bold', 'italic', 'underline','fontborder', 'strikethrough', '|','simpleupload', 'insertimage','attachment','emotion','link','unlink', '|', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote','searchreplace', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc'],
            ['inserttable','insertrow', 'insertcol','mergeright', 'mergedown','deleterow', 'deletecol','splittorows','splittocols', 'splittocells','deletecaption','inserttitle', 'mergecells', 'deletetable','insertparagraphbeforetable', 'paragraph','fontsize','fontfamily']
        ],
        initialFrameHeight:500,
        zIndex:100
    });
</script>
</block>