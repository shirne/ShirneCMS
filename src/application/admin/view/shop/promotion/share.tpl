{extend name="public:base" /}

{block name="body"}
{include file="public/bread" menu="shop_promotion_index" title="商城配置" /}
<div id="page-wrapper">
    <div class="page-header">产品分享图配置</div>
    <div id="page-content">
        <form method="post" action="" enctype="multipart/form-data">

            <div class="form-row form-group">
                <label for="v-share_background"
                    class="form-label w-100px text-right align-top">{$setting['share_background']['title']}</label>
                <div class="col-5">
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="upload_share_background" />
                            <label class="custom-file-label" for="upload_share_background">选择文件</label>
                        </div>
                    </div>
                    {if $setting['share_background']['value']}
                    <figure class="figure">
                        <img src="{$setting['share_background']['value']}" class="figure-img img-fluid rounded"
                            alt="image">
                        <figcaption class="figure-caption text-center">{$setting['share_background']['value']}
                        </figcaption>
                    </figure>
                    <input type="hidden" name="delete_share_background"
                        value="{$setting['share_background']['value']}" />
                    {/if}
                </div>
                <div class="col">
                    <div class="text-muted">{$setting['share_background']['description']}</div>
                </div>
            </div>
            <div class="form-row form-group">
                <label for="v-share_bgset"
                    class="form-label w-100px text-right align-middle">{$setting['share_bgset']['title']}</label>
                <div class="col-5">
                    <div class="btn-group btn-group-toggle mregopengroup" data-toggle="buttons">
                        {foreach $setting['share_bgset']['data'] as $k => $value}
                        {if $k==$setting['share_bgset']['value']}
                        <label class="btn btn-outline-secondary active">
                            <input type="radio" name="v-share_bgset" value="{$k}" autocomplete="off" checked> {$value}
                        </label>
                        {else /}
                        <label class="btn btn-outline-secondary">
                            <input type="radio" name="v-share_bgset" value="{$k}" autocomplete="off"> {$value}
                        </label>
                        {/if}
                        {/foreach}
                    </div>
                </div>
                <div class="col">
                    <div class="text-muted">{$setting['share_bgset']['description']}</div>
                </div>
            </div>
            <div class="form-row form-group">
                <label for="v-share_avatar"
                    class="form-label w-100px text-right align-middle">{$setting['share_avatar']['title']}</label>
                <div class="col-5">
                    <div class="input-group">
                        <span class="input-group-prepend"><span class="input-group-text">左边距</span></span>
                        <input type="text" class="form-control" name="v-share_avatar[x]"
                            value="{$setting['share_avatar']['value']['x']}" placeholder="">
                        <span class="input-group-prepend"><span class="input-group-text">上边距</span></span>
                        <input type="text" class="form-control" name="v-share_avatar[y]"
                            value="{$setting['share_avatar']['value']['y']}" placeholder="">
                        <span class="input-group-prepend"><span class="input-group-text">尺寸</span></span>
                        <input type="text" class="form-control" name="v-share_avatar[width]"
                            value="{$setting['share_avatar']['value']['width']}" placeholder="">
                    </div>
                </div>
                <div class="col">
                    <div class="text-muted">{$setting['share_avatar']['description']}</div>
                </div>
            </div>
            <div class="form-row form-group">
                <label for="v-share_nickname"
                    class="form-label w-100px text-right align-middle">{$setting['share_nickname']['title']}</label>
                <div class="col-8">
                    <div class="input-group">
                        <span class="input-group-prepend"><span class="input-group-text">对齐</span></span>
                        <select name="v-share_nickname[align]" class="form-control">
                            <option value="left" {$setting['share_nickname']['value']['align']=='left' ?'selected':''}>
                                靠左</option>
                            <option value="center" {$setting['share_nickname']['value']['align']=='center'
                                ?'selected':''}>居中</option>
                            <option value="right" {$setting['share_nickname']['value']['align']=='right'
                                ?'selected':''}>靠右</option>
                        </select>
                        <span class="input-group-prepend"><span class="input-group-text">前缀</span></span>
                        <input type="text" class="form-control" name="v-share_nickname[prefix]"
                            value="{$setting['share_nickname']['value']['prefix']}" placeholder="">
                        <span class="input-group-prepend"><span class="input-group-text">后缀</span></span>
                        <input type="text" class="form-control" name="v-share_nickname[suffix]"
                            value="{$setting['share_nickname']['value']['suffix']}" placeholder="">
                        <span class="input-group-prepend"><span class="input-group-text">左边距</span></span>
                        <input type="text" class="form-control" name="v-share_nickname[x]"
                            value="{$setting['share_nickname']['value']['x']}" placeholder="">
                        <span class="input-group-prepend"><span class="input-group-text">上边距</span></span>
                        <input type="text" class="form-control" name="v-share_nickname[y]"
                            value="{$setting['share_nickname']['value']['y']}" placeholder="">
                        <span class="input-group-prepend"><span class="input-group-text">字号</span></span>
                        <input type="text" class="form-control" name="v-share_nickname[size]"
                            value="{$setting['share_nickname']['value']['size']}" placeholder="">
                        <span class="input-group-prepend"><span class="input-group-text">颜色</span></span>
                        <input type="text" class="form-control" name="v-share_nickname[color]"
                            value="{$setting['share_nickname']['value']['color']}" placeholder="">
                    </div>
                </div>
                <div class="col">
                    <div class="text-muted">{$setting['share_nickname']['description']}</div>
                </div>
            </div>
            <div class="form-row form-group">
                <label for="v-share_qrcode"
                    class="form-label w-100px text-right align-middle">{$setting['share_qrcode']['title']}</label>
                <div class="col-5">
                    <div class="input-group">
                        <span class="input-group-prepend"><span class="input-group-text">左边距</span></span>
                        <input type="text" class="form-control" name="v-share_qrcode[x]"
                            value="{$setting['share_qrcode']['value']['x']}" placeholder="">
                        <span class="input-group-prepend"><span class="input-group-text">上边距</span></span>
                        <input type="text" class="form-control" name="v-share_qrcode[y]"
                            value="{$setting['share_qrcode']['value']['y']}" placeholder="">
                        <span class="input-group-prepend"><span class="input-group-text">尺寸</span></span>
                        <input type="text" class="form-control" name="v-share_qrcode[width]"
                            value="{$setting['share_qrcode']['value']['width']}" placeholder="">
                    </div>
                </div>
                <div class="col">
                    <div class="text-muted">{$setting['share_qrcode']['description']}</div>
                </div>
            </div>
            <div class="form-row form-group">
                <label for="v-share_qrlogo"
                    class="form-label w-100px text-right align-middle">{$setting['share_qrlogo']['title']}</label>
                <div class="col-5">
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="upload_share_qrlogo" />
                            <label class="custom-file-label" for="upload_share_qrlogo">选择文件</label>
                        </div>
                    </div>
                    {if !empty($setting['share_qrlogo']['value'])}
                    <figure class="figure">
                        <img src="{$setting['share_qrlogo']['value']}" class="figure-img img-fluid rounded" alt="image">
                        <figcaption class="figure-caption text-center">{$setting['share_qrlogo']['value']}</figcaption>
                    </figure>
                    <input type="hidden" name="delete_share_qrlogo" value="{$setting['share_qrlogo']['value']}" />
                    {/if}
                </div>
                <div class="col">
                    <div class="text-muted">{$setting['shop_pagetitle']['description']}</div>
                </div>
            </div>

            <div class="form-row form-group">
                <label for="v-share_image"
                    class="form-label w-100px text-right align-middle">{$setting['share_image']['title']}</label>
                <div class="col-8">
                    <div class="input-group">
                        <span class="input-group-prepend"><span class="input-group-text">对齐</span></span>
                        <select name="v-share_image[align]" class="form-control">
                            <option value="left" {$setting['share_image']['value']['align']=='left' ?'selected':''}>靠左
                            </option>
                            <option value="center" {$setting['share_image']['value']['align']=='center' ?'selected':''}>
                                居中</option>
                            <option value="right" {$setting['share_image']['value']['align']=='right' ?'selected':''}>靠右
                            </option>
                        </select>
                        <span class="input-group-prepend"><span class="input-group-text">左边距</span></span>
                        <input type="text" class="form-control" name="v-share_image[x]"
                            value="{$setting['share_image']['value']['x']}" placeholder="">
                        <span class="input-group-prepend"><span class="input-group-text">上边距</span></span>
                        <input type="text" class="form-control" name="v-share_image[y]"
                            value="{$setting['share_image']['value']['y']}" placeholder="">
                        <span class="input-group-prepend"><span class="input-group-text">宽度</span></span>
                        <input type="text" class="form-control" name="v-share_image[width]"
                            value="{$setting['share_image']['value']['width']}" placeholder="">
                        <span class="input-group-prepend"><span class="input-group-text">高度</span></span>
                        <input type="text" class="form-control" name="v-share_image[height]"
                            value="{$setting['share_image']['value']['height']}" placeholder="">
                    </div>
                </div>
                <div class="col">
                    <div class="text-muted">{$setting['share_image']['description']}</div>
                </div>
            </div>
            <div class="form-row form-group">
                <label for="v-share_title"
                    class="form-label w-100px text-right align-middle">{$setting['share_title']['title']}</label>
                <div class="col-8">
                    <div class="input-group">
                        <span class="input-group-prepend"><span class="input-group-text">对齐</span></span>
                        <select name="v-share_title[align]" class="form-control">
                            <option value="left" {$setting['share_title']['value']['align']=='left' ?'selected':''}>靠左
                            </option>
                            <option value="center" {$setting['share_title']['value']['align']=='center' ?'selected':''}>
                                居中</option>
                            <option value="right" {$setting['share_title']['value']['align']=='right' ?'selected':''}>靠右
                            </option>
                        </select>
                        <span class="input-group-prepend"><span class="input-group-text">前缀</span></span>
                        <input type="text" class="form-control" name="v-share_title[prefix]"
                            value="{$setting['share_title']['value']['prefix']}" placeholder="">
                        <span class="input-group-prepend"><span class="input-group-text">后缀</span></span>
                        <input type="text" class="form-control" name="v-share_title[suffix]"
                            value="{$setting['share_title']['value']['suffix']}" placeholder="">
                        <span class="input-group-prepend"><span class="input-group-text">左边距</span></span>
                        <input type="text" class="form-control" name="v-share_title[x]"
                            value="{$setting['share_title']['value']['x']}" placeholder="">
                        <span class="input-group-prepend"><span class="input-group-text">上边距</span></span>
                        <input type="text" class="form-control" name="v-share_title[y]"
                            value="{$setting['share_title']['value']['y']}" placeholder="">
                        <span class="input-group-prepend"><span class="input-group-text">字号</span></span>
                        <input type="text" class="form-control" name="v-share_title[size]"
                            value="{$setting['share_title']['value']['size']}" placeholder="">
                        <span class="input-group-prepend"><span class="input-group-text">颜色</span></span>
                        <input type="text" class="form-control" name="v-share_title[color]"
                            value="{$setting['share_title']['value']['color']}" placeholder="">
                    </div>
                </div>
                <div class="col">
                    <div class="text-muted">{$setting['share_title']['description']}</div>
                </div>
            </div>
            <div class="form-row form-group">
                <label for="v-share_vice_title"
                    class="form-label w-100px text-right align-middle">{$setting['share_vice_title']['title']}</label>
                <div class="col-8">
                    <div class="input-group">
                        <span class="input-group-prepend"><span class="input-group-text">对齐</span></span>
                        <select name="v-share_vice_title[align]" class="form-control">
                            <option value="left" {$setting['share_vice_title']['value']['align']=='left'
                                ?'selected':''}>靠左</option>
                            <option value="center" {$setting['share_vice_title']['value']['align']=='center'
                                ?'selected':''}>居中</option>
                            <option value="right" {$setting['share_vice_title']['value']['align']=='right'
                                ?'selected':''}>靠右</option>
                        </select>
                        <span class="input-group-prepend"><span class="input-group-text">前缀</span></span>
                        <input type="text" class="form-control" name="v-share_vice_title[prefix]"
                            value="{$setting['share_vice_title']['value']['prefix']}" placeholder="">
                        <span class="input-group-prepend"><span class="input-group-text">后缀</span></span>
                        <input type="text" class="form-control" name="v-share_vice_title[suffix]"
                            value="{$setting['share_vice_title']['value']['suffix']}" placeholder="">
                        <span class="input-group-prepend"><span class="input-group-text">左边距</span></span>
                        <input type="text" class="form-control" name="v-share_vice_title[x]"
                            value="{$setting['share_vice_title']['value']['x']}" placeholder="">
                        <span class="input-group-prepend"><span class="input-group-text">上边距</span></span>
                        <input type="text" class="form-control" name="v-share_vice_title[y]"
                            value="{$setting['share_vice_title']['value']['y']}" placeholder="">
                        <span class="input-group-prepend"><span class="input-group-text">字号</span></span>
                        <input type="text" class="form-control" name="v-share_vice_title[size]"
                            value="{$setting['share_vice_title']['value']['size']}" placeholder="">
                        <span class="input-group-prepend"><span class="input-group-text">颜色</span></span>
                        <input type="text" class="form-control" name="v-share_vice_title[color]"
                            value="{$setting['share_vice_title']['value']['color']}" placeholder="">
                    </div>
                </div>
                <div class="col">
                    <div class="text-muted">{$setting['share_vice_title']['description']}</div>
                </div>
            </div>
            <div class="form-row form-group">
                <label for="v-share_price"
                    class="form-label w-100px text-right align-middle">{$setting['share_price']['title']}</label>
                <div class="col-8">
                    <div class="input-group">
                        <span class="input-group-prepend"><span class="input-group-text">对齐</span></span>
                        <select name="v-share_price[align]" class="form-control">
                            <option value="left" {$setting['share_price']['value']['align']=='left' ?'selected':''}>靠左
                            </option>
                            <option value="center" {$setting['share_price']['value']['align']=='center' ?'selected':''}>
                                居中</option>
                            <option value="right" {$setting['share_price']['value']['align']=='right' ?'selected':''}>靠右
                            </option>
                        </select>
                        <span class="input-group-prepend"><span class="input-group-text">前缀</span></span>
                        <input type="text" class="form-control" name="v-share_price[prefix]"
                            value="{$setting['share_price']['value']['prefix']}" placeholder="">
                        <span class="input-group-prepend"><span class="input-group-text">后缀</span></span>
                        <input type="text" class="form-control" name="v-share_price[suffix]"
                            value="{$setting['share_price']['value']['suffix']}" placeholder="">
                        <span class="input-group-prepend"><span class="input-group-text">左边距</span></span>
                        <input type="text" class="form-control" name="v-share_price[x]"
                            value="{$setting['share_price']['value']['x']}" placeholder="">
                        <span class="input-group-prepend"><span class="input-group-text">上边距</span></span>
                        <input type="text" class="form-control" name="v-share_price[y]"
                            value="{$setting['share_price']['value']['y']}" placeholder="">
                        <span class="input-group-prepend"><span class="input-group-text">字号</span></span>
                        <input type="text" class="form-control" name="v-share_price[size]"
                            value="{$setting['share_price']['value']['size']}" placeholder="">
                        <span class="input-group-prepend"><span class="input-group-text">颜色</span></span>
                        <input type="text" class="form-control" name="v-share_price[color]"
                            value="{$setting['share_price']['value']['color']}" placeholder="">
                    </div>
                </div>
                <div class="col">
                    <div class="text-muted">{$setting['share_price']['description']}</div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">保存配置</button>
        </form>
    </div>
</div>
{/block}
{block name="script"}
<script type="text/javascript">
    jQuery(function ($) {


    });
</script>
{/block}