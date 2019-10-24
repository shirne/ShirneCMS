<extend name="public:base"/>

<block name="body">

    <extendtag:advs var="banners" flag="banner"/>
    <div id="carouselBannerControls" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <volist name="banners" id="item" key="k">
                <li data-target="#carouselBannerControls" {$k==1?'class="active"':''} data-slide-to="{$k-1}"></li>
            </volist>
        </ol>
        <div class="carousel-inner">
            <volist name="banners" id="item" key="k">
                <div class="carousel-item{$k==1?' active':''}" style="background-image:url({$item.image})">
                    <img src="{$item.image}" alt="{$image.title}">
                    <p>{$item.title}</p>
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
<div class="container index-body">
    <div class="row">
        <div class="col-lg-9">
            <ul class="list-group article-list">
                <article:list var="articles" order="create_time DESC" />
                <volist name="articles" id="art">
                <li class="list-group-item">
                    <if condition="!empty($art['cover'])">
                    <a class="list-img" href="{:url('index/article/view',['id'=>$art['id']])}" style="background-image:url({$art.cover})">
                        <img class="card-img-top" src="{$art.cover}" alt="Card image cap">
                    </a>
                    </if>
                    <div class="art-view">
                        <h3><a href="{:url('index/article/view',['id'=>$art['id']])}">{$art.title}</a></h3>
                        <div class="desc">
                            {$art.description}
                        </div>
                        <div class="text-muted">
                            <a href="{:url('index/article/index',['name'=>$art['category_name']])}"><span  class="badge badge-secondary">{$art.category_title}</span></a>
                            <span class="ml-2"><i class="ion-md-time"></i> {$art.create_time|showdate}</span>
                            <span class="ml-2"><i class="ion-md-paper-plane"></i> {$art.views}</span>
                            <span class="ml-2" data-anchor="comment"><i class="ion-md-text"></i> {$art.comment}</span>
                        </div>
                    </div>
                </li>
                </volist>
            </ul>
        </div>
        <div class="col-lg-3 sidecolumn">
            <div class="card">
                <div class="card-header">支持一下</div>
                <div class="card-body text-center">
                    <img style="max-width: 100%" src="__STATIC__/images/qrcode.png"/>
                </div>
            </div>
            <div class="card">
                <div class="card-header">推荐阅读</div>
                <div class="card-body">
                    <article:list var="articles" order="views DESC" />
                    <div class="list-side">
                    <volist name="articles" id="art">
                        <a href="{:url('index/article/view',['id'=>$art['id']])}">{$art.title}</a>
                    </volist>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</block>
<block name="script">
    <script type="text/javascript">

    </script>
</block>
