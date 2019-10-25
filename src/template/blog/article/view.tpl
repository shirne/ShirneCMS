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
                                <div class="comment_list">

                                </div>
                                <div class="comment_action"></div>
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
    <script type="text/html" id="comment_tpl">
        <div class="media mt-2">
            <img src="{@avatar|default=/static/images/avatar-default.png}" class="avatar mr-3 rounded" alt="{@nickname}">
            <div class="media-body">
                <h6 class="mt-0">{@nickname}</h6>
                {@content|html_encode}
                <div class="text-muted">于 {@create_time|timestamp_date}</div>
            </div>
        </div>
    </script>
    <script type="text/javascript" src="__STATIC__/ueditor/third-party/SyntaxHighlighter/shCore.js"></script>
    <script type="text/javascript">
        jQuery(function($){
            SyntaxHighlighter.highlight();
            $('.carousel-indicators').eq(0).addClass('active')
            $('.carousel-item').eq(0).addClass('active')
            $('.carousel').carousel()

            var page=1;
            var commentTpl=$('#comment_tpl').text();
            var isloading=false;
            $('.comment_action').on('click','.linkmore',function(e){
                page++;
                loadPage();
            })
            $('.comment_action').on('click','.linkagain',function(e){
                loadPage();
            })
            function loadPage(){
                if(isloading)return;
                isloading=true;
                $('.comment_action').html('<div class="text-muted text-center"><div class="spinner-border spinner-border-sm" role="status"><span class="sr-only">Loading...</span></div>&nbsp;加载中...</div>');
                
                $.ajax({
                    url:"{:url('index/article/comment',['id'=>$article['id']])}",
                    dataType:'json',
                    type:'get',
                    data:{
                        page:page
                    },
                    success:function(json){
                        isloading=false;
                        if(json.code==1){
                            if(json.data.comments && json.data.comments.length>0){
                                $('.comment_list').append(commentTpl.compile(json.data.comments,true))
                            }
                            if(json.data.page >= json.data.total_page){
                                if(page==1){
                                    $('.comment_action').html('<div class="empty">暂无评论</div>');
                                }else{
                                    $('.comment_action').html('<div class="text-muted text-center mt-2">没有更多评论了</div>');
                                }
                            }else{
                                $('.comment_action').html('<div class="text-muted text-center mt-2"><a href="javascript:" class="linkmore">点击加载更多</a></div>');
                            }
                        }else{

                        }
                    },
                    error:function(){
                        isloading=false;
                        $('.comment_action').html('<div class="text-muted text-center mt-2"><a href="javascript:" class="linkagain">加载出错，点击重试</a></div>');
                    }
                })
            }
            loadPage();
        })
        
    </script>
</block>