<extend name="public:base" />

<block name="body">
<include file="public/bread" menu="product_index" title="商品详情" />
<div id="page-wrapper">
    <div class="page-header">{$id>0?'编辑':'添加'}商品</div>
    <div id="page-content">
    <form method="post" action="" enctype="multipart/form-data">
        <div class="form-row">
            <div class="col">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">商品名称</span> </div>
                        <input type="text" name="title" class="form-control" value="{$product.title}" id="product-title" placeholder="输入商品名称">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">商品货号</span> </div>
                        <input type="text" name="goods_no" class="form-control" value="{$product.goods_no}" id="product-goods_no" placeholder="输入商品货号">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">商品分类</span> </div>
                        <select name="cate_id" id="product-cate" class="form-control">
                            <foreach name="category" item="v">
                                <option value="{$v.id}" {$product['cate_id'] == $v['id']?'selected="selected"':""}>{$v.html} {$v.title}</option>
                            </foreach>
                        </select>
                    </div>
                </div>
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
                    <if condition="$product['image']">
                        <figure class="figure">
                            <img src="{$product.image}" class="figure-img img-fluid rounded" alt="image">
                            <figcaption class="figure-caption text-center">{$product.image}</figcaption>
                        </figure>
                        <input type="hidden" name="delete_image" value="{$product.image}"/>
                    </if>
                </div>
            </div>
            <div class="col-4">
                <div class="card form-group">
                    <div class="card-header">商品属性</div>
                    <div class="card-body">
                        <div class="form-row">
                            <label class="col-3">商品类型</label>
                            <div class="form-group col">
                                <div class="btn-group btn-group-toggle btn-group-sm" data-toggle="buttons">
                                    <volist name="types" id="type" key="k">
                                        <label class="btn btn-outline-secondary{$k==$product['type']?' active':''}">
                                            <input type="radio" name="type" value="{$k}" autocomplete="off" {$k==$product['type']?'checked':''}>{$type}
                                        </label>
                                    </volist>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <label class="col-3">支持折扣</label>
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
                            <label class="col-3">支持分佣</label>
                            <div class="form-group col">
                                <div class="btn-group btn-group-toggle btn-group-sm" data-toggle="buttons">
                                    <label class="btn btn-outline-secondary{$product['is_commission']==1?' active':''}">
                                        <input type="radio" name="is_commission" value="1" autocomplete="off" {$product['is_commission']==1?'checked':''}>支持
                                    </label>
                                    <label class="btn btn-outline-secondary{$product['is_commission']==0?' active':''}">
                                        <input type="radio" name="is_commission" value="0" autocomplete="off" {$product['is_commission']==0?'checked':''}>不支持
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <label class="col-3">限制购买</label>
                            <div class="form-group col">
                                <div class="btn-group btn-group-toggle btn-group-sm" data-toggle="buttons">
                                    <volist name="levels" id="lv" key="k">
                                        <label class="btn btn-outline-secondary{:fix_in_array($k,$product['levels'])?' active':''}">
                                            <input type="checkbox" name="levels[]" value="{$k}" autocomplete="off" {:fix_in_array($k,$product['levels'])?'checked':''}>{$lv.level_name}
                                        </label>
                                    </volist>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-row">
            <label class="col-2" style="max-width: 80px;">商品规格</label>
            <div class="form-group col">
                <div class="spec-groups">
                    <foreach name="product['spec_data']" item="spec" key="k">
                    <div class="d-flex spec-row spec-{$k}" data-specid="{$k}">
                        <input type="hidden" name="spec_data[{$k}][title]" value="{$spec['title']}"/>
                        <label>{$spec.title}</label>
                        <div class="form-control col"><input type="text" class="taginput" data-spec_id="{$k}" value="{:implode(',',$spec['data'])}" ></div>
                        <div class="delete"><a href="javascript:" class="btn btn-outline-secondary"><i class="ion-md-trash"></i> </a> </div>
                    </div>
                    </foreach>
                </div>
                <a href="javascript:" class="btn btn-outline-dark btn-sm addspecbtn"><i class="ion-md-add"></i> 添加规格</a>
            </div>
        </div>
        <div class="form-group">
            <table class="table table-hover spec-table">
                <thead>
                <tr>
                    <foreach name="product['spec_data']" item="spec" key="k">
                        <th class="specth">{$spec['title']}</th>
                    </foreach>
                    <th class="first" scope="col">规格货号&nbsp;<a class="batch-set" title="批量设置" href="javascript:" data-field="goods_no"><i class="ion-md-create"></i> </a> </th>
                    <th scope="col">重量(克)&nbsp;<a class="batch-set" title="批量设置" href="javascript:" data-field="weight"><i class="ion-md-create"></i> </a> </th>
                    <th scope="col">销售价&nbsp;<a class="batch-set" title="批量设置" href="javascript:" data-field="price"><i class="ion-md-create"></i> </a></th>
                    <th scope="col">市场价&nbsp;<a class="batch-set" title="批量设置" href="javascript:" data-field="market_price"><i class="ion-md-create"></i> </a></th>
                    <th scope="col">成本价&nbsp;<a class="batch-set" title="批量设置" href="javascript:" data-field="cost_price"><i class="ion-md-create"></i> </a></th>
                    <th scope="col">库存&nbsp;<a class="batch-set" title="批量设置" href="javascript:" data-field="storage"><i class="ion-md-create"></i> </a></th>
                    <th scope="col">操作</th>
                </tr>
                </thead>
                <tbody>
                    <foreach name="skus" item="sku" key="k">
                    <tr data-idx="{$k}">
                        <foreach name="sku['specs']" item="spec" key="sk">
                            <td><input type="hidden" name="skus[{$k}][specs][{$sk}]" value="{$spec}" />{$spec}</td>
                        </foreach>
                        <td>
                            <input type="hidden" name="skus[{$k}][sku_id]" value="{$sku.sku_id}"/>
                            <input type="text" class="form-control" name="skus[{$k}][goods_no]" value="{$sku.goods_no}">
                        </td>
                        <td><input type="text" class="form-control" name="skus[{$k}][weight]" value="{$sku.weight}"> </td>
                        <td><input type="text" class="form-control" name="skus[{$k}][price]" value="{$sku.price}"> </td>
                        <td><input type="text" class="form-control" name="skus[{$k}][market_price]" value="{$sku.market_price}"> </td>
                        <td><input type="text" class="form-control" name="skus[{$k}][cost_price]" value="{$sku.cost_price}"> </td>
                        <td><input type="text" class="form-control" name="skus[{$k}][storage]" value="{$sku.storage}"> </td>
                        <td><a href="javascript:" class="btn btn-outline-secondary delete-btn"><i class="ion-md-trash"></i> </a> </td>
                    </tr>
                    </foreach>
                </tbody>
            </table>
        </div>

        <div class="form-group">
            <label for="product-content">商品介绍</label>
            <script id="product-content" name="content" type="text/plain">{$product.content|raw}</script>
        </div>

        <input type="hidden" name="id" value="{$product.id}">
        <button type="submit" class="btn btn-primary">{$id>0?'保存':'添加'}</button>
    </form>
        </div>
