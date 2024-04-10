{extend name="public:base" /}

{block name="body"}
{include file="public/bread" menu="shop_product_index" title="商品详情" /}
<div id="page-wrapper">
    <div class="page-header">{$id>0?'编辑':'添加'}商品</div>
    <div id="page-content">
    <form method="post" action="" class="page-form" enctype="multipart/form-data">
        <div class="form-row">
            <div class="col">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">商品名称</span> </div>
                        <input type="text" name="title" class="form-control" value="{$product.title|default=''}" id="product-title" placeholder="输入商品名称">
                        <div class="input-group-prepend"><span class="input-group-text">单位</span> </div>
                        <input type="text" name="unit" class="form-control" value="{$product.unit|default=''}" id="product-unit" style="max-width:50px;" placeholder="单位">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">商品特性</span> </div>
                        <input type="text" name="vice_title" class="form-control" value="{$product.vice_title|default=''}" id="product-vice_title" placeholder="简要概括文字">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">商品货号</span> </div>
                        <input type="text" name="goods_no" class="form-control" value="{$product.goods_no|default=''}" id="product-goods_no" placeholder="输入商品货号">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">商品分类</span> </div>
                        <select name="cate_id" id="product-cate" class="form-control">
                            {foreach $category as $key => $v}
                                <option value="{$v.id}" data-pid="{$v['pid']}" data-props="{$v['props']}" data-specs="{$v['specs']}" {$product['cate_id'] == $v['id']?'selected="selected"':""}>{$v.html} {$v.title}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">商品品牌</span> </div>
                        <select name="brand_id" id="product-brand" class="form-control">
                            <option value="0" >--无--</option>
                            {foreach $brands as $key => $v}
                                <option value="{$v.id}" {$product['brand_id'] == $v['id']?'selected="selected"':""}>{$v.title}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                {if $needarea}
                <div class="form-group areabox">
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
                {/if}
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">商品主图</span>
                        </div>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="upload_image"/>
                            <label class="custom-file-label" for="upload_image">选择文件</label>
                        </div>
                    </div>
                    {if !empty($product['image'])}
                        <figure class="figure">
                            <img src="{$product.image}" class="figure-img img-fluid rounded" alt="image">
                            <figcaption class="figure-caption text-center">{$product.image}</figcaption>
                        </figure>
                        <input type="hidden" name="delete_image" value="{$product.image}"/>
                    {/if}
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
                                    <label class="btn btn-outline-secondary{$product['status']=='1'?' active':''}">
                                        <input type="radio" name="status" value="1" autocomplete="off" {$product['status']=='1'?'checked':''}>是
                                    </label>
                                    <label class="btn btn-outline-secondary{$product['status']=='0'?' active':''}">
                                        <input type="radio" name="status" value="0" autocomplete="off" {$product['status']=='0'?'checked':''}>否
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <label style="width: 80px;">商品销量</label>
                            <div class="form-group col">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" readonly value="{$product['sale']|default=''}" />
                                    <span class="input-group-middle"><span class="input-group-text">+</span></span>
                                    <input type="text" class="form-control" name="v_sale" title="虚拟销量" value="{$product['v_sale']|default=''}" />
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <label style="width: 80px;">商品类型</label>
                            <div class="form-group col">
                                <div class="btn-group btn-group-toggle btn-group-sm type-groups" data-toggle="buttons">
                                    {foreach $types as $k=>$type}
                                        <label class="btn btn-outline-secondary{$k==$product['type']?' active':''}">
                                            <input type="radio" name="type" value="{$k}" autocomplete="off" {$k==$product['type']?'checked':''}>{$type}
                                        </label>
                                    {/foreach}
                                </div>
                            </div>
                        </div>
                        <div class="form-row type_level">
                            <label style="width: 80px;">&nbsp;</label>
                            <div class="form-group col">
                                <div class="btn-group btn-group-toggle btn-group-sm" data-toggle="buttons">
                                    {volist name="levels" id="lv" key="k"}
                                        <label class="btn btn-outline-secondary{if isset($product['level_id']) && $k==$product['level_id']} active{/if}">
                                            <input type="radio" name="level_id" value="{$k}" autocomplete="off" {if isset($product['level_id']) && $k==$product['level_id']}checked{/if}>{$lv.level_name}
                                        </label>
                                    {/volist}
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <label style="width: 80px;">支持折扣</label>
                            <div class="form-group col">
                                <div class="btn-group btn-group-toggle btn-group-sm" data-toggle="buttons">
                                    <label class="btn btn-outline-secondary{$product['is_discount']==1?' active':''}">
                                        <input type="radio" name="is_discount" value="1" autocomplete="off" {$product['is_discount']==1?'checked':''}>支持
                                    </label>
                                    <label class="btn btn-outline-secondary{$product['is_discount']==0?' active':''}">
                                        <input type="radio" name="is_discount" value="0" autocomplete="off" {$product['is_discount']==0?'checked':''}>不支持
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <label style="width: 80px;">可用优惠券</label>
                            <div class="form-group col">
                                <div class="btn-group btn-group-toggle btn-group-sm" data-toggle="buttons">
                                    <label class="btn btn-outline-secondary{$product['is_coupon']==1?' active':''}">
                                        <input type="radio" name="is_coupon" value="1" autocomplete="off" {$product['is_coupon']==1?'checked':''}>可用
                                    </label>
                                    <label class="btn btn-outline-secondary{$product['is_coupon']==0?' active':''}">
                                        <input type="radio" name="is_coupon" value="0" autocomplete="off" {$product['is_coupon']==0?'checked':''}>不可用
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <label style="width: 80px;">支持分佣</label>
                            <div class="form-group col">
                                <div class="btn-group btn-group-toggle btn-group-sm commision-groups" data-toggle="buttons">
                                    <label class="btn btn-outline-secondary{$product['is_commission']==1?' active':''}">
                                        <input type="radio" name="is_commission" value="1" autocomplete="off" {$product['is_commission']==1?'checked':''}>支持
                                    </label>
                                    <label class="btn btn-outline-secondary{$product['is_commission']==2?' active':''}">
                                        <input type="radio" name="is_commission" value="2" autocomplete="off" {$product['is_commission']==2?'checked':''}>设置比例
                                    </label>
                                    <label class="btn btn-outline-secondary{$product['is_commission']==3?' active':''}">
                                        <input type="radio" name="is_commission" value="3" autocomplete="off" {$product['is_commission']==3?'checked':''}>设置金额
                                    </label>
                                    <label class="btn btn-outline-secondary{$product['is_commission']==4?' active':''}">
                                        <input type="radio" name="is_commission" value="4" autocomplete="off" {$product['is_commission']==4?'checked':''}>详细设置
                                    </label>
                                    <label class="btn btn-outline-secondary{$product['is_commission']==0?' active':''}">
                                        <input type="radio" name="is_commission" value="0" autocomplete="off" {$product['is_commission']==0?'checked':''}>不支持
                                    </label>
                                </div>
                            </div>
                        </div>

                        {php}$layercounts = array_column($levels,'commission_layer');$layercount = max($layercounts);{/php}
                        <div class="form-row commission_box cbox2">
                            <div class="form-group mb-0 col">
                                {for start="0" end="$layercount"}
                                    <div class="input-group input-group-sm mb-2 col">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">第 {$i+1} 代</span>
                                        </div>
                                        <input type="text" name="commission_percent[{$i}]"
                                               value="{$product['commission_percent'][$i]|default=''}"
                                               class="form-control"/>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                {/for}
                            </div>
                        </div>
                        <div class="form-row commission_box cbox3">
                            <div class="form-group mb-0 col">
                                {for start="0" end="$layercount"}
                                    <div class="input-group input-group-sm mb-2 col">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">第 {$i+1} 代</span>
                                        </div>
                                        <input type="text" name="commission_amount[{$i}]"
                                               value="{$product['commission_percent'][$i]|default=''}"
                                               class="form-control"/>
                                    </div>
                                {/for}
                            </div>
                        </div>
                        <div class="form-row commission_box cbox4">
                            <div class="form-group mb-0 col">
                                {volist name="levels" id="lv" key="k"}
                                <div class="input-group input-group-sm mb-2 col">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{$lv.level_name}</span>
                                    </div>
                                    {for start="0" end="$layercount"}
                                            <input type="text" name="commission_levels[{$k}][{$i}]"
                                                   value="{$product['commission_percent'][$k][$i]|default=''}"
                                                   class="form-control"/>
                                            <div class="input-group-append">
                                                <span class="input-group-text">/</span>
                                            </div>
                                    {/for}
                                </div>
                                {/volist}
                            </div>
                        </div>
                        <div class="commission_desc mb-2 text-muted">此处佣金层级按会员组设置的最大层级，不需要分佣的层级填写0即可，如需增加分级，先在会员组中设置一个最大值</div>
                        <div class="form-row">
                            <label style="width: 80px;">购买限制</label>
                            <div class="form-group mb-1 col">
                                <div class="btn-group btn-group-toggle btn-group-sm" data-toggle="buttons">
                                    {volist name="levels" id="lv" key="k"}
                                        <label class="btn btn-outline-secondary{:fix_in_array($k,$product['levels'])?' active':''}">
                                            <input type="checkbox" name="levels[]" value="{$k}" autocomplete="off" {:fix_in_array($k,$product['levels'])?'checked':''}>{$lv.level_name}
                                        </label>
                                    {/volist}
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
                                    <select name="max_buy_cycle" class="form-control">
                                        <option value="" >总计</option>
                                        <option value="day" {if isset($product['max_buy_cycle']) && $product['max_buy_cycle']=='day'}selected{/if}>每天</option>
                                        <option value="week" {if isset($product['max_buy_cycle']) && $product['max_buy_cycle']=='week'}selected{/if}>每周</option>
                                        <option value="month" {if isset($product['max_buy_cycle']) && $product['max_buy_cycle']=='month'}selected{/if}>每月</option>
                                        <option value="season" {if isset($product['max_buy_cycle']) && $product['max_buy_cycle']=='season'}selected{/if}>每季</option>
                                        <option value="year" {if isset($product['max_buy_cycle']) && $product['max_buy_cycle']=='year'}selected{/if}>每年</option>
                                    </select>
                                    <span class="input-group-middle"><span class="input-group-text">最多购买</span></span>
                                    <input type="text" name="max_buy" class="form-control" value="{$product['max_buy']|default=0}" placeholder="填写0不限制" />
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
                                <select class="form-control form-control-sm" name="postage_id" >
                                    <option value="0">免运费</option>
                                    {volist name="postages" id="pos" key="k"}
                                        {php}
                                            $selected='';
                                            if(($product['id']==0 && $pos['is_default']) || $product['postage_id']==$pos['id']){
                                                $selected='selected';
                                            }
                                        {/php}
                                        <option value="{$pos.id}" {$selected}>{$pos.title}{$pos['is_default']?'(默认)':''}</option>
                                    {/volist}
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
                    {if !empty($product['prop_data'])}
                    {foreach $product['prop_data'] as $k => $prop}
                        <div class="input-group mb-2" >
                            <input type="text" class="form-control" style="max-width:120px;" name="prop_data[keys][]" value="{$k}"/>
                            <input type="text" class="form-control" name="prop_data[values][]" value="{$prop}"/>
                            <div class="input-group-append delete"><a href="javascript:" class="btn btn-outline-secondary"><i class="ion-md-trash"></i> </a> </div>
                        </div>
                    {/foreach}
                    {/if}
                </div>
                <a href="javascript:" class="btn btn-outline-dark btn-sm addpropbtn"><i class="ion-md-add"></i> 添加属性</a>
            </div>
        </div>
        <div class="form-row">
            <label class="col-2" style="max-width: 80px;">商品规格</label>
            <div class="form-group col">
                <div class="spec-groups">
                    {foreach $product['spec_data'] as $k => $spec}
                    <div class="d-flex spec-row spec-{$k}" data-specid="{$k}">
                        <input type="hidden" name="spec_data[{$k}][title]" value="{$spec['title']}"/>
                        <label>{$spec.title}</label>
                        <div class="form-control col"><input type="text" class="taginput" data-spec_id="{$k}" value="{:implode(',',$spec['data'])}" ></div>
                        <div class="delete"><a href="javascript:" class="btn btn-outline-secondary"><i class="ion-md-trash"></i> </a> </div>
                    </div>
                    {/foreach}
                </div>
                <a href="javascript:" class="btn btn-outline-dark btn-sm addspecbtn"><i class="ion-md-add"></i> 添加规格</a>
            </div>
        </div>
        <div class="form-group">
            <table class="table table-hover spec-table">
                <thead>
                <tr>
                    {foreach $product['spec_data'] as $k => $spec}
                        <th class="specth">{$spec['title']}</th>
                    {/foreach}
                    <th class="first" scope="col">规格货号&nbsp;<a class="batch-set" title="批量设置" href="javascript:" data-field="goods_no"><i class="ion-md-create"></i> </a> </th>
                    <th scope="col">规格图片</th>
                    <th scope="col">重量(克)&nbsp;<a class="batch-set" title="批量设置" href="javascript:" data-field="weight"><i class="ion-md-create"></i> </a> </th>
                    <th scope="col">销售价&nbsp;<a class="batch-set" title="批量设置" href="javascript:" data-field="price"><i class="ion-md-create"></i> </a></th>
                    <th scope="col">独立价&nbsp;<a class="batch-set" title="批量设置" href="javascript:" data-field="ext_price"><i class="ion-md-create"></i> </a></th>
                    <th scope="col">市场价&nbsp;<a class="batch-set" title="批量设置" href="javascript:" data-field="market_price"><i class="ion-md-create"></i> </a></th>
                    <th scope="col">成本价&nbsp;<a class="batch-set" title="批量设置" href="javascript:" data-field="cost_price"><i class="ion-md-create"></i> </a></th>
                    <th scope="col">库存&nbsp;<a class="batch-set" title="批量设置" href="javascript:" data-field="storage"><i class="ion-md-create"></i> </a></th>
                    <th scope="col">操作</th>
                </tr>
                </thead>
                <tbody>
                    {foreach $skus as $k => $sku}
                    <tr data-idx="{$k}">
                        {foreach $product['spec_data'] as $sk => $spec}
                            <td><input type="hidden" class="spec-val" data-specid="{$sk}" name="skus[{$k}][specs][{$sk}]" value="{$sku['specs'][$sk]}" />{$sku['specs'][$sk]}</td>
                        {/foreach}
                        <td>
                            <input type="hidden" class="field-sku_id" name="skus[{$k}][sku_id]" value="{$sku.sku_id|default=''}"/>
                            <input type="text" class="form-control field-goods_no" name="skus[{$k}][goods_no]" value="{$sku.goods_no|default=''}">
                        </td>
                        <td><input type="hidden" class="field-sku_id" name="skus[{$k}][image]" value="{$sku.image|default=''}"/><img class="imgupload rounded" src="{$sku.image|default='/static/images/noimage.png'}" /> </td>
                        <td><input type="text" class="form-control field-weight" name="skus[{$k}][weight]" value="{$sku.weight|default=''}"> </td>
                        <td><input type="text" class="form-control field-price" name="skus[{$k}][price]" value="{$sku.price|default=''}"> </td>
                        <td>
                            {if !empty($price_levels)}
                                {foreach $price_levels as $plv}
                                    <div class="input-group input-group-sm"><span class="input-group-prepend"><span class="input-group-text">{$plv.level_name}</span></span><input type="text" class="form-control field-ext_price" data-level_id="{$plv.level_id}" name="skus[{$k}][ext_price][{$plv.level_id}]" value="{$sku['ext_price'][$plv['level_id']]?:''}"></div>
                                {/foreach}
                                {else/}
                                -
                            {/if}
                        </td>
                        <td><input type="text" class="form-control field-market_price" name="skus[{$k}][market_price]" value="{$sku.market_price|default=''}"> </td>
                        <td><input type="text" class="form-control field-cost_price" name="skus[{$k}][cost_price]" value="{$sku.cost_price|default=''}"> </td>
                        <td><input type="text" class="form-control field-storage" name="skus[{$k}][storage]" value="{$sku.storage|default=''}"> </td>
                        <td><a href="javascript:" class="btn btn-outline-secondary delete-btn"><i class="ion-md-trash"></i> </a> </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>

        <div class="form-group">
            <label for="product-content">商品介绍</label>
            <script id="product-content" name="content" type="text/plain">{$product.content|default=''|raw}</script>
        </div>
        <div class="form-group submit-btn">
            <input type="hidden" name="id" value="{$product.id|default=''}">
            <button type="submit" class="btn btn-primary">{$id>0?'保存':'添加'}</button>
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
<script type="text/javascript">
    var ue = UE.getEditor('product-content',{
        toolbars: Toolbars.normal,
        initialFrameHeight:500,
        zIndex:100
    });
    jQuery(function ($) {
        if(Location && $(".areabox").length>0){
            var locobj = new Location()
            $(".areabox").jChinaArea({
                aspnet: true,
                s1:"{$product.province|default=''}",
                s2:"{$product.city|default=''}",
                s3:"{$product.county|default=''}",
                onEmpty:function(sel){
                    sel.prepend('<option value="">全部</option>');
                }
            });
        }

        var usespecs=[];
        var rows=null;
        var isready=false;
        var goods_no=$('[name=goods_no]').val();
        var diy_levels=JSON.parse('{$price_levels|array_values|json_encode|raw}');
        var skus=JSON.parse('{$skus|json_encode|raw}');
        var presets = JSON.parse('{$presets|json_encode|raw}');

        function setSpecs(specids) {
            if(specids && specids.length) {
                $.ajax({
                    url: "{:url('get_specs')}",
                    dataType: 'JSON',
                    data: {
                        ids: specids.join(',')
                    },
                    type: 'POST',
                    success: function (json) {
                        $('.spec-groups').html('');
                        if (json.code === 1 && json.data) {
                            addSpec(json.data);
                        }
                        resetSkus();
                    }
                })
            }else{
                $('.spec-groups').html('');
                resetSkus();
            }
        }
        var lastPresets;
        function setValue(i,value){
            var field=$('[name='+i+']');
            if(field.attr('type')=='radio'){
                $('[name='+i+'][value='+value+']').trigger('click')
            }else if(field.attr('type')=='checkbox' || field.length<1){
                if(field.length<1){
                    field=$('[name="'+i+'[]"]');
                }
                if(field.length<1){
                    return;
                }
                if(!value){
                    value=[];
                }
                if(value.join){
                    value=value.join(',')
                }else{
                    value=value.toString()
                }
                if(typeof value == 'string'){
                    value=value.split(',')
                }
                for(var j=0;j<field.length;j++){
                    var fitem=field.eq(j)
                    if(value.indexOf(fitem.val())>-1){
                        if(!fitem.prop('checked')){
                            fitem.trigger('click')
                        }
                    }else{
                        if(fitem.prop('checked')){
                            fitem.trigger('click')
                        }
                    }
                }
            }else{
                field.val(value)
            }
        }
        function changeCategory(select,force) {
            var option=$(select).find('option:selected');
            var curProps=[];
            var cid=$(option).val();
            
            if(lastPresets){
                for(var i in lastPresets){
                    setValue(i, presets[0][i])
                }
                lastPresets=null;
            }
            if(presets[cid]){
                lastPresets = presets[cid];
                for(var i in presets[cid]){
                    setValue(i, presets[cid][i])
                }
            }
            var props=$(option).data('props') || [];
            $('.prop-groups .input-group').each(function () {
                var input=$(this).find('input');
                var prop=input.val().trim();
                if(input.eq(1).val().trim()===''){
                    if(props.indexOf(prop)<0){
                        $(this).remove();
                    }else{
                        curProps.push(prop);
                    }
                }else {
                    curProps.push(prop);
                }
            });
            for(var i=0;i<props.length;i++){
                if(curProps.indexOf(props[i])<0){
                    addProp(props[i]);
                }
            }
            var newspecs = $(option).data('specs');
            if(!newspecs)newspecs=[];
            if(force===true){
                setSpecs(newspecs);
            }else {
                usespecs = usespecs.sort(function (a, b){return a<b?-1:1});
                newspecs = newspecs.sort(function (a, b){return a<b?-1:1});
                if (usespecs.join(',') !== newspecs.join(',')) {
                    dialog.confirm('是否重置规格?', function () {
                        setSpecs(newspecs);
                    })
                }
            }
        }
        $('#product-cate').change(function (e) {
            changeCategory(this);
        });
        if('add'==="{$product['id']?'':'add'}"){
            changeCategory($('#product-cate'),true);
        }

        window.checkUsed=function(id) {
            if(usespecs.indexOf(id)>-1){
                return ' disabled';
            }
            return '';
        };
        window.joinTags=function (data) {
            return data?('<span class="badge badge-secondary badge-pill">'+
                data.join('</span><span class="badge badge-secondary badge-pill">')+
                '</span>'):'';
        };
        function updateSkus(){
            skus=[];
            var skurows=$('.spec-table tbody tr');
            skurows.each(function () {
                var sku={
                    sku_id: '',
                    goods_no: '',
                    weight: '',
                    price: '',
                    ext_price: {},
                    market_price: '',
                    cost_price: '',
                    storage: ''
                };
                for(var i in sku){
                    if(i=='ext_price'){
                        var ext_prices=$(this).find('.field-' + i);
                        ext_prices.each(function () {
                            var lid=$(this).data('level_id')
                            sku[i][lid]=$(this).val()
                        })
                    }else {
                        sku[i] = $(this).find('.field-' + i).val();
                    }
                }
                sku.specs={};
                var speccells=$(this).find('.spec-val');
                speccells.each(function () {
                    sku.specs[$(this).data('specid')]=$(this).val();
                });
                skus.push(sku);
            });
        }

        var diytpl='';
        function resetSkus(){
            if(!isready)return;
            var nrows=[],specrows=$('.spec-groups .spec-row');
            usespecs=[];
            var spec_datas=[];
            for(var i=0;i<specrows.length;i++){
                nrows.push(specrows.eq(i).find('label').text());
                var specid=specrows.eq(i).data('specid');
                usespecs.push(specid);

                var datas=[],labels=specrows.eq(i).find('.badge input[type=hidden]');
                for(var k=0;k<labels.length;k++){
                    datas.push(labels.eq(k).val());
                }
                spec_datas.push(datas);
            }
            if(!diytpl){
                if(diy_levels && diy_levels.length>0) {
                    var diyarr = [];
                    for (i = 0; i < diy_levels.length; i++) {
                        diyarr.push('<div class="input-group input-group-sm"><span class="input-group-prepend"><span class="input-group-text">'+diy_levels[i].level_name+'</span></span><input type="text" class="form-control field-ext_price" data-level_id="'+diy_levels[i].level_id+'" name="skus[{@i}][ext_price]['+diy_levels[i].level_id+']" value="{@ext_price.'+diy_levels[i].level_id+'}"></div>');
                    }
                    diytpl = diyarr.join("\n");
                }else{
                    diytpl = ' - ';
                }
            }

            var rowhtml='<tr data-idx="{@i}">\n' +
                '   {@specs}\n' +
                '   <td>\n' +
                '       <input type="hidden" class="field-sku_id" name="skus[{@i}][sku_id]" value="{@sku_id}"/>\n'+
                '       <input type="text" class="form-control field-goods_no" name="skus[{@i}][goods_no]" value="{@goods_no}">\n' +
                '   </td>\n' +
                '   <td><input type="hidden" class="field-image" name="skus[{@i}][image]" value="{@image}"/><img class="imgupload rounded" src="{@image|default=/static/images/noimage.png}" /></td>\n' +
                '   <td><input type="text" class="form-control field-weight" name="skus[{@i}][weight]" value="{@weight}"> </td>\n' +
                '   <td><input type="text" class="form-control field-price" name="skus[{@i}][price]" value="{@price}"> </td>\n' +
                '   <td>' + diytpl + '</td>\n' +
                '   <td><input type="text" class="form-control field-market_price" name="skus[{@i}][market_price]" value="{@market_price}"> </td>\n' +
                '   <td><input type="text" class="form-control field-cost_price" name="skus[{@i}][cost_price]" value="{@cost_price}"> </td>\n' +
                '   <td><input type="text" class="form-control field-storage" name="skus[{@i}][storage]" value="{@storage}"> </td>\n' +
                '   <td><a href="javascript:" class="btn btn-outline-secondary delete-btn"><i class="ion-md-trash"></i> </a> </td>\n'+
                '</tr>';
            if(!rows || nrows.join("\n")!==rows.join("\n")){
                $('.spec-table thead th.specth').remove();
                for(i=0;i<nrows.length;i++){
                    $('.spec-table thead th.first').before('<th class="specth">'+nrows[i]+'</th>');
                }
                rows=nrows;
            }

            var allhtml=[];
            var mixed_specs=[[]];
            if(spec_datas.length>0) {
                mixed_specs = specs_mix(spec_datas);
            }
            for (i = 0; i < mixed_specs.length; i++) {
                var data = findSku(mixed_specs[i]);
                data.specs=spec_cell(mixed_specs[i],i);
                data.i= i;
                if(goods_no)data.goods_no=goods_no+'_'+i;
                allhtml.push(rowhtml.compile(data));
            }

            $('.spec-table tbody').html(allhtml.join('\n'));
            updateSkus();
        }
        function findSku(specs) {
            var spec_obj=array_combine(usespecs,specs);
            for(var i=0;i<skus.length;i++){
                if(isObjectValueEqual(spec_obj, skus[i].specs)){
                    return {
                        sku_id:skus[i].sku_id,
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
                sku_id:'',
                image:'',
                goods_no: '',
                weight: '',
                price: '',
                ext_price: {},
                market_price: '',
                cost_price: '',
                storage: ''
            };
        }
        function spec_cell(arr,idx) {
            var specs=[];
            for(var i=0;i<arr.length;i++){
                specs.push('<td><input type="hidden" class="spec-val" data-specid="'+usespecs[i]+'" name="skus['+idx+'][specs]['+usespecs[i]+']" value="'+arr[i]+'" />'+arr[i]+'</td>')
            }
            return specs.join('\n');
        }
        function specs_mix(arr, idx, base){
            if(!idx)idx=0;
            if(!base)base=[];
            var mixed=[];
            var l=arr.length;
            for(var i=0;i<arr[idx].length;i++){
                var narr=copy_obj(base);
                narr.push(arr[idx][i]);
                if(idx+1>=l){
                    mixed.push(narr);
                }else {
                    mixed = mixed.concat(specs_mix(arr, idx+1, narr));
                }
            }
            return mixed;
        }
        function addProp(key,value) {
            $('.prop-groups').append('<div class="input-group mb-2" >\n' +
                '                            <input type="text" class="form-control" style="max-width:120px;" name="prop_data[keys][]" value="'+(key?key:'')+'" />\n' +
                '                            <input type="text" class="form-control" name="prop_data[values][]" value="'+(value?value:'')+'" />\n' +
                '                            <div class="input-group-append delete"><a href="javascript:" class="btn btn-outline-secondary"><i class="ion-md-trash"></i> </a> </div>\n' +
                '                        </div>');
        }
        $('.addpropbtn').click(function (e) {
            addProp();
        });
        function addSpec(spec,update) {
            if(spec instanceof Array){
                for(var i=0;i<spec.length;i++){
                    addSpec(spec[i],false);
                }
                if(update!==false)resetSkus();
            }else {
                $('.spec-groups').append(('<div class="spec-row d-flex spec-{@id}" data-specid="{@id}">\n' +
                    '   <input type="hidden" name="spec_data[{@id}][title]" value="{@title}"/>\n' +
                    '   <label>{@title}</label>\n' +
                    '   <div class="form-control col"><input type="text" class="taginput" value="{@data}" ></div>\n' +
                    '   <div class="delete"><a href="javascript:" class="btn btn-outline-secondary"><i class="ion-md-trash"></i> </a> </div>\n' +
                    '</div>').compile(spec));
                var lastrow = $('.spec-groups .spec-row').eq(-1);
                var firstInit=update;
                lastrow.find('.taginput').tags('spec_data[' + spec.id + '][data][]',function () {
                    if(firstInit!==false)resetSkus();
                    else firstInit=true;
                });

                if(update!==false)resetSkus();
            }
        }
        $('.addspecbtn').click(function (e) {
            dialog.pickList({
                'url':'{:url("get_specs")}',
                'name':'规格',
                'rowTemplate':'<a class="list-group-item list-group-item-action{@id|checkUsed} d-flex justify-content-between"  data-id="{@id}" ><span class="title">{@title}</span><div>{@data|joinTags}</div></a>'
            },function (spec) {
                if(!spec){
                    dialog.info('请选择规格');
                    return false;
                }
                if(checkUsed(spec.id)){
                    dialog.info('该规格已使用');
                    return false;
                }
                addSpec(spec);
            });
        });

        $('.taginput').each(function () {
            $(this).tags('spec_data['+$(this).data('spec_id')+'][data][]',resetSkus);
        });
        $('.prop-groups').on('click','.delete .btn',function (e) {
            var self=$(this);
            dialog.confirm('确定删除该属性？',function () {
                self.parents('.input-group').remove();
            })
        });
        $('.spec-groups').on('click','.delete .btn',function (e) {
            var self=$(this);
            dialog.confirm('确定删除该规格？',function () {
                self.parents('.spec-row').remove();
                resetSkus();
            })
        });
        $('.batch-set').click(function (e) {
            var field=$(this).data('field');
            var message='请输入要设置的数据';
            if(field === 'ext_price'){
                message={
                    title:message,
                    multi:{}
                }
                for(var i=0;i<diy_levels.length;i++){
                    message.multi[diy_levels[i].level_id]=diy_levels[i].level_name;
                }
            }
            dialog.prompt(message,function(val) {
                if(field==='goods_no') {
                    if (!val) {
                        dialog.warning('请填写货号');
                        return false;
                    }
                    if (!goods_no) {
                        goods_no = val;
                        $('[name=goods_no]').val(val);
                    }
                    $('.spec-table tbody .field-' + field).each(function () {
                        //console.log(this)
                        var row = $(this).parents('tr');
                        $(this).val(val + '_' + row.data('idx'));
                    })
                }else if(field==='ext_price'){
                    for(var k in val){
                        val[k]=parseFloat(val[k]);
                        if(isNaN(val[k])){
                            dialog.warning('请填写数值');
                            return false;
                        }
                    }
                    var extputs=$('.spec-table tbody .field-' + field);
                    extputs.each(function () {
                        var key=$(this).data('level_id')
                        $(this).val(val[key])
                    })
                }else {
                    val=parseFloat(val);
                    if(isNaN(val)){
                        dialog.warning('请填写数值');
                        return false;
                    }
                    $('.spec-table tbody .field-' + field).val(val);
                }
                updateSkus();
                return true;
            });
        });
        $('.spec-table').on('click','.delete-btn',function (e) {
            var row=$(this).parents('tr').eq(0);
            row.remove();
        });

        var currentUpload=null;
        $(document.body).append('<div class="d-none uploadfield"><input type="file" /></div>').on('click','.imgupload',function (e) {
            currentUpload=$(this);
            $('.uploadfield input').trigger('click');
        });
        $('.uploadfield input').on('change',function (e) {
            if(this.value){
                var file=this.files[0];
                currentUpload.attr('src',window.URL.createObjectURL(file));
                (function (img) {
                    var formData=new FormData();
                    formData.append('file',file);
                    $.ajax({
                        url:"{:url('index/uploads',['folder'=>'productsku'])}",
                        data:formData,
                        cache:false,
                        processData: false,
                        contentType: false,
                        dataType:'json',
                        type:'POST',
                        success:function (json) {
                            if(json.code==1) {
                                dialog.success(json.msg);
                                img.attr('src',json.data.url);
                                img.parent().find('input[type=hidden]').val(json.data.url)
                            }else{
                                dialog.error(json.msg);
                            }
                        }
                    })
                })(currentUpload)
            }
        });
        $('.commision-groups label').click(function () {
            var val=$(this).find('input').val();
            $('.commission_desc,.commission_box').hide();
            if(val>1){
                $('.commission_desc,.commission_box.cbox'+val).show();
            }
        }).filter('.active').trigger('click');

        $('.type-groups label').click(function () {
            var val=$(this).find('input').val();
            if(val>2){
                $('.type_level').show();
            }else{
                $('.type_level').hide();
            }
        }).filter('.active').trigger('click');
        isready=true;
    });
</script>
{/block}