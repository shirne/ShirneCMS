{extend name="public:base" /}

{block name="body"}

{include file="public/bread" menu="adv_index" title="广告位详情" /}

<div id="page-wrapper">
    <div class="page-header">{$id>0?'编辑':'添加'}广告位</div>
    <div class="page-content">
        <form method="post" class="page-form" action="">
            <div class="form-group">
                <label for="title">位置名称</label>
                <input type="text" name="title" class="form-control" value="{$model.title|default=''}"
                    placeholder="位置名称">
            </div>
            <div class="form-group">
                <label for="flag">调用标识</label>
                <input type="text" name="flag" class="form-control" value="{$model.flag|default=''}" placeholder="调用标识">
            </div>
            <div class="form-group">
                <label for="type">类型</label>
                <label class="radio-inline">
                    <input type="radio" name="type" value="1" {if $model['type']==1}checked="checked" {/if}>视频
                </label>
                <label class="radio-inline">
                    <input type="radio" name="type" value="0" {if $model['type']==0}checked="checked" {/if}>图片
                </label>
            </div>
            <div class="form-group">
                <label for="image">广告位尺寸</label>
                <div class="form-row">
                    <div class="input-group col">
                        <div class="input-group-prepend">
                            <span class="input-group-text">宽</span>
                        </div>
                        <input type="text" name="width" class="form-control fromdate"
                            value="{$model.width|default=0}" />
                    </div>
                    <div class="input-group col">
                        <div class="input-group-prepend">
                            <span class="input-group-text">高</span>
                        </div>
                        <input type="text" name="height" class="form-control todate"
                            value="{$model.height|default=0}" />
                    </div>
                </div>

            </div>
            <div class="form-group">
                <label>自定义字段</label>
                <div class="form-group ">
                    <div class="prop-groups">
                        {if !empty($model['ext_set']) and !empty($model['ext_set']['key'])}
                        {foreach $model['ext_set']['key'] as $k => $key}
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" style="max-width:120px;" name="ext_set[key][]"
                                value="{$key}" />
                            <input type="text" class="form-control" name="ext_set[value][]"
                                value="{$model['ext_set']['value'][$k]}" />
                            <div class="input-group-append delete"><a href="javascript:"
                                    class="btn btn-outline-secondary"><i class="ion-md-trash"></i> </a> </div>
                        </div>
                        {/foreach}
                        {/if}
                    </div>
                    <a href="javascript:" class="btn btn-outline-dark btn-sm addpropbtn"><i class="ion-md-add"></i>
                        添加字段</a>
                </div>
                <div class="form-text text-muted">
                    自定义字段分[字段名]和[字段标题]，字段名必须使用字母和数字这些组合，字段标题为文字。<br />
                    设置好自定义字段在添加广告项时可以额外录入一些内容，供设计师调用
                </div>
            </div>
            <div class="form-group">
                <label for="cc">状态</label>
                <label class="radio-inline">
                    <input type="radio" name="status" value="1" {if $model['status']==1}checked="checked" {/if}>显示
                </label>
                <label class="radio-inline">
                    <input type="radio" name="status" value="0" {if $model['status']==0}checked="checked" {/if}>隐藏
                </label>
            </div>
            <div class="form-group submit-btn">
                <input type="hidden" name="id" value="{$model.id|default=''}">
                <button type="submit" class="btn btn-primary">{$id>0?'保存':'添加'}</button>
            </div>
        </form>
    </div>
</div>
{/block}

{block name="script"}
<script type="text/javascript">
    jQuery(function ($) {
        $('.addpropbtn').click(function (e) {
            $('.prop-groups').append('<div class="input-group mb-2" >\n' +
                '                            <input type="text" class="form-control" style="max-width:120px;" name="ext_set[key][]" />\n' +
                '                            <input type="text" class="form-control" name="ext_set[value][]" />\n' +
                '                            <div class="input-group-append delete"><a href="javascript:" class="btn btn-outline-secondary"><i class="ion-md-trash"></i> </a> </div>\n' +
                '                        </div>');
        });
        $('.prop-groups').on('click', '.delete .btn', function (e) {
            var self = $(this);
            dialog.confirm('确定删除该字段？', function () {
                self.parents('.input-group').remove();
            })
        });
    });
</script>
{/block}