<extend name="public:base"/>

<block name="body">

    <extend:advs var="banners" flag="banner"/>
    <div id="carouselBannerControls" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <volist name="banners" id="item" key="k">
                <li data-target="#carouselBannerControls" {$k==1?'class="active"':''} data-slide-to="{$k-1}"></li>
            </volist>
        </ol>
        <div class="carousel-inner">
            <volist name="banners" id="item" key="k">
                <div class="carousel-item{$k==1?' active':''}" >
                    <img class="d-block h-100 m-auto" src="{$item.image}" alt="{$image.title}">
                    <p>{$image.title}</p>
                </div>
            </volist>
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

    <div class="container-fluid">
        <div class="index-card">
            <div class="container">
                <div class="index-card-title">
                    <h2>服务范围</h2>
                    <p>SERVICE SCOPE</p>
                </div>
                <div class="row index-card-body service-body">
                    <article:pages var="services" group="services"/>
                    <volist name="services" id="serv">
                        <figure class="col figure">
                            <img src="{$serv.icon}" class="figure-img img-fluid rounded" alt="{$serv.title}">
                            <figcaption class="figure-title">{$serv.title}</figcaption>
                            <figcaption class="figure-caption">{$serv.vice_title}</figcaption>
                        </figure>
                    </volist>
                </div>
            </div>
        </div>
        <div class="index-card">
            <div class="container">
                <div class="index-card-title">
                    <h2>解决方案</h2>
                    <p>INDUSTRY SOLUTIONS</p>
                </div>
                <div class="row index-card-body solution-body">
                    <article:pages var="solutions" group="solutions"/>
                    <div class="col-3">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <volist name="solutions" id="pg">
                        <a class="nav-link {$i<2?'active':''}" id="v-pills-{$pg.name}-tab" data-toggle="pill" href="#v-pills-{$pg.name}" role="tab" aria-controls="v-pills-{$pg.name}" aria-selected="true">
                            <p class="main_title">{$pg.title}解决方案</p>
                            <p class="vice_title">{$pg.vice_title}</p>
                        </a>
                        </volist>
                    </div>
                    </div>
                    <div class="col-9">
                    <div class="tab-content" id="v-pills-tabContent">
                        <volist name="solutions" id="pg">
                            <div class="tab-pane fade show {$i<2?'active':''}" id="v-pills-{$pg.name}" role="tabpanel" aria-labelledby="v-pills-{$pg.name}-tab">{$pg.title}</div>
                        </volist>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="index-card index-bg">
            <div class="container">
                <div class="index-card-title">
                    <h2>案例中心</h2>
                    <p>CASE CENTER</p>
                </div>
                <div class="index-card-body">
                    <article:list var="case_list" category="4"/>
                    <volist name="case_list" id="case">
                        <a href="{:url('article/view',['id'=>$art['id']])}">{$art.title}</a>
                    </volist>
                </div>
            </div>
        </div>
        <div class="index-card">
            <div class="container">
                <div class="index-card-title">
                    <h2>关于原设</h2>
                    <p>ABOUT ORIGIN SOFTWARE</p>
                </div>
                <div class="index-card-body">

                </div>
            </div>
        </div>
        <div class="index-card index-bg">
            <div class="container">
                <div class="index-card-title slide">
                    <div class="slide-box float-right">
                        <span class="toleft"><i class="ion-chevron-left"></i></span>
                        <span class="toright"><i class="ion-chevron-right"></i></span>
                    </div>
                    <h2>新闻动态</h2>
                    <p>NEWS AND TRENDS</p>
                </div>
                <div class="index-card-body">
                    <article:list var="article_list" category="5"/>
                    <volist name="article_list" id="art">
                        <a href="{:url('article/view',['id'=>$art['id']])}">{$art.title}</a>
                    </volist>
                </div>
            </div>
        </div>
    </div>
</block>
<block name="script">
    <script type="text/javascript">

    </script>
</block>
