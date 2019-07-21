<div class="footer">
    <div class="container">
        <hr class="my-4"/>
        <div class="copyright-row text-center">
            <div class="mt-3">
                &copy;2014-2018 原设软件&nbsp;<a href="http://www.beian.miit.gov.cn/" target="_blank">{$config['site-icp']}</a>{$config['site-tongji']|raw}
            </div>
            <if condition="!empty($config['gongan-icp'])">
                <php>$icpcode=preg_replace('/[^\d]+/','',$config['gongan-icp'])</php>
                <div class="mt-3"> <a href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode={$icpcode}" target="_blank"> <img src="__STATIC__/images/beianicon.png" style="vertical-align: middle;" /> {$config['gongan-icp']}</a></div>
            </if>
        </div>
    </div>
</div>