<extend name="public:base"/>

<block name="body">
    <include file="public/bread" menu="product_postage_index" title="运费设置"/>

    <div id="page-wrapper">
        <div class="page-header">添加等级</div>
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
                                                <span class="input-group-text">模板名称</span>
                                            </div>
                                            <input type="text" name="level_name" class="form-control"
                                                   value="{$model.level_name}"
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
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="card mt-3">
                            <div class="card-header">基本设置</div>
                            <div class="card-body">
                                <div class="form-group col form-row">
                                    <label for="is_default">限制类型</label>
                                    <div class="col">
                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-outline-primary {$model['area_type']?"active":""}">
                                                <input type="radio" name="area_type" value="1"
                                                       autocomplete="off" {$model['area_type']?"checked":""}> 仅配送地区
                                            </label>
                                            <label class="btn btn-outline-secondary {$model['area_type']==0?"active":""}">
                                                <input type="radio" name="area_type" value="0"
                                                       autocomplete="off" {$model['area_type']==0?"checked":""}> 不配送地区
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">地区设置</div>
                    <div class="card-body">

                        <a href="javascript:" class="btn btn-outline-primary">添加设置</a>
                    </div>
                </div>
                <div class="form-group mt-3">
                    <input type="hidden" name="level_id" value="{$model.id}"/>
                    <button type="submit" class="btn btn-primary">提交</button>
                </div>
            </form>
        </div>
    </div>
</block>