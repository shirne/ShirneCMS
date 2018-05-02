<extend name="public:base"/>

<block name="body">
    <div class="container">
        <extend:advs var="banners" flag="banner"/>
        <div id="carouselBannerControls" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                <volist name="banners" id="item" key="k">
                    <li data-target="#carouselBannerControls" {$k==1?'class="active"':''} data-slide-to="{$k-1}"></li>
                </volist>
            </ol>
            <div class="carousel-inner">
                <volist name="banners" id="item" key="k">
                    <div class="carousel-item{$k==1?' active':''}" {$k}>
                        <img class="d-block w-100" src="{$item.image}" alt="{$image.title}">
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
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">系统说明</h5>
                <article:list var="article_list" />
                <volist name="article_list" id="art">
                    <a href="{:url('post/view',['id'=>$art['id']])}">{$art.title}</a>
                </volist>
                <article:cates var="cates" />
                <volist name="cates" id="cate">
                    <a href="{:url('post/index',['name'=>$cate['name']])}">{$cate.title}</a>
                </volist>
            </div>
        </div>
    </div>
</block>
<block name="script">
    <script type="text/javascript">

    </script>
</block>
