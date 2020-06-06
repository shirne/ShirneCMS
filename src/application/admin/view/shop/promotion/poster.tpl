<extend name="public:base" />

<block name="body">
<include file="public/bread" menu="shop_promotion_index" title="商城配置" />
<div id="page-wrapper">
    <div class="page-header">分享图配置</div>
    <div id="page-content">
    <form method="post" action="" enctype="multipart/form-data">
        
        <div class="form-row form-group">
            <label for="v-poster_background" class="form-label w-100px text-right align-top">{$setting['poster_background']['title']}</label>
            <div class="col-5">
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="upload_poster_background"/>
                        <label class="custom-file-label" for="upload_poster_background">选择文件</label>
                    </div>
                </div>
                <if condition="$setting['poster_background']['value']">
                    <figure class="figure">
                        <img src="{$setting['poster_background']['value']}" class="figure-img img-fluid rounded" alt="image">
                        <figcaption class="figure-caption text-center">{$setting['poster_background']['value']}</figcaption>
                    </figure>
                    <input type="hidden" name="delete_poster_background" value="{$setting['poster_background']['value']}"/>
                </if>
            </div>
            <div class="col">
                <div class="text-muted">{$setting['poster_background']['description']}</div>
            </div>
        </div>
        <div class="form-row form-group">
            <label for="v-poster_avatar" class="form-label w-100px text-right align-middle">{$setting['poster_avatar']['title']}</label>
            <div class="col-5">
                <div class="input-group">
                    <span class="input-group-prepend"><span class="input-group-text">左边距</span></span>
                    <input type="text" class="form-control" name="v-poster_avatar[x]" value="{$setting['poster_avatar']['value']['x']}" placeholder="">
                    <span class="input-group-prepend"><span class="input-group-text">上边距</span></span>
                    <input type="text" class="form-control" name="v-poster_avatar[y]" value="{$setting['poster_avatar']['value']['y']}" placeholder="">
                    <span class="input-group-prepend"><span class="input-group-text">尺寸</span></span>
                    <input type="text" class="form-control" name="v-poster_avatar[width]" value="{$setting['poster_avatar']['value']['width']}" placeholder="">
                </div>
            </div>
            <div class="col">
                <div class="text-muted">{$setting['poster_avatar']['description']}</div>
            </div>
        </div>
        <div class="form-row form-group">
            <label for="v-poster_nickname" class="form-label w-100px text-right align-middle">{$setting['poster_nickname']['title']}</label>
            <div class="col-6">
                <div class="input-group">
                    <span class="input-group-prepend"><span class="input-group-text">左边距</span></span>
                    <input type="text" class="form-control" name="v-poster_nickname[x]" value="{$setting['poster_nickname']['value']['x']}" placeholder="">
                    <span class="input-group-prepend"><span class="input-group-text">上边距</span></span>
                    <input type="text" class="form-control" name="v-poster_nickname[y]" value="{$setting['poster_nickname']['value']['y']}" placeholder="">
                    <span class="input-group-prepend"><span class="input-group-text">字号</span></span>
                    <input type="text" class="form-control" name="v-poster_nickname[size]" value="{$setting['poster_nickname']['value']['size']}" placeholder="">
                    <span class="input-group-prepend"><span class="input-group-text">颜色</span></span>
                    <input type="text" class="form-control" name="v-poster_nickname[color]" value="{$setting['poster_nickname']['value']['color']}" placeholder="">
                </div>
            </div>
            <div class="col">
                <div class="text-muted">{$setting['poster_nickname']['description']}</div>
            </div>
        </div>
        <div class="form-row form-group">
            <label for="v-poster_qrcode" class="form-label w-100px text-right align-middle">{$setting['poster_qrcode']['title']}</label>
            <div class="col-5">
                <div class="input-group">
                    <span class="input-group-prepend"><span class="input-group-text">左边距</span></span>
                    <input type="text" class="form-control" name="v-poster_qrcode[x]" value="{$setting['poster_qrcode']['value']['x']}" placeholder="">
                    <span class="input-group-prepend"><span class="input-group-text">上边距</span></span>
                    <input type="text" class="form-control" name="v-poster_qrcode[y]" value="{$setting['poster_qrcode']['value']['y']}" placeholder="">
                    <span class="input-group-prepend"><span class="input-group-text">尺寸</span></span>
                    <input type="text" class="form-control" name="v-poster_qrcode[width]" value="{$setting['poster_qrcode']['value']['width']}" placeholder="">
                </div>
            </div>
            <div class="col">
                <div class="text-muted">{$setting['poster_qrcode']['description']}</div>
            </div>
        </div>
        <div class="form-row form-group">
            <label for="v-poster_qrlogo" class="form-label w-100px text-right align-middle">{$setting['poster_qrlogo']['title']}</label>
            <div class="col-5">
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="upload_poster_qrlogo"/>
                        <label class="custom-file-label" for="upload_poster_qrlogo">选择文件</label>
                    </div>
                </div>
                <if condition="$setting['poster_qrlogo']['value']">
                    <figure class="figure">
                        <img src="{$setting['poster_qrlogo']['value']}" class="figure-img img-fluid rounded" alt="image">
                        <figcaption class="figure-caption text-center">{$setting['poster_qrlogo']['value']}</figcaption>
                    </figure>
                    <input type="hidden" name="delete_poster_qrlogo" value="{$setting['poster_qrlogo']['value']}"/>
                </if>
            </div>
            <div class="col">
                <div class="text-muted">{$setting['shop_pagetitle']['description']}</div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">保存配置</button>
    </form>
    </div>
</div>
    </block>
<block name="script">
<script type="text/javascript">
    jQuery(function ($) {


    });
</script>
</block>