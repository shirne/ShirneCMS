{extend name="public:base"/}

{block name="body"}
{include file="public/bread" menu="page_index" title="编辑页面分组"/}

<div id="page-wrapper">
    <div class="page-header">{$id>0?'编辑':'添加'}分组</div>
    <div id="page-content">
        <form method="post" action="">
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
                <input type="text" name="sort" class="form-control" value="{$model.sort}">
            </div>
            <div class="form-row">
                <label class="col-md-1">独立模板</label>
                <div class="form-group col-md-2">
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-outline-secondary{$model['use_template']==1?' active':''}">
                            <input type="radio" name="use_template" value="1" autocomplete="off"
                                {$model['use_template']==1?' checked':''}> 是
                        </label>
                        <label class="btn btn-outline-secondary{$model['use_template']==0?' active':''}">
                            <input type="radio" name="use_template" value="0" autocomplete="off"
                                {$model['use_template']==0?' checked':''}> 否
                        </label>
                    </div>
                </div>
                <div class="col">
                    <div class="form-text text-muted">独立模板编写index.tpl放在“分组标识”目录下，参考page/index.tpl</div>
                </div>
            </div>
            <div class="form-group">
                <input type="hidden" name="id" value="{$model.id}">
                <button type="submit" class="btn btn-primary">{$id>0?'保存':'添加'}</button>
            </div>
        </form>
    </div>
</div>
{/block}