{extend name="public:base" /}

{block name="body"}
{include file="public/bread" menu="shop_promotion_index" title="商城配置" /}
<div id="page-wrapper">
    <div class="page-header">分享图配置</div>
    <div id="page-content">
    <form method="post" action="" enctype="multipart/form-data">
        
        <div class="form-row form-group">
            <label for="v-poster_background" class="form-label w-100px text-right align-top">{$setting['poster_background']['title']|default='分享图背景'}</label>
            <div class="col-5">
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="upload_poster_background"/>
                        <label class="custom-file-label" for="upload_poster_background">选择文件</label>
                    </div>
                </div>
                {if !empty($setting['poster_background']['value'])}
                    <figure class="figure">
                        <img src="{$setting['poster_background']['value']}" class="figure-img img-fluid rounded" alt="image">
                        <figcaption class="figure-caption text-center">{$setting['poster_background']['value']|default=''}</figcaption>
                    </figure>
                    <input type="hidden" name="delete_poster_background" value="{$setting['poster_background']['value']}"/>
                {/if}
            </div>
            <div class="col">
                <div class="text-muted">{$setting['poster_background']['description']|default=''}</div>
            </div>
        </div>
        <div class="form-row form-group">
            <label for="v-poster_bgset" class="form-label w-100px text-right align-middle">{$setting['poster_bgset']['title']|default='背景设置'}</label>
            <div class="col-5">
                <div class="btn-group btn-group-toggle mregopengroup" data-toggle="buttons">
                    {foreach $setting['poster_bgset']['data'] as $k=>$value}
                        {if $k==$setting['poster_bgset']['value']}
                            <label class="btn btn-outline-secondary active">
                                <input type="radio" name="v-poster_bgset" value="{$k}" autocomplete="off" checked> {$value}
                            </label>
                        {else}
                            <label class="btn btn-outline-secondary">
                                <input type="radio" name="v-poster_bgset" value="{$k}" autocomplete="off"> {$value}
                            </label>
                        {/if}
                    {/foreach}
                </div>
            </div>
            <div class="col">
                <div class="text-muted">{$setting['poster_bgset']['description']}</div>
            </div>
        </div>
        <div class="form-row form-group">
            <label for="v-poster_avatar" class="form-label w-100px text-right align-middle">{$setting['poster_avatar']['title']|default='头像设置'}</label>
            <div class="col-5">
                <div class="input-group">
                    <span class="input-group-prepend"><span class="input-group-text">左边距</span></span>
                    <input type="text" class="form-control" name="v-poster_avatar[x]" value="{$setting['poster_avatar']['value']['x']|default=''}" placeholder="">
                    <span class="input-group-prepend"><span class="input-group-text">上边距</span></span>
                    <input type="text" class="form-control" name="v-poster_avatar[y]" value="{$setting['poster_avatar']['value']['y']|default=''}" placeholder="">
                    <span class="input-group-prepend"><span class="input-group-text">尺寸</span></span>
                    <input type="text" class="form-control" name="v-poster_avatar[width]" value="{$setting['poster_avatar']['value']['width']|default=''}" placeholder="">
                </div>
            </div>
            <div class="col">
                <div class="text-muted">{$setting['poster_avatar']['description']|default=''}</div>
            </div>
        </div>
        <div class="form-row form-group">
            <label for="v-poster_nickname" class="form-label w-100px text-right align-middle">{$setting['poster_nickname']['title']|default='昵称设置'}</label>
            <div class="col-8">
                <div class="input-group">
                    <span class="input-group-prepend"><span class="input-group-text">对齐</span></span>
                    <select name="v-poster_nickname[align]" class="form-control">
                        <option value="left" {$setting['poster_nickname']['value']['align']=='left'?'selected':''}>靠左</option>
                        <option value="center" {$setting['poster_nickname']['value']['align']=='center'?'selected':''}>居中</option>
                        <option value="right" {$setting['poster_nickname']['value']['align']=='right'?'selected':''}>靠右</option>
                    </select>
                    <span class="input-group-prepend"><span class="input-group-text">前缀</span></span>
                    <input type="text" class="form-control" name="v-poster_nickname[prefix]" value="{$setting['poster_nickname']['value']['prefix']}" placeholder="">
                    <span class="input-group-prepend"><span class="input-group-text">左边距</span></span>
                    <input type="text" class="form-control" name="v-poster_nickname[x]" value="{$setting['poster_nickname']['value']['x']|default=''}" placeholder="">
                    <span class="input-group-prepend"><span class="input-group-text">上边距</span></span>
                    <input type="text" class="form-control" name="v-poster_nickname[y]" value="{$setting['poster_nickname']['value']['y']|default=''}" placeholder="">
                    <span class="input-group-prepend"><span class="input-group-text">字号</span></span>
                    <input type="text" class="form-control" name="v-poster_nickname[size]" value="{$setting['poster_nickname']['value']['size']|default=''}" placeholder="">
                    <span class="input-group-prepend"><span class="input-group-text">颜色</span></span>
                    <input type="text" class="form-control" name="v-poster_nickname[color]" value="{$setting['poster_nickname']['value']['color']|default=''}" placeholder="">
                </div>
            </div>
            <div class="col">
                <div class="text-muted">{$setting['poster_nickname']['description']|default=''}</div>
            </div>
        </div>
        <div class="form-row form-group">
            <label for="v-poster_agentcode" class="form-label w-100px text-right align-middle">{$setting['poster_agentcode']['title']|default='推荐码设置'}</label>
            <div class="col-8">
                <div class="input-group">
                    <span class="input-group-prepend"><span class="input-group-text">对齐</span></span>
                    <select name="v-poster_agentcode[align]" class="form-control">
                        <option value="left" {$setting['poster_agentcode']['value']['align']=='left'?'selected':''}>靠左</option>
                        <option value="center" {$setting['poster_agentcode']['value']['align']=='center'?'selected':''}>居中</option>
                        <option value="right" {$setting['poster_agentcode']['value']['align']=='right'?'selected':''}>靠右</option>
                    </select>
                    <span class="input-group-prepend"><span class="input-group-text">前缀</span></span>
                    <input type="text" class="form-control" name="v-poster_agentcode[prefix]" value="{$setting['poster_agentcode']['value']['prefix']|default=''}" placeholder="">
                    <span class="input-group-prepend"><span class="input-group-text">左边距</span></span>
                    <input type="text" class="form-control" name="v-poster_agentcode[x]" value="{$setting['poster_agentcode']['value']['x']|default=''}" placeholder="">
                    <span class="input-group-prepend"><span class="input-group-text">上边距</span></span>
                    <input type="text" class="form-control" name="v-poster_agentcode[y]" value="{$setting['poster_agentcode']['value']['y']|default=''}" placeholder="">
                    <span class="input-group-prepend"><span class="input-group-text">字号</span></span>
                    <input type="text" class="form-control" name="v-poster_agentcode[size]" value="{$setting['poster_agentcode']['value']['size']|default=''}" placeholder="">
                    <span class="input-group-prepend"><span class="input-group-text">颜色</span></span>
                    <input type="text" class="form-control" name="v-poster_agentcode[color]" value="{$setting['poster_agentcode']['value']['color']|default=''}" placeholder="">
                </div>
            </div>
            <div class="col">
                <div class="text-muted">{$setting['poster_agentcode']['description']|default=''}</div>
            </div>
        </div>
        <div class="form-row form-group">
            <label for="v-poster_qrcode" class="form-label w-100px text-right align-middle">{$setting['poster_qrcode']['title']|default='二维码设置'}</label>
            <div class="col-5">
                <div class="input-group">
                    <span class="input-group-prepend"><span class="input-group-text">左边距</span></span>
                    <input type="text" class="form-control" name="v-poster_qrcode[x]" value="{$setting['poster_qrcode']['value']['x']|default=''}" placeholder="">
                    <span class="input-group-prepend"><span class="input-group-text">上边距</span></span>
                    <input type="text" class="form-control" name="v-poster_qrcode[y]" value="{$setting['poster_qrcode']['value']['y']|default=''}" placeholder="">
                    <span class="input-group-prepend"><span class="input-group-text">尺寸</span></span>
                    <input type="text" class="form-control" name="v-poster_qrcode[width]" value="{$setting['poster_qrcode']['value']['width']|default=''}" placeholder="">
                </div>
            </div>
            <div class="col">
                <div class="text-muted">{$setting['poster_qrcode']['description']|default=''}</div>
            </div>
        </div>
        <div class="form-row form-group">
            <label for="v-poster_qrlogo" class="form-label w-100px text-right align-middle">{$setting['poster_qrlogo']['title']|default='二维码LOGO'}</label>
            <div class="col-5">
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="upload_poster_qrlogo"/>
                        <label class="custom-file-label" for="upload_poster_qrlogo">选择文件</label>
                    </div>
                </div>
                {if !empty($setting['poster_qrlogo']['value'])}
                    <figure class="figure">
                        <img src="{$setting['poster_qrlogo']['value']}" class="figure-img img-fluid rounded" alt="image">
                        <figcaption class="figure-caption text-center">{$setting['poster_qrlogo']['value']}</figcaption>
                    </figure>
                    <input type="hidden" name="delete_poster_qrlogo" value="{$setting['poster_qrlogo']['value']}"/>
                {/if}
            </div>
            <div class="col">
                <div class="text-muted">{$setting['shop_pagetitle']['description']|default=''}</div>
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