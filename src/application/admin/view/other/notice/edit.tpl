{extend name="public:base" /}

{block name="body"}
{include file="public/bread" menu="notice_index" title="公告详情" /}

<div id="page-wrapper">
    <div class="page-header">{$id>0?'修改':'添加'}公告</div>
    <div class="page-content">
        <form method="post" action="">
            <div class="form-group">
                <label for="title">公告标题</label>
                <input type="text" name="title" class="form-control" value="{$model.title|default=''}"
                    placeholder="输入公告标题">
            </div>
            <div class="form-group {:empty($flags)?'d-none':''}">
                <label for="page">调用标志</label>
                <select name="page" class="form-control">
                    <option value="">无</option>
                    {foreach $flags as $flag => $v}
                    <option value="{$flag}" {$flag==$model['page']?'selected':''}>{$v}({$flag})</option>
                    {/foreach}
                </select>
                <div class="text-muted">此处内容由设计师设定，请勿改动！</div>
            </div>
            <div class="form-group">
                <label for="bb">链接地址</label>
                <input type="text" name="url" class="form-control" value="{$model.url|default=''}" placeholder="输入链接地址">
            </div>
            <div class="form-group">
                <label for="status1">公告状态</label>
                <label class="radio-inline">
                    <input type="radio" name="status" id="status1" value="1" {if $model['status']==1}checked="checked"
                        {/if}>显示
                </label>
                <label class="radio-inline">
                    <input type="radio" name="status" id="status0" value="0" {if $model['status']==0}checked="checked"
                        {/if}>隐藏
                </label>
            </div>
            <div class="form-group">
                <label for="summary">公告摘要</label>
                <textarea name="summary" class="form-control">{$model.summary|default=''}</textarea>
            </div>
            <div class="form-group">
                <label for="post-content">公告内容</label>
                <script id="post-content" name="content" type="text/plain">{$model.content|default=''|raw}</script>
            </div>
            <div class="form-group">
                <input type="hidden" name="id" value="{$model.id|default=''}">
                <button type="submit" class="btn btn-primary">保存</button>
            </div>
        </form>
    </div>
</div>
{/block}
{block name="script"}
<!-- 配置文件 -->
<script type="text/javascript" src="__STATIC__/ueditor/ueditor.config.js"></script>
<!-- 编辑器源码文件 -->
<script type="text/javascript" src="__STATIC__/ueditor/ueditor.all.min.js"></script>
<!-- 实例化编辑器 -->
<script type="text/javascript">
    var ue = UE.getEditor('post-content', {
        toolbars: Toolbars.normal,
        initialFrameHeight: 500,
        zIndex: 100
    });
</script>
{/block}