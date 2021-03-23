{extend name="public:base"/}

{block name="body"}

    {extendtag:advs var="banners" flag="banner" /}
    <div id="carouselBannerControls" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            {volist name="banners" id="item" key="k"}
                <li data-target="#carouselBannerControls" {$k==1?'class="active"':''} data-slide-to="{$k-1}"></li>
            {/volist}
        </ol>
        <div class="carousel-inner">
            {volist name="banners" id="item" key="k"}
                <div class="carousel-item{$k==1?' active':''}" style="background-image:url({$item.image})">
                    <img src="{$item.image}" alt="{$item.title}">
                    <p>{$item.title}</p>
                </div>
            {/volist}
        </div>
        <a class="carousel-control-prev" href="#carouselBannerControls" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselBannerControls" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <div class="index-card index-bg">
        <div class="container">
            <div class="index-card-title">
                <h2>服务范围</h2>
                <p>SERVICE SCOPE</p>
            </div>
            <div class="row index-card-body service-body">
                {article:pages var="services" group="services" /}
                {volist name="services" id="serv"}
                    <a href="{:url('index/page/index',['group'=>'services','name'=>$serv['name']])}">
                        <figure class="col figure">
                            <img src="{$serv.icon}" class="figure-img img-fluid rounded" alt="{$serv.title}">
                            <figcaption class="figure-title">{$serv.title}</figcaption>
                            <figcaption class="figure-caption">{$serv.vice_title}</figcaption>
                        </figure>
                    </a>
                {/volist}
            </div>
        </div>
    </div>
    <div class="index-card index-bg">
        <div class="container">
            <div class="index-card-title">
                <h2>解决方案</h2>
                <p>INDUSTRY SOLUTIONS</p>
            </div>
            <div class="row index-card-body solution-body">
                {article:pages var="solutions" group="solutions" recursive="true" /}
                <div class="col-3">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                         aria-orientation="vertical">
                        {volist name="solutions" id="pg"}
                            <a class="nav-link {$i<2?'active':''}" id="v-pills-{$pg.name}-tab" data-toggle="pill"
                               href="#v-pills-{$pg.name}" role="tab" aria-controls="v-pills-{$pg.name}"
                               aria-selected="true">
                                <p class="main_title">{$pg.title}解决方案</p>
                                <p class="vice_title">{$pg.vice_title}</p>
                            </a>
                        {/volist}
                    </div>
                </div>
                <div class="col-9">
                    <div class="tab-content" id="v-pills-tabContent">
                        {volist name="solutions" id="pg"}
                            <div class="tab-pane fade show {$i<2?'active':''}" id="v-pills-{$pg.name}"
                                 role="tabpanel" aria-labelledby="v-pills-{$pg.name}-tab"><img class="card-img-top"
                                                                                               src="{$pg.icon}"
                                                                                               alt="{$pg.title}">
                            </div>
                        {/volist}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="index-card">
        <div class="container">
            <div class="index-card-title">
                <h2>案例中心</h2>
                <p>CASE CENTER</p>
            </div>
            <div class="index-card-body cases-body">
                <div class="row">
                    {article:list var="case_list" category="4" limit="9" recursive="true" /}
                    {volist name="case_list" id="case"}
                        <div class="col-4">
                            <div class="card">
                                <img class="card-img-top" src="{$case.cover}" alt="Card image cap">
                                <div class="card-body">
                                    <h3 class="card-text">{$case.title}</h3>
                                    <p class="card-text text-muted">
                                        <span class="float-right"><i class="ion-md-ion-monitor"></i> <i
                                                    class="ion-md-iphone"></i> </span>
                                        <span>{$case.vice_title}</span>
                                    </p>
                                </div>
                                <a target="_blank" href="{:url('index/article/view',['id'=>$case['id']])}">
                                    <div class="mask"></div>
                                </a>
                            </div>
                        </div>
                    {/volist}
                </div>
                {if count($case_list) EQ 9}
                    <div class="row">
                        <div class="col-1 align-self-center"><a href="{:url('index/article/index',['name'=>'cases'])}" class="btn btn-outline-secondary btn-block">MORE</a></div>
                    </div>
                {/if}
            </div>
        </div>
    </div>
    <div class="index-card index-bg">
        <div class="container">
            <div class="index-card-title">
                <h2>关于原设</h2>
                <p>ABOUT ORIGIN SOFTWARE</p>
            </div>
            <div class="index-card-body about-body">
                {article:page var="about" name="about" /}
                <div class="row">
                    <div class="col-5">
                        <figure class="figure">
                            <img src="{$about.icon}" class="figure-img img-fluid rounded" />
                        </figure>
                    </div>
                    <div class="col-7">
                        <h4>WHO WE ARE ?</h4>
                        <div class="text-muted">{$about.content|raw}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="index-card">
        <div class="container">
            <div class="index-card-title slide">
                <div class="slide-box float-right">
                    <a href="#carouselNewsIndicators" class="toleft" role="button" data-slide="prev"><i class="ion-md-arrow-dropleft"></i></a>
                    <a href="#carouselNewsIndicators" class="toright" role="button" data-slide="next"><i class="ion-md-arrow-dropright"></i></a>
                </div>
                <h2>新闻动态</h2>
                <p>NEWS AND TRENDS</p>
            </div>
            <div class="index-card-body news-body">
                {article:list var="article_list" category="5" limit="9" recursive="true" /}
                <div id="carouselNewsIndicators" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner">
                        <article:listwrap name="article_list" step="3" id="art_list">
                            <div class="carousel-item {$wrapi==0?'active':''}">
                                <div class="row">
                                    {volist name="art_list" id="art" }
                                        <a class="col-4" target="_blank" href="{:url('index/article/view',['id'=>$art['id']])}">
                                            <div class="media">
                                                <img class="align-self-end mr-3" src="{$art.cover}" alt="{$art.title}">
                                                <div class="media-body">
                                                    <div>
                                                        <span class="badge">{$art.category_title}</span>
                                                        <text class="text-muted">{$art.create_time|showdate='Y-m-d'}</text>
                                                    </div>
                                                    <h4>{$art.title}</h4>
                                                </div>
                                            </div>
                                        </a>
                                    {/volist}
                                </div>
                            </div>
                        </article:listwrap>
                    </div>
                </div>

            </div>
        </div>
    </div>
{/block}
{block name="script"}
    <script type="text/javascript">

    </script>
{/block}
