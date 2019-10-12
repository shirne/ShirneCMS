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
                            <a
                                href="{:url('index/article/index',array('name'=>$category['name']))}">{$category.title}</a>
                            &nbsp;&nbsp;
                            <i class="ion-md-calendar"></i>&nbsp;{$article.create_time|showdate}
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