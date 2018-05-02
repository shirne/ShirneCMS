<extend name="public:base"/>

<block name="body">
    <include file="public/bread" menu="member_level_index" section="会员" title="会员等级"/>

    <div id="page-wrapper">
        <div class="page-header">添加等级</div>
        <div class="page-content">
            <form method="post">
                <div class="form-row">
                    <div class="form-group col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text">等级名称</span>
                                <label class="input-group-text">
                                    <input type="checkbox" name="is_default" value="1" {$model['is_default']?"checked":""} />
                                    默认
                                </label>
                            </div>
                            <input type="text" name="level_name" class="form-control" value="{$model.level_name}"
                                   placeholder="输入名称">
                        </div>
                    </div>
                    <div class="form-group col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">简称</span>
                            </div>
                            <input type="text" name="short_name" class="form-control" value="{$model.short_name}"
                                   placeholder="名称简写">
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">购买价格</span>
                            </div>
                            <input type="text" name="level_price" class="form-control" value="{$model.level_price}"
                                   placeholder="输入购买价格">
                        </div>
                    </div>
                    <div class="form-group col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">排序</span>
                            </div>
                            <input type="text" name="sort" class="form-control" value="{$model.sort}"
                                   placeholder="越小越靠前">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">佣金层数</span>
                        </div>
                        <input type="text" name="commission_layer" class="form-control"
                               value="{$model.commission_layer}"
                               placeholder="佣金层数">
                    </div>
                    <span class="form-text text-muted">层数修改需保存后才能再修改比例</span>
                </div>
                <div class="form-group">
                    <label for="cc">佣金比例</label>
                    <div class="row">
                        <for start="0" end="$model['commission_layer']">
                            <div class="input-group col">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{$i+1} 代</span>
                                </div>
                                <input type="text" name="commission_percent[{$i}]"
                                       value="{$model['commission_percent'][$i]}"
                                       class="form-control"/>
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </for>
                    </div>

                </div>

                <div class="form-group">
                    <input type="hidden" name="level_id" value="{$model.level_id}"/>
                    <button type="submit" class="btn btn-primary">提交</button>
                </div>
            </form>
        </div>
    </div>
</block>