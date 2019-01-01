<extend name="public:base"/>

<block name="body">
    <include file="public/bread" menu="member_level_index" title="会员组配置"/>

    <div id="page-wrapper">
        <div class="page-header">添加等级</div>
        <div class="page-content">
            <form method="post">
                <div class="form-row">
                    <div class="form-group col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">等级名称</span>
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
                    <div class="form-group col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">样式</span>
                            </div>
                            <select name="style" class="form-control text-{$model.style}" onchange="$(this).attr('class','form-control text-'+$(this).val())">
                                <foreach name="styles" id="style">
                                    <option value="{$style}" {$model['style']==$style?'selected':''} class="text-{$style}">██████████</option>
                                </foreach>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">消费额度</span>
                            </div>
                            <input type="text" name="level_price" class="form-control" value="{$model.level_price}"
                                   placeholder="输入购买价格">
                        </div>
                    </div>
                    <div class="form-group col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">会员折扣</span>
                            </div>
                            <input type="text" name="discount" class="form-control" value="{$model.discount}"
                                   placeholder="百分比折扣">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
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
                <div class="form-row">
                    <div class="form-group col form-row">
                        <label class="col-2" for="is_default">是否默认</label>
                        <div class="col">
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-outline-primary {$model['is_default']?"active":""}">
                                    <input type="radio" name="is_default" value="1" autocomplete="off" {$model['is_default']?"checked":""}> 是
                                </label>
                                <label class="btn btn-outline-secondary {$model['is_default']?"":"active"}">
                                    <input type="radio" name="is_default" value="0" autocomplete="off" {$model['is_default']?"":"checked"}> 否
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col form-row">
                        <label class="col-2" for="is_default">是否代理</label>
                        <div class="col">
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-outline-primary {$model['is_agent']?"active":""}">
                                    <input type="radio" name="is_agent" value="1" autocomplete="off" {$model['is_agent']?"checked":""}> 是
                                </label>
                                <label class="btn btn-outline-secondary {$model['is_agent']?"":"active"}">
                                    <input type="radio" name="is_agent" value="0" autocomplete="off" {$model['is_agent']?"":"checked"}> 否
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">消费返佣</div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">代数</span>
                                </div>
                                <input type="text" name="commission_layer" class="form-control"
                                    value="{$model.commission_layer}"
                                    placeholder="佣金层数">
                                <div class="input-group-middle">
                                    <span class="input-group-text">本金上限</span>
                                </div>
                                <input type="text" name="commission_limit" class="form-control" value="{$model.commission_limit}">
                            </div>
                            <div class="row"><div class="col form-text text-muted">代数修改需保存后才能再修改比例</div><div class="col form-text text-muted">本金上限即计算佣金时基数的最大值</div></div>
                        </div>
                        <div class="form-group">
                            <label for="cc">比例</label>
                            <div class="row">
                                <for start="0" end="$model['commission_layer']">
                                    <div class="input-group col">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">第 {$i+1} 代</span>
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
                            <span class="form-text text-muted">佣金本金 = 本金上限 > 0 ? min(销售价-成本价,本金上限) : (销售价-成本价) &nbsp;|&nbsp; 佣金金额 = 佣金本金 * 消费会员相对本会员的层级的比例</span>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <input type="hidden" name="level_id" value="{$model.level_id}"/>
                    <button type="submit" class="btn btn-primary">提交</button>
                </div>
            </form>
        </div>
    </div>
</block>