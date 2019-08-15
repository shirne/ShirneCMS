<extend name="public:base"/>

<block name="body">
    <include file="public/bread" menu="credit_promotion_index" title="积分策略"/>

    <div id="page-wrapper">
        <div class="page-header">积分策略</div>
        <div class="page-content">
            <form method="post">
                <div class="form-row">
                    <div class="form-group col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">策略名称</span>
                            </div>
                            <input type="text" name="name" class="form-control" value="{$model.name}"
                                   placeholder="输入名称">
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
                    <div class="form-group col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">共享比例</span>
                            </div>
                            <input type="text" name="share_percent" class="form-control" value="{$model.share_percent}"
                                   placeholder="共享分红积分的比例">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">赠送比例</span>
                            </div>
                            <input type="text" name="send_percent" class="form-control" value="{$model.send_percent}"
                                   placeholder="相对产品价格的百分比">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-2 form-row">
                        <label class="col-5" for="is_default" style="white-space: nowrap;">是否默认</label>
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
                    <div class="form-group col">
                    </div>
                </div>

                <div class="form-group mt-3">
                    <input type="hidden" name="id" value="{$model.id}"/>
                    <button type="submit" class="btn btn-primary">提交</button>
                </div>
            </form>
        </div>
    </div>
</block>