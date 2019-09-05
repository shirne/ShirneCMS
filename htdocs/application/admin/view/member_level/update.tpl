<extend name="public:base"/>

<block name="body">
    <include file="public/bread" menu="member_level_index" title="会员组配置"/>

    <div id="page-wrapper">
        <div class="page-header">{$model['level_id']>0?'编辑':'添加'}等级</div>
        <div class="page-content">
            <form method="post">
                <div class="row">
                    <div class="col-12 col-lg-6">
                    <div class="card mt-3">
                        <div class="card-header">基本设置</div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">等级名称</span>
                                        </div>
                                        <input type="text" name="level_name" class="form-control"
                                               value="{$model.level_name}"
                                               placeholder="输入名称">
                                    </div>
                                </div>
                                <div class="form-group col">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">简称</span>
                                        </div>
                                        <input type="text" name="short_name" class="form-control"
                                               value="{$model.short_name}"
                                               placeholder="名称简写">
                                    </div>
                                </div>

                            </div>
                            <div class="form-row">
                                <div class="form-group col">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">样式</span>
                                        </div>
                                        <select name="style" class="form-control text-{$model.style}"
                                                onchange="$(this).attr('class','form-control text-'+$(this).val())">
                                            <foreach name="styles" id="style">
                                                <option value="{$style}" {$model['style']==$style?'selected':''}
                                                        class="text-{$style}">██████████
                                                </option>
                                            </foreach>
                                        </select>
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
                                    <label for="is_default">是否默认</label>
                                    <div class="col">
                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-outline-primary {$model['is_default']?"active":""}">
                                                <input type="radio" name="is_default" value="1"
                                                       autocomplete="off" {$model['is_default']?"checked":""}> 是
                                            </label>
                                            <label class="btn btn-outline-secondary {$model['is_default']?"":"active"}">
                                                <input type="radio" name="is_default" value="0"
                                                       autocomplete="off" {$model['is_default']?"":"checked"}> 否
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col form-row">
                                    <label for="is_default">是否代理</label>
                                    <div class="col">
                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-outline-primary {$model['is_agent']?"active":""}">
                                                <input type="radio" name="is_agent" value="1"
                                                       autocomplete="off" {$model['is_agent']?"checked":""}> 是
                                            </label>
                                            <label class="btn btn-outline-secondary {$model['is_agent']?"":"active"}">
                                                <input type="radio" name="is_agent" value="0"
                                                       autocomplete="off" {$model['is_agent']?"":"checked"}> 否
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="text-muted">会员注册时自动升级为默认会员，并应用该会员组的设置（如:是否代理）</div>
                            </div>
                        </div>
                    </div>
                </div>
                    <div class="col-12 col-lg-6">
                    <div class="card mt-3">
                        <div class="card-header">消费设置</div>
                        <div class="card-body">
                            <div class="form-group form-row">
                                <label for="is_default">升级方式</label>
                                <div class="col">
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <label class="btn btn-outline-primary {$model['upgrade_type']=='1'?"active":""}">
                                            <input type="radio" name="upgrade_type" value="1"
                                                   autocomplete="off" {$model['upgrade_type']=='1'?"checked":""}> 自动升级
                                        </label>
                                        <label class="btn btn-outline-secondary {$model['upgrade_type']==0?"active":""}">
                                            <input type="radio" name="upgrade_type" value="0"
                                                   autocomplete="off" {$model['upgrade_type']==0?"checked":""}> 手动或绑定
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">消费额度</span>
                                    </div>
                                    <input type="text" name="level_price" class="form-control"
                                           value="{$model.level_price}"
                                           placeholder="输入消费额度">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="text-muted">自动升级的会员组，在累积消费达到指定额度时自动升级。否则，会员升级只能是购买绑定了等级的商品或后台手动升级</div>
                            </div>
                            <div class="form-group  form-row">
                                <label for="is_default">自定义价格</label>
                                <div class="col">
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <label class="btn btn-outline-primary {$model['diy_price']=='1'?"active":""}">
                                            <input type="radio" name="diy_price" value="1"
                                                   autocomplete="off" {$model['diy_price']=='1'?"checked":""}> 是
                                        </label>
                                        <label class="btn btn-outline-secondary {$model['diy_price']==0?"active":""}">
                                            <input type="radio" name="diy_price" value="0"
                                                   autocomplete="off" {$model['diy_price']==0?"checked":""}> 否
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
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
                            <div class="form-group">
                                <div class="text-muted">设置了自定义价格的等级，将在产品规格中单独设置价格。未设置的，按折扣计算折扣价，折扣按百分比算，100%即原价</div>
                            </div>
                        </div>
                    </div>

                    </div>
                </div>

                <div class="card mt-3">
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
                                <input type="text" name="commission_limit" class="form-control"
                                       value="{$model.commission_limit}">
                            </div>
                            <div class="row">
                                <div class="col form-text text-muted">代数修改需保存后才能再修改比例</div>
                                <div class="col form-text text-muted">本金上限即计算佣金时基数的最大值，填写 0 为不限制</div>
                            </div>
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