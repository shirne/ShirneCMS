<extend name="Public:Base" />

<block name="body">
<include file="Public/bread" menu="notice_index" section="系统" title="公告管理" />

<div id="page-wrapper">
    <div class="page-header">添加公告</div>
    <div class="page-content">
    <form method="post" action="{:U('Notice/add')}">
        <div class="form-group">
            <label for="aa">公告标题</label>
            <input type="text" name="title" class="form-control" id="aa" placeholder="输入公告标题">
        </div>
        <div class="form-group">
            <label for="bb">链接地址</label>
            <input type="text" name="url" class="form-control" id="bb" placeholder="输入链接地址">
        </div>
        <div class="form-group">
            <label for="cc">公告状态</label>
            <label class="radio-inline">
                <input type="radio" name="status" id="status1" value="1" checked="checked">显示
            </label>
            <label class="radio-inline">
                <input type="radio" name="status" id="status0" value="0" >隐藏
            </label>
        </div>
        <div class="form-group">
            <label for="post-content">公告内容</label>
            <script id="post-content" name="content" type="text/plain"></script>
        </div>
        <div class="form-group">
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
    var ue = UE.getEditor('post-content',{
        toolbars: Toolbars.normal,
        initialFrameHeight:500,
        zIndex:100
    });
</script>
</block>