</div>
    </block>
<block name="script">
<!-- 配置文件 -->
<script type="text/javascript" src="__STATIC__/ueditor/ueditor.config.js"></script>
<!-- 编辑器源码文件 -->
<script type="text/javascript" src="__STATIC__/ueditor/ueditor.all.min.js"></script>
<!-- 实例化编辑器 -->
<script type="text/javascript">
    var ue = UE.getEditor('product-content',{
        toolbars: Toolbars.normal,
        initialFrameHeight:500,
        zIndex:100
    });
    jQuery(function ($) {
        var specs=null;
        var spec=null;
        var usespecs=[];
        var rows=null;
        var isready=false;

        function showSpec(body){
            var html=['<div class="list-group">'];
            if(specs && specs.length) {
                for (var i = 0; i < specs.length; i++) {
                    html.push('<a class="list-group-item list-group-item-action'+checkUsed(specs[i].id)+' d-flex justify-content-between" data-idx="'+i+'"><span class="title">' + specs[i].title + '</span><div><span class="badge badge-secondary badge-pill">'+specs[i].data.join('</span><span class="badge badge-secondary badge-pill">')+'</span></div></a>');
                }
            }else{
                html.push('<p class="empty">暂时没有规格数据</p>');
            }
            html.push('</div>');
            var htmlstr= html.join("\n");
            body.html(htmlstr);
            var lists=body.find('.list-group-item');
            lists.click(function() {
                if($(this).is('.disabled'))return;
                lists.removeClass('active');
                $(this).addClass('active');
                spec=specs[$(this).data('idx')];
            })
        }
        function checkUsed(id) {
            if(usespecs.indexOf(id)>-1){
                return ' disabled';
            }
            return '';
        }
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
            //console.log(usespecs);
            //console.log(spec_datas);
            var rowhtml='<tr data-idx="{@i}">\n' +
                '   {@specs}\n' +
                '   <td>\n' +
                '       <input type="text" class="form-control field-goods_no" name="skus[{@i}][goods_no]" value="{@goods_no}">\n' +
                '   </td>\n' +
                '   <td><input type="text" class="form-control field-weight" name="skus[{@i}][weight]" value="{@weight}"> </td>\n' +
                '   <td><input type="text" class="form-control field-price" name="skus[{@i}][price]" value="{@price}"> </td>\n' +
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
                var data = {
                    specs: spec_cell(mixed_specs[i],i),
                    i: i,
                    goods_no: '',
                    weight: '',
                    price: '',
                    market_price: '',
                    cost_price: '',
                    storage: ''
                };
                allhtml.push(rowhtml.compile(data));
            }

            $('.spec-table tbody').html(allhtml.join('\n'));
        }
        function spec_cell(arr,idx) {
            var specs=[];
            for(var i=0;i<arr.length;i++){
                specs.push('<td><input type="hidden" name="skus['+idx+'][specs]['+usespecs[i]+']" value="'+arr[i]+'" />'+arr[i]+'</td>')
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
        $('.addspecbtn').click(function (e) {
            var dlg=new Dialog({
                'onshow':function (body) {
                    if(!specs){
                        $.ajax({
                            'url':'{:url("get_specs")}',
                            'dataType':'JSON',
                            'success':function (json) {
                                specs=json.lists;
                                showSpec(body);
                            }
                        })
                    }else{
                        showSpec(body);
                    }
                },
                'onsure':function(){
                    if(!spec){
                        toastr.info('请选择规格');
                        return false;
                    }
                    //console.log(spec);
                    $('.spec-groups').append(('<div class="spec-row d-flex spec-{@id}" data-specid="{@id}">\n' +
                        '   <input type="hidden" name="spec_data[{@id}][title]" value="{@title}"/>\n'+
                        '   <label>{@title}</label>\n' +
                        '   <div class="form-control col"><input type="text" class="taginput" value="{@data}" ></div>\n'+
                        '   <div class="delete"><a href="javascript:" class="btn btn-outline-secondary"><i class="ion-md-trash"></i> </a> </div>\n' +
                    '</div>').compile(spec));
                    var lastrow=$('.spec-groups .spec-row').eq(-1);
                    lastrow.find('.taginput').tags('spec_data['+spec.id+'][data][]',resetSkus);
                    spec=null;
                }
            }).show('<p>数据加载中...</p>','添加规格');
        });

        $('.taginput').each(function () {
            $(this).tags('spec_data['+$(this).data('spec_id')+'][data][]',resetSkus);
        });
        $('.spec-groups').on('click','.delete .btn',function (e) {
            //console.log(e);
            var self=$(this);
            dialog.confirm('确定删除该规格？',function () {
                self.parents('.spec-row').remove();
                resetSkus();
            })
        });
        $('.batch-set').click(function (e) {
            var field=$(this).data('field');
            dialog.prompt('请输入要设置的数据',function(val) {
                if(field==='goods_no'){
                    if(!val){
                        toastr.warning('请填写货号');
                        return false;
                    }
                    $('.spec-table tbody .field-' + field).each(function () {
                        console.log(this)
                        var row=$(this).parents('tr');
                        $(this).val(val+'_'+row.data('idx'));
                    })
                }else {
                    val=parseFloat(val);
                    if(isNaN(val)){
                        toastr.warning('请填写数值');
                        return false;
                    }
                    $('.spec-table tbody .field-' + field).val(val);
                }
                return true;
            });
        });
        $('.spec-table').on('click','.delete-btn',function (e) {
            var row=$(this).parents('tr').eq(0);
            row.remove();
        });
        isready=true;
    });
</script>
</block>