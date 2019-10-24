<extend name="public:base" />
<block name="header">
    <link href="__STATIC__/ueditor/third-party/SyntaxHighlighter/shCoreDefault.css" rel="stylesheet">
</block>
<block name="body">
    <div class="main">
        <div class="container view-body">
            <div class="row">
                <div class="col-lg-9">
                    <div class="article-body">
                        <h1 class="article-title">{$article.title}</h1>
                        <div class="article-info text-muted text-center">
                            <a href="{:url('index/article/index',array('name'=>$category['name']))}"><i class="ion-md-pricetag"></i> {$category.title}</a>
                            <span class="ml-2"><i class="ion-md-calendar"></i> {$article.create_time|showdate}</span>
                            <span class="ml-2"><i class="ion-md-paper-plane"></i> {$article.views}</span>
                            <span class="ml-2"><i class="ion-md-text"></i> {$article.comment}</span>
                        </div>
                        <if condition="!empty($images)">
                            <div class="article-slides">
                                <div id="carouselExampleCaptions" class="carousel slide" data-ride="carousel">
                                    <ol class="carousel-indicators">
                                        <volist name="images" id="img">
                                        <li data-target="#carouselExampleCaptions" data-slide-to="{$i-1}">
                                        </li>
                                        </volist>
                                    </ol>
                                    <div class="carousel-inner">
                                        <volist name="images" id="img">
                                            <div class="carousel-item" style="background-image:url({$img.image})">
                                                <img src="{$img.image}" alt="{$img.title}">
                                                <div class="carousel-caption d-none d-md-block">
                                                    <h5>{$img.title}</h5>
                                                    <p>{$img.title}</p>
                                                </div>
                                            </div>
                                        </volist>
                                    </div>
                                </div>
                            </div>
                        </if>
                        <div class="article-content">
                            <div>
                                {$article.content|raw}
                            </div>
                        </div>
                    </div>

                    <div class="card card-comment mt-3">
                        <div class="card-header">
                            评论
                        </div>
                        <div class="card-body">
                            <if condition="$article['close_comment']">
                                <div class="empty">评论已关闭</div>
                            <else/>
                                <if condition="empty($comments)">
                                    <div class="empty">暂无评论</div>
                                <else/>
                                    <volist name="comments" id="cmt">
                                        <div class="media">
                                            <img src="..." class="mr-3" alt="...">
                                            <div class="media-body">
                                                <h5 class="mt-0">Media heading</h5>
                                                Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi vulputate fringilla. Donec lacinia congue felis in faucibus.
                                            </div>
                                        </div>
                                    </volist>
                                </if>
                            </if>
                        </div>
                    </div>
                </div>
                <include file="article:_left" />
            </div>
        </div>
    </div>
</block>
<block name="script">
    <script type="text/javascript" src="__STATIC__/ueditor/third-party/SyntaxHighlighter/shCore.js"></script>
    <script type="text/javascript">
        SyntaxHighlighter.highlight();
        $('.carousel-indicators').eq(0).addClass('active')
        $('.carousel-item').eq(0).addClass('active')
        $('.carousel').carousel()
    </script>
</block>