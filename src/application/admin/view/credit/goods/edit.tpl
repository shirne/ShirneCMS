{extend name="public:base" /}

{block name="body"}
{include file="public/bread" menu="credit_goods_index" title="商品详情" /}
<div id="page-wrapper">
    <div class="page-header">{$id>0?'编辑':'添加'}商品</div>
    <div id="page-content">
        <form method="post" action="" enctype="multipart/form-data">
            <div class="form-row">
                <div class="col">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">商品名称</span> </div>
                            <input type="text" name="title" class="form-control" value="{$goods.title|default=''}"
                                id="goods-title" placeholder="输入商品名称">
                            <div class="input-group-prepend"><span class="input-group-text">单位</span> </div>
                            <input type="text" name="unit" class="form-control" value="{$goods.unit|default=''}"
                                id="goods-unit" style="max-width:50px;" placeholder="单位">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">商品简介</span> </div>
                            <input type="text" name="vice_title" class="form-control"
                                value="{$goods.vice_title|default=''}" id="goods-vice_title">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">商品货号</span> </div>
                            <input type="text" name="goods_no" class="form-control" value="{$goods.goods_no|default=''}"
                                id="goods-goods_no" placeholder="输入商品货号">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">商品分类</span> </div>
                            <select name="cate_id" id="goods-cate" class="form-control">
                                {foreach $category as $key => $v}
                                <option value="{$v.id}" {$goods['cate_id']==$v['id']?'selected':""}
                                    data-props="{$v['props']}">{$v.html} {$v.title}
                                </option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">商品主图</span>
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="upload_image" />
                                <label class="custom-file-label" for="upload_image">选择文件</label>
                            </div>
                        </div>
                        {if !empty($goods['image'])}
                        <figure class="figure">
                            <img src="{$goods.image}" class="figure-img img-fluid rounded" alt="image">
                            <figcaption class="figure-caption text-center">{$goods.image}</figcaption>
                        </figure>
                        <input type="hidden" name="delete_image" value="{$goods.image}" />
                        {/if}
                    </div>
                </div>
                <div class="col-5">
                    <div class="card form-group">
                        <div class="card-header">商品属性</div>
                        <div class="card-body">
                            <div class="form-row">
                                <label class="col-3">是否发布</label>
                                <div class="form-group col">
                                    <div class="btn-group btn-group-toggle btn-group-sm" data-toggle="buttons">
                                        <label class="btn btn-outline-secondary{$goods['status']=='1'?' active':''}">
                                            <input type="radio" name="status" value="1" autocomplete="off"
                                                {$goods['status']=='1' ?'checked':''}>是
                                        </label>
                                        <label class="btn btn-outline-secondary{$goods['status']=='0'?' active':''}">
                                            <input type="radio" name="status" value="0" autocomplete="off"
                                                {$goods['status']=='0' ?'checked':''}>否
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <label class="col-3">商品售价</label>
                                <div class="form-group col">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="price"
                                            value="{$goods.price|default=''}" />
                                        <div class="input-group-append">
                                            <span class="input-group-text">积分</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <label class="col-3">市场价格</label>
                                <div class="form-group col">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">￥</span>
                                        </div>
                                        <input type="text" class="form-control" name="market_price"
                                            value="{$goods.market_price|default=''}" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <label class="col-3">库存</label>
                                <div class="form-group col">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="storage"
                                            value="{$goods.storage|default=''}" />
                                        <div class="input-group-append">
                                            <span class="input-group-text">已售</span>
                                        </div>
                                        <span class="form-control">{$goods.sale|default=''}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <label class="col-3">排序</label>
                                <div class="form-group col">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="sort"
                                            value="{$goods.sort|default=''}" />
                                    </div>
                                    <div class="help-block text-muted">
                                        排序越大越靠前
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <label class="col-3">限制购买</label>
                                <div class="form-group col">
                                    <div class="btn-group btn-group-toggle btn-group-sm" data-toggle="buttons">
                                        {volist name="levels" id="lv" key="k"}
                                        <label
                                            class="btn btn-outline-secondary{:fix_in_array($k,$goods['levels'])?' active':''}">
                                            <input type="checkbox" name="levels[]" value="{$k}" autocomplete="off"
                                                {:fix_in_array($k,$goods['levels'])?'checked':''}>{$lv.level_name}
                                        </label>
                                        {/volist}
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <label class="col-3">兑换数量</label>
                                <div class="form-group col">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="limit"
                                            value="{$goods.limit|default='0'}" />
                                    </div>
                                    <div class="help-block text-muted">
                                        填写0不限制
                                    </div>
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
                        {foreach $goods['prop_data']??[] as $k => $prop}
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" style="max-width:120px;" name="prop_data[keys][]"
                                value="{$k}" />
                            <input type="text" class="form-control" name="prop_data[values][]" value="{$prop}" />
                            <div class="input-group-append delete"><a href="javascript:"
                                    class="btn btn-outline-secondary"><i class="ion-md-trash"></i> </a> </div>
                        </div>
                        {/foreach}
                    </div>
                    <a href="javascript:" class="btn btn-outline-dark btn-sm addpropbtn"><i class="ion-md-add"></i>
                        添加属性</a>
                </div>
            </div>

            <div class="form-group">
                <label for="goods-content">商品介绍</label>
                <script id="goods-content" name="content" type="text/plain">{$goods.content|default=''|raw}</script>
            </div>

            <input type="hidden" name="id" value="{$goods.id|default=''}">
            <button type="submit" class="btn btn-primary">{$id>0?'保存':'添加'}</button>
        </form>
    </div>
