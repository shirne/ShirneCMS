<div class="footer bg-dark text-light">
    <div class="container">
        <div class="row">
            <volist name="navigator" id="nav">
                <if condition="$nav['footer']">
            <div class="col">
                <dl>
                    <dt>{$nav['title']}</dt>
                    <dd>
                        <volist name="nav['subnav']" id="nav">
                        <a href="{$nav['url']}">{$nav['title']}</a>
                        </volist>
                    </dd>
                </dl>
            </div>
                </if>
            </volist>
            <div class="col">
                <dl>
                    <dt>友情链接</dt>
                    <dd>
                        <extend:links var="links" />
                        <volist name="links" id="link">
                            <a href="{$link.url}">{$link.title}</a>
                        </volist>
                    </dd>
                </dl>
            </div>
            <div class="col qrcode">
                <img src="__STATIC__/images/qrcode.png"/>
            </div>
            <div class="col telephone">
                <h2><span class="ion-ios-telephone-outline"></span>&nbsp;076088618161</h2>
                <p><span class="ion-location"></span>&nbsp;中山市西区</p>
                <p><span class="ion-social-tux"></span>&nbsp;631380009</p>
            </div>
        </div>
        <hr class="my-4"/>
        <div class="row copyright-row">
            <div class="col-md-8">
                &nbsp;
            </div>
            <div class="col-md-4 text-right">
                &copy;CopyRight 2014-2018 原设软件
            </div>
        </div>
    </div>
</div>