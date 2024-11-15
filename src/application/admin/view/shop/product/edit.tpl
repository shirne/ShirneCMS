{extend name="public:base" /}

{block name="body"}
{include file="public/bread" menu="shop_product_index" title="商品详情" /}
<div id="page-wrapper">
    <div class="page-header">{$id>0?'编辑':'添加'}商品</div>
    <div id="page-content">
        <form class="noajax" method="post" action="" class="page-form" enctype="multipart/form-data">
            <div class="form-row">
                <div class="col">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">商品名称</span> </div>
                            <input type="text" name="title" class="form-control" v-model="product.title"
                                id="product-title" placeholder="输入商品名称">
                            <div class="input-group-prepend"><span class="input-group-text">单位</span> </div>
                            <input type="text" name="unit" class="form-control" v-model="product.unit" id="product-unit"
                                style="max-width:50px;" placeholder="单位">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">商品特性</span> </div>
                            <input type="text" name="vice_title" class="form-control" v-model="product.vice_title"
                                id="product-vice_title" placeholder="简要概括文字">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">商品货号</span> </div>
                            <input type="text" name="goods_no" class="form-control" v-model="product.goods_no"
                                id="product-goods_no" placeholder="输入商品货号">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">商品分类</span> </div>
                            <select name="cate_id" id="product-cate" v-model="product.cate_id" class="form-control"
                                @change="cateChange">
                                <option v-for="(v,key) in category" :value="v.id">{{v.html}} {{v.title}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">商品品牌</span> </div>
                            <select name="brand_id" id="product-brand" v-model="product.brand_id" class="form-control">
                                <option value="0">--无--</option>
                                <option v-for="(v,key) in brands" :value="v.id">{{v.title}}</option>
                            </select>
                        </div>
                    </div>
                    <div v-show="needarea" class="form-group areabox">
                        <input type="hidden" name="province" />
                        <input type="hidden" name="city" />
                        <input type="hidden" name="county" />
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">发布地</span> </div>
                            <select name="province_id" id="province-id" class="form-control">
                            </select>
                            <select name="city_id" id="city-id" class="form-control">
                            </select>
                            <select name="county_id" id="county-id" class="form-control">
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">商品主图</span>
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input vue-handler" name="upload_image"
                                    @change="uploadFile" />
                                <label class="custom-file-label" for="upload_image">选择文件</label>
                            </div>
                        </div>
                        <figure class="figure" v-show="product.image">
                            <img :src="product.image" class="figure-img img-fluid rounded" alt="image">
                            <figcaption class="figure-caption text-center">{{product.image}}</figcaption>
                        </figure>
                    </div>
                </div>
                <div class="col-5">
                    <div class="card form-group">
                        <div class="card-header">商品属性</div>
                        <div class="card-body">
                            <div class="form-row">
                                <label style="width: 80px;">是否发布</label>
                                <div class="form-group col">
                                    <div class="btn-group btn-group-toggle btn-group-sm" data-toggle="buttons">
                                        <label :class="'btn btn-outline-secondary'+(product.status==1?' active':'')">
                                            <input type="radio" name="status" :value="1" autocomplete="off"
                                                v-model="product.status">是
                                        </label>
                                        <label :class="'btn btn-outline-secondary'+(product.status==0?' active':'')">
                                            <input type="radio" name="status" :value="0" autocomplete="off"
                                                v-model="product.status">否
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <label style="width: 80px;">商品销量</label>
                                <div class="form-group col">
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control" readonly :value="product.sale" />
                                        <span class="input-group-middle"><span class="input-group-text">+</span></span>
                                        <input type="text" class="form-control" name="v_sale" title="虚拟销量"
                                            v-model="product.v_sale" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <label style="width: 80px;">商品类型</label>
                                <div class="form-group col">
                                    <div class="btn-group btn-group-toggle btn-group-sm type-groups"
                                        data-toggle="buttons">
                                        <label v-for="(type,k) in types"
                                            :class="'btn btn-outline-secondary'+(k==product.type?' active':'')">
                                            <input type="radio" name="type" :value="k" autocomplete="off"
                                                v-model="product.type">{{type}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row type_level" v-show="product.type == 4">
                                <label style="width: 80px;">&nbsp;</label>
                                <div class="form-group col">
                                    <div class="btn-group btn-group-toggle btn-group-sm" data-toggle="buttons">
                                        <label v-for="(lv,k) in levels"
                                            :class="'btn btn-outline-secondary'+(product.level_id==lv.id?' active':'')">
                                            <input type="radio" name="level_id" :value="k" autocomplete="off"
                                                v-model="product.level_id">{{lv.level_name}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <label style="width: 80px;">支持折扣</label>
                                <div class="form-group col">
                                    <div class="btn-group btn-group-toggle btn-group-sm" data-toggle="buttons">
                                        <label
                                            :class="'btn btn-outline-secondary'+(product.is_discount==1?' active':'')">
                                            <input type="radio" name="is_discount" :value="1" autocomplete="off"
                                                v-model="product.is_discount">支持
                                        </label>
                                        <label
                                            :class="'btn btn-outline-secondary'+(product.is_discount==0?' active':'')">
                                            <input type="radio" name="is_discount" :value="0" autocomplete="off"
                                                v-model="product.is_discount">不支持
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <label style="width: 80px;">可用优惠券</label>
                                <div class="form-group col">
                                    <div class="btn-group btn-group-toggle btn-group-sm" data-toggle="buttons">
                                        <label :class="'btn btn-outline-secondary'+(product.is_coupon==1?' active':'')">
                                            <input type="radio" name="is_coupon" :value="1" autocomplete="off"
                                                v-model="product.is_coupon">可用
                                        </label>
                                        <label :class="'btn btn-outline-secondary'+(product.is_coupon==0?' active':'')">
                                            <input type="radio" name="is_coupon" :value="0" autocomplete="off"
                                                v-model="product.is_coupon">不可用
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <label style="width: 80px;">支持分佣</label>
                                <div class="form-group col">
                                    <div class="btn-group btn-group-toggle btn-group-sm commision-groups"
                                        data-toggle="buttons">
                                        <label
                                            :class="'btn btn-outline-secondary'+(product.is_commission==1?' active':'')">
                                            <input type="radio" name="is_commission" :value="1" autocomplete="off"
                                                v-model="product.is_commission">支持
                                        </label>
                                        <label
                                            :class="'btn btn-outline-secondary'+(product.is_commission==2?' active':'')">
                                            <input type="radio" name="is_commission" :value="2" autocomplete="off"
                                                v-model="product.is_commission">设置比例
                                        </label>
                                        <label
                                            :class="'btn btn-outline-secondary'+(product.is_commission==3?' active':'')">
                                            <input type="radio" name="is_commission" :value="3" autocomplete="off"
                                                v-model="product.is_commission">设置金额
                                        </label>
                                        <label
                                            :class="'btn btn-outline-secondary'+(product.is_commission==4?' active':'')">
                                            <input type="radio" name="is_commission" :value="4" autocomplete="off"
                                                v-model="product.is_commission">详细设置
                                        </label>
                                        <label
                                            :class="'btn btn-outline-secondary'+(product.is_commission==0?' active':'')">
                                            <input type="radio" name="is_commission" :value="0" autocomplete="off"
                                                v-model="product.is_commission">不支持
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row commission_box cbox2" v-show="product.is_commission==2">
                                <div class="form-group mb-0 col">
                                    <div v-for="i in layerArr" class="input-group input-group-sm mb-2 col">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">第 {{i+1}} 代</span>
                                        </div>
                                        <input type="text" :name="'commission_percent['+i+']'"
                                            v-model="product.commission_percent[i]" class="form-control" />
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row commission_box cbox3" v-show="product.is_commission==3">
                                <div class="form-group mb-0 col">
                                    <div v-for="i in layerArr" class="input-group input-group-sm mb-2 col">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">第 {{i+1}} 代</span>
                                        </div>
                                        <input type="text" :name="'commission_amount['+i+']'"
                                            v-model="product.commission_amount[i]" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-row commission_box cbox4" v-show="product.is_commission==4">
                                <div class="form-group mb-0 col">
                                    <div v-for="(lv,k) in levels" class="input-group input-group-sm mb-2 col">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">{{lv.level_name}}</span>
                                        </div>
                                        <template v-for="i in layerArr">
                                            <input type="text" :name="'commission_levels['+lv.level_id+']['+i+']'"
                                                v-model="product.commission_levels[lv.level_id][i]"
                                                class="form-control" />
                                            <div class="input-group-append">
                                                <span class="input-group-text">/</span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <div class="commission_desc mb-2 text-muted">
                                此处佣金层级按会员组设置的最大层级，不需要分佣的层级填写0即可，如需增加分级，先在会员组中设置一个最大值</div>
                            <div class="form-row">
                                <label style="width: 80px;">购买限制</label>
                                <div class="form-group mb-1 col">
                                    <div class="btn-group btn-group-toggle btn-group-sm" data-toggle="buttons">
                                        <label v-for="(lv,k) in levels"
                                            :class="'btn btn-outline-secondary'+(product.levels && product.levels.indexOf(lv.id)>-1?' active':'')">
                                            <input type="checkbox" name="levels[]" :value="k" autocomplete="off"
                                                v-model="product.levels">{{lv.level_name}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <label style="width: 80px;">&nbsp;</label>
                                <div class="form-group col">
                                    <div class="text-muted">只有选中的会员组可以购买，不选则不限制</div>
                                </div>
                            </div>
                            <div class="form-row">
                                <label style="width: 80px;">购买数量</label>
                                <div class="form-group mb-1 col">
                                    <div class="input-group input-group-sm">
                                        <select name="max_buy_cycle" v-model="product.max_buy_cycle"
                                            class="form-control">
                                            <option value="">总计</option>
                                            <option value="day">每天</option>
                                            <option value="week">每周</option>
                                            <option value="month">每月</option>
                                            <option value="season">每季</option>
                                            <option value="year">每年</option>
                                        </select>
                                        <span class="input-group-middle"><span
                                                class="input-group-text">最多购买</span></span>
                                        <input type="text" name="max_buy" class="form-control" v-model="product.max_buy"
                                            placeholder="填写0不限制" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <label style="width: 80px;">&nbsp;</label>
                                <div class="form-group col">
                                    <div class="text-muted">默认为0不限制购买数量</div>
                                </div>
                            </div>
                            <div class="form-row">
                                <label style="width: 80px;">运费设置</label>
                                <div class="form-group col">
                                    <select class="form-control form-control-sm" name="postage_id"
                                        v-model="product.postage_id">
                                        <option value="0">免运费</option>
                                        <option v-for="(pos,k) in postages" :value="pos.id">
                                            {{pos.title}}{{pos['is_default']?'(默认)':''}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <label class="col-2" style="max-width: 80px;">自定义属性</label>
                <div class="form-group col">
                    <div class="prop-groups">
                        <div v-for="(prop, k) in product.prop_data" class="input-group mb-2">
                            <input type="text" class="form-control" style="max-width:120px;" v-model="prop.key" />
                            <input type="text" class="form-control" v-model="prop.value" />
                            <div class="input-group-append delete">
                                <a href="javascript:" class="btn btn-outline-secondary" @click="delProp(k)"><i
                                        class="ion-md-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <a href="javascript:" class="btn btn-outline-dark btn-sm addpropbtn" @click="addProp"><i
                            class="ion-md-add"></i>
                        添加属性</a>
                </div>
            </div>
            <div class="form-row">
                <label class="col-2" style="max-width: 80px;">商品规格</label>
                <div class="form-group col">
                    <div class="spec-groups">
                        <div v-for="(spec, k) in product.spec_data" :class="'d-flex spec-row spec-'+k" :data-specid="k">
                            <input type="hidden" :name="'spec_data['+k+'][title]'" :value="spec['title']" />
                            <label>{{spec.title}}</label>
                            <div class="form-control col">
                                <span v-show="spec.data && spec.data.length>0" class="badge-group">
                                    <span v-for="label in spec.data" class="badge badge-info">{{label}}<input
                                            type="hidden" name="'+nm+'" value="{{label}}" /><button type="button"
                                            class="close" @click="removeSpecVale(k,label)" data-dismiss="alert"
                                            aria-label="Close"><span aria-hidden="true">&times;</span></button></span>
                                </span>
                                <input type="text" class="taginput" :data-spec_id="k" v-model="spec.value"
                                    @blur="checkSpecValue(k, true)" @keyup="checkSpecValue(k, false)">
                            </div>
                            <div class="delete">
                                <a href="javascript:" class="btn btn-outline-secondary" @click="delSpecEvent(k)"><i
                                        class="ion-md-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <a href="javascript:" class="btn btn-outline-dark btn-sm addspecbtn" @click="addSpecEvent"><i
                            class="ion-md-add"></i>
                        添加规格</a>
                </div>
            </div>
            <div v-show="needUpdate">规格已更新, <a href="javascript:" class="btn btn-link" @click="resetSkus">刷新规格表</a>
            </div>
            <div class="form-group">
                <table class="table table-sm table-hover spec-table">
                    <thead>
                        <tr>
                            <th v-for="(spec, k) in product.spec_data" class="specth">{{spec['title']}}</th>
                            <th class="first" scope="col">规格货号&nbsp;<a class="batch-set" title="批量设置" href="javascript:"
                                    data-field="goods_no" @click="batchSet('goods_no')"><i class="ion-md-create"></i>
                                </a>
                            </th>
                            <th scope="col">规格图片</th>
                            <th scope="col">重量(克)&nbsp;<a class="batch-set" title="批量设置" href="javascript:"
                                    data-field="weight" @click="batchSet('weight')"><i class="ion-md-create"></i> </a>
                            </th>
                            <th scope="col">销售价&nbsp;<a class="batch-set" title="批量设置" href="javascript:"
                                    data-field="price" @click="batchSet('price')"><i class="ion-md-create"></i> </a>
                            </th>
                            <th scope="col">独立价&nbsp;<a class="batch-set" title="批量设置" href="javascript:"
                                    data-field="ext_price" @click="batchSet('ext_price')"><i class="ion-md-create"></i>
                                </a></th>
                            <th scope="col">市场价&nbsp;<a class="batch-set" title="批量设置" href="javascript:"
                                    data-field="market_price" @click="batchSet('market_price')"><i
                                        class="ion-md-create"></i> </a></th>
                            <th scope="col">成本价&nbsp;<a class="batch-set" title="批量设置" href="javascript:"
                                    data-field="cost_price" @click="batchSet('cost_price')"><i
                                        class="ion-md-create"></i> </a></th>
                            <th scope="col">库存&nbsp;<a class="batch-set" title="批量设置" href="javascript:"
                                    data-field="storage" @click="batchSet('storage')"><i class="ion-md-create"></i> </a>
                            </th>
                            <th scope="col">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(sku, k) in skus" :data-idx="k">
                            <td v-for="(spec, sk) in product.spec_data"><input type="hidden" class="spec-val"
                                    :data-specid="sk" :name="'skus['+k+'][specs]['+spec.key+']'"
                                    v-model="sku.specs[spec.key]" />{{sku.specs[spec.key]}}
                            </td>
                            <td>
                                <input type="hidden" class="field-sku_id" :name="'skus['+k+'][sku_id]'"
                                    v-model="sku.sku_id" />
                                <input type="text" class="form-control field-goods_no" :name="'skus['+k+'][goods_no]'"
                                    v-model="sku.goods_no">
                            </td>
                            <td class="sku-upload"><input type="file" @change="uploadSkuFile" :data-key="k" /><img
                                    class="imgupload rounded"
                                    :src="sku.image?sku.image: '/static/images/noimage.png'" /> </td>
                            <td><input type="text" class="form-control field-weight" :name="'skus['+k+'][weight]'"
                                    v-model="sku.weight"> </td>
                            <td><input type="text" class="form-control field-price" :name="'skus['+k+'][price]'"
                                    v-model="sku.price"> </td>
                            <td>
                                <template v-if="price_levels && price_levels.length>0">
                                    <div v-for="plv in price_levels" class="input-group input-group-sm"><span
                                            class="input-group-prepend"><span
                                                class="input-group-text">{{plv.level_name}}</span></span><input
                                            type="text" class="form-control field-ext_price"
                                            :data-level_id="plv.level_id" v-model="sku.ext_price[plv.level_id]"></div>
                                </template>
                                <template v-else>
                                    -
                                </template>

                            </td>
                            <td><input type="text" class="form-control field-market_price"
                                    :name="'skus['+k+'][market_price]'" v-model="sku.market_price"> </td>
                            <td><input type="text" class="form-control field-cost_price"
                                    :name="'skus['+k+'][cost_price]'" v-model="sku.cost_price"> </td>
                            <td><input type="text" class="form-control field-storage" :name="'skus['+k+'][storage]'"
                                    v-model="sku.storage"> </td>
                            <td><a href="javascript:" class="btn btn-outline-secondary delete-btn"><i
                                        class="ion-md-trash"></i> </a> </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="form-group">
                <label for="product-content">商品介绍</label>
                <script id="product-content" name="content" type="text/plain">{$product.content|default=''|raw}</script>
            </div>
            <div class="form-group submit-btn">
                <input type="hidden" name="id" :value="product.id">
                <button type="submit" class="btn btn-primary" @click="doSubmit">{$id>0?'保存':'添加'}</button>
            </div>
        </form>
    </div>
</div>
{/block}
{block name="script"}
<script type="text/javascript" src="__STATIC__/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="__STATIC__/ueditor/ueditor.all.min.js"></script>
{if $needarea}
<script type="text/javascript" src="__STATIC__/js/location.min.js"></script>
{/if}
<script type="text/plain"
    id="category_json">{:json_encode(\\app\\common\\facade\\ProductCategoryFacade::getCategories())}</script>
<script type="text/plain" id="model">{:json_encode($product)}</script>
<script type="text/plain" id="brands">{:json_encode($brands)}</script>
<script type="text/plain" id="levels">{:json_encode(array_values($levels))}</script>
<script type="text/plain" id="types">{:json_encode($types)}</script>
<script type="text/plain" id="postages">{:json_encode($postages)}</script>
<script type="text/plain" id="skus">{:json_encode($skus)}</script>
<script type="text/javascript" src="__STATIC__/vue/2.6/vue.min.js"></script>
<script type="text/javascript">
    var product = JSON.parse($('#model').text())
    var levels = JSON.parse($('#levels').text())
    var price_levels = [];
    var olevels = product.commission_percent;
    if (!olevels) olevels = {}
    var clevels = {}
    var count = 0
    var layerArr = []
    for (var i = 0; i < levels.length; i++) {
        count = Math.max(count, levels[i].commission_layer)
    }
    for (var i = 0; i < count; i++) {
        layerArr.push(i)
    }

    for (var i = 0; i < levels.length; i++) {
        if (levels[i].diy_price == 1) {
            price_levels.push(levels[i]);
        }
        if (!clevels[levels[i].level_id]) {
            clevels[levels[i].level_id] = olevels[levels[i].level_id] ? olevels[levels[i].level_id] : {}
        }
    }
    product.commission_percent = olevels;
    product.commission_amount = olevels;
    product.commission_levels = clevels;

    var skus = JSON.parse($('#skus').text())
    for (var i = 0; i < skus.length; i++) {
        if (!skus[i].specs) {
            skus[i].specs = {}
        }
    }

    var presets = JSON.parse('{$presets|json_encode|raw}');
    var lastPresets = null


    var app = new Vue({
        el: '#page-wrapper',
        data: {
            id: '{$id}',
            product: product,
            skus: skus,
            category: JSON.parse($('#category_json').text()),
            brands: JSON.parse($('#brands').text()),
            levels: levels,
            price_levels: price_levels,
            types: JSON.parse($('#types').text()),
            postages: JSON.parse($('#postages').text()),
            needarea: '{$needarea}' == 1,
            layercount: count,
            layerArr: layerArr,
            needUpdate: false
        },
        mounted: function () {
            this.loadData();
        },
        methods: {
            loadData: function () {
                var self = this;

                self.ue = UE.getEditor('product-content', {
                    toolbars: Toolbars.normal,
                    initialFrameHeight: 500,
                    zIndex: 100
                });
                if (Location && $(".areabox").length > 0) {
                    var locobj = new Location()
                    $(".areabox").jChinaArea({
                        aspnet: true,
                        s1: "{$product.province|default=''}",
                        s2: "{$product.city|default=''}",
                        s3: "{$product.county|default=''}",
                        onEmpty: function (sel) {
                            sel.prepend('<option value="">全部</option>');
                        }
                    });
                }

                window.checkUsed = function (key) {
                    var sds = self.product.spec_data
                    for (var i = 0; i < sds.length; i++) {
                        if (key == sds[i].key) {
                            return ' disabled';
                        }
                    }
                    return '';
                };
                window.joinTags = function (data) {
                    return data ? ('<span class="badge badge-secondary badge-pill">' +
                        data.join('</span><span class="badge badge-secondary badge-pill">') +
                        '</span>') : '';
                };

            },
            removeSpecVale: function (k, label) {

                var spec = this.product.spec_data[k]
                var index = spec.data.indexOf(label)
                if (index > -1) {
                    spec.data.splice(index, 1)
                    this.needUpdate = true;
                }
            },
            checkSpecValue: function (k, isend) {
                var spec = this.product.spec_data[k]
                if (!spec.value) return;
                var val = spec.value.replace(/，/g, ',');
                if (val.indexOf(',') > -1 || isend) {
                    var vals = val.split(',');
                    for (var i = 0; i < vals.length; i++) {
                        vals[i] = vals[i].replace(/^\s|\s$/g, '');
                        if (vals[i] && spec.data.indexOf(vals[i]) === -1) {
                            spec.data.push(vals[i]);
                            this.needUpdate = true;
                        }
                    }
                    Vue.set(this.product.spec_data[k], 'value', '')
                }
            },
            setSpecs: function (specids) {
                var self = this
                if (specids && specids.length) {
                    $.ajax({
                        url: "{:url('get_specs')}",
                        dataType: 'JSON',
                        data: {
                            ids: specids.join(',')
                        },
                        type: 'POST',
                        success: function (json) {
                            //$('.spec-groups').html('');
                            if (json.code === 1 && json.data) {
                                self.addSpec(json.data);
                            }
                            //self.resetSkus();
                        }
                    })
                } else {
                    Vue.set(self.product, 'spec_data', [])
                    this.needUpdate = true
                    //self.resetSkus();
                }
            },
            setValue: function (i, value) {
                var field = $('[name=' + i + ']');
                if (field.attr('type') == 'radio') {
                    $('[name=' + i + '][value=' + value + ']').trigger('click')
                } else if (field.attr('type') == 'checkbox' || field.length < 1) {
                    if (field.length < 1) {
                        field = $('[name="' + i + '[]"]');
                    }
                    if (field.length < 1) {
                        return;
                    }
                    if (!value) {
                        value = [];
                    }
                    if (value.join) {
                        value = value.join(',')
                    } else {
                        value = value.toString()
                    }
                    if (typeof value == 'string') {
                        value = value.split(',')
                    }
                    for (var j = 0; j < field.length; j++) {
                        var fitem = field.eq(j)
                        if (value.indexOf(fitem.val()) > -1) {
                            if (!fitem.prop('checked')) {
                                fitem.trigger('click')
                            }
                        } else {
                            if (fitem.prop('checked')) {
                                fitem.trigger('click')
                            }
                        }
                    }
                } else {
                    field.val(value)
                }
            },
            resetSkus: function () {
                var spec_datas = this.product.spec_data;

                if (spec_datas.length > 0) {
                    mixed_specs = this.specs_mix(spec_datas);
                }
                var skus = []
                var goods_no = this.product.goods_no
                for (i = 0; i < mixed_specs.length; i++) {
                    var data = this.findSku(mixed_specs[i]);
                    //data.specs = this.spec_cell(mixed_specs[i], i);
                    var suffix = []
                    for (var j = 0; j < spec_datas.length; j++) {
                        suffix.push(data.specs[spec_datas[j].key])
                    }
                    if (goods_no) {
                        data.goods_no = goods_no + '_' + suffix.join('_');
                    }
                    skus.push(data)
                }

                Vue.set(this, 'skus', skus)
                this.needUpdate = false
            },
            findSku: function (specs) {

                for (var i = 0; i < skus.length; i++) {
                    if (isObjectValueEqual(specs, skus[i].specs)) {
                        return {
                            sku_id: skus[i].sku_id,
                            specs: skus[i].specs,
                            goods_no: skus[i].goods_no,
                            image: skus[i].image,
                            weight: skus[i].weight,
                            price: skus[i].price,
                            ext_price: skus[i].ext_price,
                            market_price: skus[i].market_price,
                            cost_price: skus[i].cost_price,
                            storage: skus[i].storage
                        };
                    }
                }
                return {
                    sku_id: '',
                    specs: specs,
                    image: '',
                    goods_no: '',
                    weight: '',
                    price: '',
                    ext_price: {},
                    market_price: '',
                    cost_price: '',
                    storage: ''
                };
            },
            specs_mix: function (arr, idx, base) {
                if (!idx) idx = 0;
                if (!base) base = {};
                var mixed = [];
                var l = arr.length;
                for (var i = 0; i < arr[idx].data.length; i++) {
                    var narr = copy_obj(base);
                    narr[arr[idx].key] = arr[idx].data[i];
                    if (idx + 1 >= l) {
                        mixed.push(narr);
                    } else {
                        mixed = mixed.concat(this.specs_mix(arr, idx + 1, narr));
                    }
                }
                return mixed;
            },
            addProp: function (key, vaue) {
                //console.log(this.product.prop_data)
                if (!this.product.prop_data) {
                    Vue.set(this.product, 'prop_data', [])
                }
                this.product.prop_data.push({})
                //Vue.set(this.product, 'prop_data', this.product.prop_data)
            },
            delProp: function (key) {
                var self = this
                dialog.confirm('确定删除该属性？', function () {
                    self.product.prop_data.splice(key, 1)
                    //self.parents('.spec-row').remove();
                    //self.resetSkus();
                })
            },
            addSpec: function (spec) {
                if (spec instanceof Array) {
                    for (var i = 0; i < spec.length; i++) {
                        this.addSpec(spec[i], false);
                    }
                    this.needUpdate = true
                } else {
                    // console.log(this.product.spec_data)
                    this.product.spec_data.push(spec)

                    this.needUpdate = true
                }
            },
            addSpecEvent: function () {
                var self = this
                dialog.pickList({
                    'url': '{:url("get_specs")}',
                    'name': '规格',
                    'rowTemplate': '<a class="list-group-item list-group-item-action{@key|checkUsed} d-flex justify-content-between"  data-id="{@id}" data-key="{@key}" ><span class="title">{@title}</span><div>{@data|joinTags}</div></a>'
                }, function (spec) {
                    if (!spec) {
                        dialog.info('请选择规格');
                        return false;
                    }
                    if (checkUsed(spec.key)) {
                        dialog.info('该规格已使用');
                        return false;
                    }
                    self.addSpec(spec);
                });
            },
            delSpecEvent: function (key) {
                var self = this
                dialog.confirm('确定删除该规格？', function () {
                    self.product.spec_data.splice(key, 1)
                    //self.parents('.spec-row').remove();
                    this.needUpdate = true
                })
            },
            uploadFile: function (e) {
                var files = e.target.files
                var img = $(e.target).parents('.form-group').find('img')
                //var caption = $(e.target).parents('.form-group').find('.figure-caption')
                if (files.length > 0) {
                    img.attr('src', window.URL.createObjectURL(files[0]));
                    var self = this
                    this.uploadImage(files[0], function (data) {
                        Vue.set(self.product, 'image', data.url)
                        //caption.text(url)
                        $(e.target).val('')
                    })
                }
            },
            uploadSkuFile: function (e) {
                var files = e.target.files
                var img = $(e.target).parents('td').find('img')
                if (files.length > 0) {
                    img.attr('src', window.URL.createObjectURL(files[0]));
                    $(e.target).hide()
                    var self = this
                    var key = $(e.target).data('key')
                    this.uploadImage(files[0], function (data) {
                        Vue.set(self.skus[key], 'image', data.url)
                        $(e.target).val('').show()
                    })
                }
            },
            uploadImage: function (file, uploaded, progress) {
                var formData = new FormData();
                formData.append('file', file);
                $.ajax({
                    url: "{:url('index/uploads',['folder'=>'productsku'])}",
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    type: 'POST',
                    success: function (json) {
                        if (json.code == 1) {
                            dialog.success(json.msg);
                            uploaded(json.data)
                        } else {
                            dialog.error(json.msg);
                        }
                    }
                })
            },
            findCate: function (cid) {
                for (var i = 0; i < this.category.length; i++) {
                    if (this.category[i].id == cid) {
                        return this.category[i]
                    }
                }
                return null
            },
            cateChange: function (e) {
                var curProps = [];
                var cid = $(e.target).val();

                var cate = this.findCate(cid)
                if (!cate) return;
                if (lastPresets) {
                    for (var i in lastPresets) {
                        this.setValue(i, presets[0][i])
                    }
                    lastPresets = null;
                }
                if (presets[cid]) {
                    lastPresets = presets[cid];
                    for (var i in presets[cid]) {
                        this.setValue(i, presets[cid][i])
                    }
                }
                var props = cate.props;
                if (props) {
                    var hasProps = []
                    for (var i = 0; i < this.product.prop_data.length; i++) {
                        if (props.indexOf(this.product.prop_data[i].key) < 0) {
                            hasProps.push(this.product.prop_data[i].key)
                        }
                    }
                    for (var i = 0; i < props.length; i++) {
                        if (hasProps.indexOf(props[i]) < 0) {

                            this.product.prop_data.push({
                                key: props[i]
                            })
                        }
                    }
                }


                var newspecs = cate.specs;
                if (!newspecs) newspecs = [];
                var self = this

                var usespecs = []
                for (var i = 0; i < this.product.spec_data.length; i++) {
                    usespecs.push(this.product.spec_data[i].key)
                }
                usespecs.sort(function (a, b) { return a < b ? -1 : 1 });
                newspecs = newspecs.sort(function (a, b) { return a < b ? -1 : 1 });
                if (usespecs.join(',') !== newspecs.join(',')) {
                    dialog.confirm('是否重置规格?', function () {
                        self.setSpecs(newspecs);
                    })
                }

            },
            batchSet: function (field) {

                var message = '请输入要设置的数据';
                if (field === 'ext_price') {
                    message = {
                        title: message,
                        multi: {}
                    }
                    for (var i = 0; i < this.diy_levels.length; i++) {
                        message.multi[this.diy_levels[i].level_id] = this.diy_levels[i].level_name;
                    }
                }
                var self = this
                dialog.prompt(message, function (val) {
                    if (field === 'goods_no') {
                        if (!val) {
                            dialog.warning('请填写货号');
                            return false;
                        }
                        var goods_no = self.product.goods_no
                        if (!goods_no) {
                            goods_no = val;
                            Vue.set(self.product, 'goods_no', val)
                        }
                        var speckeys = []
                        for (var i = 0; i < self.product.spec_data.length; i++) {
                            speckeys.push(self.product.spec_data[i].key)
                        }
                        for (var i = 0; i < self.skus.length; i++) {
                            var suffix = []
                            for (var j = 0; j < speckeys.length; j++) {
                                suffix.push(self.skus[i].specs[speckeys[j]])
                            }
                            Vue.set(self.skus[i], field, val + '_' + suffix.join('_'))
                        }

                    } else if (field === 'ext_price') {
                        for (var k in val) {
                            val[k] = parseFloat(val[k]);
                            if (isNaN(val[k])) {
                                dialog.warning('请填写数值');
                                return false;
                            }
                        }
                        for (var i = 0; i < self.skus.length; i++) {
                            Vue.set(self.skus[i], field, val)
                        }
                    } else {
                        val = parseFloat(val);
                        if (isNaN(val)) {
                            dialog.warning('请填写数值');
                            return false;
                        }
                        for (var i = 0; i < self.skus.length; i++) {

                            Vue.set(self.skus[i], field, val)
                        }
                    }
                    //updateSkus();
                    return true;
                });
            },
            doSubmit: function (e) {
                e.preventDefault()

                var data = JSON.parse(JSON.stringify(this.product))
                data.skus = JSON.parse(JSON.stringify(this.skus))
                data.content = this.ue.getContent()

                if (data.is_commission == 3) {
                    data.commission_percent = data.commission_amount
                } else if (data.is_commission == 4) {
                    data.commission_percent = data.commission_levels
                } else if (data.is_commission != 2) {
                    data.commission_percent = {}
                }
                delete data.commission_amount
                delete data.commission_levels

                var loading = dialog.loading()
                $.ajax({
                    url: '',
                    data: data,
                    type: 'POST',
                    dataType: 'json',
                    success: function (json) {
                        loading.close()
                        if (json.code == 1) {
                            dialog.success(json.msg)
                            setTimeout(function () {
                                location.href = json.url
                            }, 500)
                        } else {
                            dialog.error(json.msg)
                        }
                    },
                    error: function (err) {
                        loading.close()
                        dialog.error('服务器错误')
                    }
                })
            }
        }
    });
</script>
{/block}