</div>
{/block}
{block name="script"}
<!-- 配置文件 -->
<script type="text/javascript" src="__STATIC__/ueditor/ueditor.config.js"></script>
<!-- 编辑器源码文件 -->
<script type="text/javascript" src="__STATIC__/ueditor/ueditor.all.min.js"></script>
<!-- 实例化编辑器 -->
<script type="text/javascript">
    var ue = UE.getEditor('goods-content', {
        toolbars: Toolbars.normal,
        initialFrameHeight: 500,
        zIndex: 100
    });
    jQuery(function ($) {
        $('.taginput').each(function () {
            $(this).tags('spec_data[' + $(this).data('spec_id') + '][data][]', resetSkus);
        });
        $('.prop-groups').on('click', '.delete .btn', function (e) {
            var self = $(this);
            dialog.confirm('确定删除该属性？', function () {
                self.parents('.input-group').remove();
            })
        });
        function addProp(key, value) {
            $('.prop-groups').append('<div class="input-group mb-2" >\n' +
                '                            <input type="text" class="form-control" style="max-width:120px;" name="prop_data[keys][]" value="' + (key ? key : '') + '" />\n' +
                '                            <input type="text" class="form-control" name="prop_data[values][]" value="' + (value ? value : '') + '" />\n' +
                '                            <div class="input-group-append delete"><a href="javascript:" class="btn btn-outline-secondary"><i class="ion-md-trash"></i> </a> </div>\n' +
                '                        </div>');
        }
        $('.addpropbtn').click(function (e) {
            addProp();
        });

        function changeCategory(select, force) {
            var option = $(select).find('option:selected');
            var curProps = [];
            var cid = $(option).val();

            var props = $(option).data('props') || [];
            $('.prop-groups .input-group').each(function () {
                var input = $(this).find('input');
                var prop = input.val().trim();
                if (input.eq(1).val().trim() === '') {
                    if (props.indexOf(prop) < 0) {
                        $(this).remove();
                    } else {
                        curProps.push(prop);
                    }
                } else {
                    curProps.push(prop);
                }
            });
            for (var i = 0; i < props.length; i++) {
                if (curProps.indexOf(props[i]) < 0) {
                    addProp(props[i]);
                }
            }
        }

        $('#goods-cate').change(function (e) {
            changeCategory(this);
        });
        if ('add' === "{$goods['id']?'':'add'}") {
            changeCategory($('#goods-cate'), true);
        }

    });
</script>
{/block}