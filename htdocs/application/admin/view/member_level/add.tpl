<extend name="Public:Base" />

<block name="body">
    <include file="Public/bread" menu="memberlevel_index" section="会员" title="会员等级" />

    <div id="page-wrapper">
        <div class="page-header">添加等级</div>
        <div class="page-content">
            <form method="post" >
                <div class="form-group">
                    <label for="level_name">等级名称</label>
                    <div class="input-group">
                        <label class="input-group-addon">
                            <input type="checkbox" name="is_default" value="1" {$model['is_default']?"checked":""} />
                            默认
                        </label>
                        <input type="text" name="level_name" class="form-control" value="{$model.level_name}" placeholder="输入名称">
                        <label class="input-group-addon">
                            简称
                        </label>
                        <input type="text" name="short_name" class="form-control" value="{$model.short_name}" placeholder="名称简写">
                    </div>
                </div>
                <div class="form-group">
                    <label for="level_price">购买价格</label>
                    <input type="text" name="level_price" class="form-control" value="{$model.level_price}" placeholder="输入购买价格">
                </div>
                <div class="form-group">
                    <label for="sort">排序</label>
                    <input type="text" name="sort" class="form-control" value="{$model.sort}" placeholder="越小越靠前" >
                </div>
                <div class="form-group">
                    <label for="cc">佣金层数</label>
                    <input type="text" name="commission_layer" class="form-control" value="{$model.commission_layer}" placeholder="佣金层数" >
                    <span class="help-block">层数修改需保存后才能再修改比例</span>
                </div>
                <div class="form-group">
                    <label for="cc">佣金比例</label>
                    <div class="input-group">
                        <for start="0" end="$model['commission_layer']">
                            <span class="input-group-addon">{$i+1} 代</span>
                            <input type="text" name="commission_percent[$i]" value="{$model['commission_percent'][$i]}" class="form-control" />
                            <span class="input-group-addon">%</span>
                        </for>
                    </div>
                </div>

                <div class="form-group">
                    <input type="hidden" name="level_id" value="{$model.level_id}" />
                    <button type="submit" class="btn btn-primary">提交</button>
                </div>
            </form>
        </div>
    </div>
</block>