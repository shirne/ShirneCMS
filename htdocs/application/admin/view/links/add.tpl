<extend name="Public:Base" />

<block name="body">
<include file="Public/bread" menu="links_index" section="其它" title="链接管理" />

<div id="page-wrapper">
    <div class="page-header">添加链接</div>
    <div class="page-content">
    <form method="post" action="{:U('links/add')}">
        <div class="form-group">
            <label for="aa">链接标题</label>
            <input type="text" name="title" class="form-control" id="aa" placeholder="输入链接标题">
        </div>
        <div class="form-group">
            <label for="bb">链接地址</label>
            <input type="text" name="url" class="form-control" id="bb" placeholder="输入链接标题">
        </div>
        <div class="form-group">
            <label for="cc">优先级</label>
            <input type="text" name="sort" class="form-control" id="cc" placeholder="越大越靠前" value="100">
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">提交</button>
        </div>
    </form>
        </div>
</div>
</block>