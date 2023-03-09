{extend name="public:base" /}
{block name="header"}
    <link href="__STATIC__/ueditor/third-party/SyntaxHighlighter/shCoreDefault.css" rel="stylesheet">
{/block}
{block name="body"}
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
                        {if !empty($images)}
                            <div class="article-slides">
                                <div id="carouselExampleCaptions" class="carousel slide" data-ride="carousel">
                                    <ol class="carousel-indicators">
                                        {volist name="images" id="img"}
                                        <li data-target="#carouselExampleCaptions" data-slide-to="{$i-1}">
                                        </li>
                                        {/volist}
                                    </ol>
                                    <div class="carousel-inner">
                                        {volist name="images" id="img"}
                                            <div class="carousel-item" style="background-image:url({$img.image})">
                                                <img src="{$img.image}" alt="{$img.title}">
                                                <div class="carousel-caption d-none d-md-block">
                                                    <h5>{$img.title}</h5>
                                                    <p>{$img.title}</p>
                                                </div>
                                            </div>
                                        {/volist}
                                    </div>
                                </div>
                            </div>
                        {/if}
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
                            {if $article['close_comment']}
                                <div class="empty">评论已关闭</div>
                            {else/}
                                <form class="comment-form" name="commentForm" action="{:url('index/article/comment',['id'=>$article['id']])}" method="POST">
                                <div class="media comment-form mt-2">
                                    <img src="{$member.avatar|default='/static/images/avatar-default.png'}" class="avatar mr-3 rounded" alt="{$member.nickname}">
                                    <div class="media-body">
                                        <input type="hidden" name="reply_id" value="0">
                                        {if $isLogin}
                                            <textarea name="content" placeholder="说点什么..." class="form-control"></textarea>
                                            <div class="d-flex mt-2">
                                                <div class="flex-fill text-muted">支持除链接外的Markdown语法</div>
                                                <input type="submit" class="btn btn-info" value="提交评论">
                                            </div>
                                        {elseif condition="$config['anonymous_comment']" /}
                                            <textarea name="content" placeholder="说点什么..." class="form-control"></textarea>
                                            <div class="d-flex mt-2">
                                                <div class="w-25"><input type="text" name="email" class="form-control float-left" placeholder="填写邮箱"/> </div>
                                                <div class="flex-fill text-muted pl-2" style="line-height:38px;"> 或<a class="pl-1 pr-1 text-info" href="{:url('index/login/index')}">登录</a>后评论</div>
                                                <input type="submit" class="btn btn-info" value="提交评论">
                                            </div>
                                        {else/}
                                            <textarea name="content" readonly class="form-control"></textarea>
                                            <div class="d-flex mt-2">
                                                <div class="flex-fill text-muted">请<a class="pl-1 pr-1 text-info" href="{:url('index/login/index')}">登录</a>后评论</div>
                                                <input type="submit" class="btn btn-info" disabled value="提交评论">
                                            </div>
                                        {/if}
                                    </div>
                                </form>
                                </div>
                                
                                <div class="comment_list mt-3">

                                </div>
                                <div class="comment_action"></div>
                            {/if}
                        </div>
                    </div>
                </div>
                {include file="article:_left" /}
            </div>
        </div>
    </div>
{/block}
{block name="script"}
    <script type="text/html" id="comment_tpl">
        <div class="media mt-2">
            <img src="{@avatar|default=/static/images/avatar-default.png}" class="avatar mr-3 rounded" alt="{@nickname}">
            <div class="media-body">
                <h6 class="mt-0">{@nickname}{if @status == 0}<span class="badge badge-warning">审核中</span>{/if}{if @status < 0}<span class="badge badge-danger">已隐藏</span>{/if}</h6>
                <div class="comment-content mb-1">{@content|markdown2html}</div>
                <div class="comment-info mb-1 text-muted">于 {@create_time|timestamp_date}</div>
            </div>
        </div>
    </script>
    <script type="text/javascript" src="__STATIC__/ueditor/third-party/SyntaxHighlighter/shCore.js"></script>
    <script type="text/javascript" src="__STATIC__/js/marked.min.js"></script>
    <script type="text/javascript">
        function markdown2html(text){
            return marked(html_decode(text));
        }
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
                $('.comment_action').html('<div class="text-muted text-center mt-2"><div class="spinner-border spinner-border-sm" role="status"><span class="sr-only">Loading...</span></div>&nbsp;加载中...</div>');
                
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
                            }else{
                                if(page==1){
                                    $('.comment_action').html('<div class="empty">暂无评论</div>');
                                    return;
                                }
                            }
                            if(json.data.page >= json.data.total_page){
                                $('.comment_action').html('<div class="text-muted text-center mt-2">没有更多评论了</div>');
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

            $('.comment-form').submit(function(e){
                e.preventDefault();
                var content=$(this).find('[name=content]').val();
                if(!content){
                    dialog.error('您还没填写评论内容呢');
                    return false;
                }
                if("{$isLogin?1:''}"==''){
                    var email=$(this).find('[name=email]').val();
                    if(!content){
                        dialog.error('请登录或填写邮箱再提交评论');
                        return false;
                    }
                }
                var submit=$(this).find('[type=submit]')
                submit.prop('disabled',true);
                $.ajax({
                    url:$(this).attr('action'),
                    type:'POST',
                    dataType:'json',
                    data:$(this).serialize(),
                    success:function(json){
                        submit.prop('disabled',false);
                        if(json.code==1){
                            $('.comment-form')[0].reset();
                            dialog.success(json.msg);
                        }else{
                            dialog.error(json.msg);
                        }
                    },
                    error:function(){
                        submit.prop('disabled',false);
                        dialog.error('提交失败了,请稍候再试');
                    }
                })
            });
        })
        
    </script>
{/block}