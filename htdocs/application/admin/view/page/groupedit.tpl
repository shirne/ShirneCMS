<extend name="public:base"/>

<block name="body">
    <include file="public/bread" menu="page_index" title="编辑页面分组"/>

    <div id="page-wrapper">
        <div class="page-header">{$id>0?'编辑':'添加'}分组</div>
        <div id="page-content">
            <form method="post" action="" >
                <div class="form-group">
                    <label for="group_name">分组名称</label>
                    <input type="text" name="group_name" class="form-control" value="{$model.group_name}"
                           placeholder="输入分组名称">
                </div>
                <div class="form-group">
                    <label for="group">分组标识</label>
                    <input type="text" name="group" class="form-control" value="{$model.group}"
                           placeholder="输入分组标识,不能和其他分组标识重复">
                </div>
                <div class="form-group">
                    <label for="sort">分组排序</label>
                    <input type="text" name="sort" class="form-control" value="{$model.sort}" >
                </div>
                <div class="form-group">
                    <input type="hidden" name="id" value="{$model.id}">
                    <button type="submit" class="btn btn-primary">{$id>0?'保存':'添加'}</button>
                </div>
            </form>
        </div>
    </div>
</block>