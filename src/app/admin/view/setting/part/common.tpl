<div class="form-row form-group">
    <label for="v-site-webname" class="col-3 col-md-2 text-right align-middle">站点名</label>
    <div class="col-9 col-md-8 col-lg-6">
        <input type="text" class="form-control" name="v-site-webname" value="{$setting['site-webname']['value']}" placeholder="站点名">
    </div>
</div>
<div class="form-row form-group">
    <label for="v-site-keywords" class="col-3 col-md-2 text-right align-middle">关键词</label>
    <div class="col-9 col-md-8 col-lg-6">
        <input type="text" class="form-control" name="v-site-keywords" value="{$setting['site-keywords']['value']}" placeholder="关键词">
    </div>
</div>
<div class="form-row form-group">
    <label for="v-site-description" class="col-3 col-md-2 text-right align-middle">站点描述</label>
    <div class="col-9 col-md-8 col-lg-6">
        <input type="text" class="form-control" name="v-site-description" value="{$setting['site-description']['value']}" placeholder="站点描述">
    </div>
</div>
<div class="form-row form-group">
    <label for="v-site-weblogo" class="col-3 col-md-2 text-right align-middle">站点logo</label>
    <div class="col-9 col-md-8 col-lg-6">
        <div class="input-group">
            <div class="custom-file">
                <input type="file" class="custom-file-input" name="upload_site-weblogo">
                <label class="custom-file-label" for="upload_site-weblogo">选择文件</label>
            </div>
        </div>
        {if $setting['site-weblogo']['value']}
            <figure class="figure">
                <img src="{$setting['site-weblogo']['value']}" class="figure-img img-fluid rounded" alt="image">
                <figcaption class="figure-caption text-center">{$setting['site-weblogo']['value']}</figcaption>
            </figure>
            <input type="hidden" name="delete_site-weblogo" value="{$setting['site-weblogo']['value']}"/>
        {/if}
    </div>
</div>

<div class="form-row form-group">
    <label for="v-site-close" class="col-3 col-md-2 text-right align-middle">关闭站点</label>
    <div class="col-5">
        <div class="btn-group btn-group-toggle mregopengroup" data-toggle="buttons">
            <foreach name="setting['site-close']['data']" item="value" key="k">
                <if condition="$k==$setting['site-close']['value']">
                    <label class="btn btn-outline-secondary active">
                        <input type="radio" name="v-site-close" value="{$k}" autocomplete="off" checked> {$value}
                    </label>
                    <else />
                    <label class="btn btn-outline-secondary">
                        <input type="radio" name="v-site-close" value="{$k}" autocomplete="off"> {$value}
                    </label>
                </if>
            </foreach>
        </div>
    </div>
    <div class="col-4">
        <a href="{:FU('/')}?force=1" target="_blank" class="btn btn-link">强制访问</a>
    </div>
</div>
<div class="form-row form-group">
    <label for="v-site-close-desc" class="col-3 col-md-2 text-right align-middle">关闭说明</label>
    <div class="col-9 col-md-8 col-lg-6">
        <input type="text" class="form-control" name="v-site-close-desc" value="{$setting['site-close-desc']['value']}" >
    </div>
</div>

<div class="form-row form-group">
    <label for="v-site-shareimg" class="col-3 col-md-2 text-right align-middle">默认分享图</label>
    <div class="col-9 col-md-8 col-lg-6">
        <div class="input-group">
            <div class="custom-file">
                <input type="file" class="custom-file-input" name="upload_site-shareimg">
                <label class="custom-file-label" for="upload_site-shareimg">选择文件</label>
            </div>
        </div>
        {if $setting['site-shareimg']['value']}
            <figure class="figure">
                <img src="{$setting['site-shareimg']['value']}" class="figure-img img-fluid rounded" alt="image">
                <figcaption class="figure-caption text-center">{$setting['site-shareimg']['value']}</figcaption>
            </figure>
            <input type="hidden" name="delete_site-shareimg" value="{$setting['site-shareimg']['value']}"/>
        {/if}
    </div>
</div>
<div class="form-row form-group">
    <label for="v-site-tongji" class="col-3 col-md-2 text-right align-middle">统计代码</label>
    <div class="col-9 col-md-8 col-lg-6">
        <textarea name="v-site-tongji" class="form-control" placeholder="统计代码">{$setting['site-tongji']['value']}</textarea>
    </div>
</div>
<div class="form-row form-group">
    <label for="v-site-icp" class="col-3 col-md-2 text-right align-middle">ICP备案号</label>
    <div class="col-9 col-md-8 col-lg-6">
        <input type="text" class="form-control" name="v-site-icp" value="{$setting['site-icp']['value']}" placeholder="ICP备案号">
    </div>
</div>
<div class="form-row form-group">
    <label for="v-gongan-icp" class="col-3 col-md-2 text-right align-middle">公安备案号</label>
    <div class="col-9 col-md-8 col-lg-6">
        <input type="text" class="form-control" name="v-gongan-icp" value="{$setting['gongan-icp']['value']}" placeholder="公安备案号">
    </div>
</div>
<div class="form-row form-group">
    <label for="v-site-url" class="col-3 col-md-2 text-right align-middle">站点网址</label>
    <div class="col-9 col-md-8 col-lg-6">
        <input type="text" class="form-control" name="v-site-url" value="{$setting['site-url']['value']}" placeholder="站点地址">
    </div>
</div>
<div class="form-row form-group">
    <label for="v-site-name" class="col-3 col-md-2 text-right align-middle">公司名</label>
    <div class="col-9 col-md-8 col-lg-6">
        <input type="text" class="form-control" name="v-site-name" value="{$setting['site-name']['value']}" placeholder="公司名">
    </div>
</div>
<div class="form-row form-group">
    <label for="v-site-address" class="col-3 col-md-2 text-right align-middle">公司地址</label>
    <div class="col-9 col-md-8 col-lg-6">
        <input type="text" class="form-control" name="v-site-address" value="{$setting['site-address']['value']}" placeholder="公司地址">
    </div>
</div>
<div class="form-row form-group">
    <label for="v-site-location" class="col-3 col-md-2 text-right align-middle">公司位置</label>
    <div class="col-9 col-md-8 col-lg-6">
        <div class="input-group">
            <input type="text" class="form-control" name="v-site-location" value="{$setting['site-location']['value']}" placeholder="location">
            <div class="input-group-append">
                <a href="javascript:" class="btn btn-outline-secondary locationPick">选择位置</a>
            </div>
        </div>
    </div>
</div>