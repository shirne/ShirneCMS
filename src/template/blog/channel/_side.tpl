<div class="col sidecolumn">
    <div class="card side-block wow slideInLeft" data-wow-delay="0.5s" data-wow-duration="0.8s">
        <div class="card-header">
            <span class="float-left">{$channel['title']}</span>
        </div>
        <div class="card-body">
            <div class="list-side">
            {volist name="categories[$channel['id']]" id="p"}
                <a class="col{$p['id']==$category['id']?' active':''}" href="{:url('index/channel/list',['channel_name'=>$channel['name'],'cate_name'=>$p['name'],])}"><i class="ion-md-arrow-dropright"></i>&nbsp;{$p.title}</a>
            {/volist}
            </div>
        </div>
    </div>
    <div class="card side-block mt-3 wow slideInLeft" data-wow-delay="0.5s" data-wow-duration="0.8s">
        <div class="card-header">
            <span class="float-left">联系我们</span>
        </div>
        <div class="card-body">
            {$config['site-name']}<br />
            咨询热线：{$config['site-400']}<br />
            电话：{$config['site-telephone']}<br />
            地址：{$config['site-address']}<br />
            网址：{$config['site-url']}<br />
            邮箱：{$config['site-email']}
        </div>
    </div>
</div>