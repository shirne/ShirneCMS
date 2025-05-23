{extend name="public:base"/}

{block name="body"}

{include file="public/bread" menu="shop_category_index" title="分类信息"/}

<div id="page-wrapper">
    <div class="page-header">{$id>0?'编辑':'添加'}分类</div>
    <div class="page-content">
        <form method="post" action="" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group col">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">分类名称</span>
                        </div>
                        <input type="text" name="title" class="form-control" value="{$model.title|default=''}"
                            placeholder="输入分类名称" />
                    </div>
                </div>
                <div class="form-group col">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">简称</span>
                        </div>
                        <input type="text" name="short" class="form-control" value="{$model.short|default=''}" />
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">父分类</span>
                        </div>
                        <select name="pid" class="form-control">
                            <option value="">顶级分类</option>
                            {foreach $cate as $key => $v}
                            <option value="{$v.id}" {$model['pid']==$v['id']?'selected':''}?>{$v.html} {$v.title}
                            </option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="form-group col">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">分类别名</span>
                        </div>
                        <input type="text" name="name" class="form-control"
                            {$model['is_lock']?'readonly="readonly"':''} value="{$model.name|default=''}" placeholder="输入分类别名,不能和其他分类别名重复">
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">分类图标</span>
                            </div>
                            <div class="custom-file">
                            <input type="file" class="custom-file-input" name="upload_icon"/>
                                <label class="custom-file-label" for="upload_icon">选择文件</label>
                            </div>
                        </div>
                        {if !empty($model[' icon'])} <figure class="figure">
                        <img src="{$model.icon}" class="figure-img img-fluid rounded" alt="icon">
                        <figcaption class="figure-caption text-center">{$model.icon}</figcaption>
                        </figure>
                        <input type="hidden" name="delete_icon" value="{$model.icon}" />
                        {/if}
                    </div>
                    <div class="form-group col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">分类图片</span>
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="upload_image" />
                                <label class="custom-file-label" for="upload_image">选择文件</label>
                            </div>
                        </div>
                        {if !empty($model['image'])}
                        <figure class="figure">
                            <img src="{$model.image}" class="figure-img img-fluid rounded" alt="image">
                            <figcaption class="figure-caption text-center">{$model.image}</figcaption>
                        </figure>
                        <input type="hidden" name="delete_image" value="{$model.image}" />
                        {/if}
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">排序</span>
                            </div>
                            <input type="text" name="sort" class="form-control" value="{$model.sort|default=''}"
                                placeholder="排序按从小到大">
                        </div>
                    </div>
                    <div class="form-group col">
                        <label style="width: 80px;">热门</label>
                        <div class="btn-group btn-group-toggle btn-group-sm" data-toggle="buttons">
                            <label class="btn btn-outline-secondary{$model['is_hot']=='1'?' active':''}">
                                <input type="radio" name="is_hot" value="1" autocomplete="off" {$model['is_hot']=='1'
                                    ?'checked':''}>是
                            </label>
                            <label class="btn btn-outline-secondary{$model['is_hot']=='0'?' active':''}">
                                <input type="radio" name="is_hot" value="0" autocomplete="off" {$model['is_hot']=='0'
                                    ?'checked':''}>否
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">关键词</span>
                        </div>
                        <input type="text" name="keywords" class="form-control" value="{$model.keywords|default=''}"
                            placeholder="请输入SEO关键词(选填)">
                    </div>
                </div>
                <div class="form-group">
                    <label for="description">产品属性</label>
                    <div class="form-control">
                        <input type="text" class="taginput" value="{$model.props|default=''|implode_cmp}"
                            placeholder="填写多个值以,分割" />
                    </div>
                </div>
                {if !empty($specs)}
                <div class="form-group">
                    <label for="description">绑定规格</label>
                    <div class="btn-group-toggle">
                        {volist name="specs" id="val" key="k"}
                        <label
                            class="btn btn-outline-secondary{:fix_in_array($k,$model['specs'])?' active':''} mr-1 mb-1"
                            data-toggle="button">
                            <input type="checkbox" name="specs[]" value="{$k}" autocomplete="off"
                                {:fix_in_array($k,$model['specs'])?' checked':''}>{$val}
                        </label>
                        {/volist}
                    </div>
                </div>
                {/if}
                <div class="form-group">
                    <label for="description">描述信息</label>
                    <textarea name="description" cols="30" rows="10" class="form-control"
                        placeholder="请输入分类描述(选填)">{$model.description|default=''}</textarea>
                </div>
                <input type="hidden" name="id" value="{$model.id|default=''}">
                <button type="submit" class="btn btn-primary">{$id>0?'保存':'添加'}</button>
        </form>
    </div>
</div>
{/block}
{block name="script"}
<script type="text/javascript">
    jQuery(function ($) {
        $('.taginput').tags('props[]');
    })
</script>
{/block}