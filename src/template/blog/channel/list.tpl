{extend name="public:base"/}

{block name="body"}
    <div class="main">
        {include file="channel:_banner" /}
        <div class="breadcrumb-box wow slideInUp" data-wow-duration="0.8s">
			<div class="container">
				<nav aria-label="breadcrumb" >
					<ol class="breadcrumb">
						<li class="breadcrumb-item" ><i class="ion-md-pin" name="breadcrumb"></i> <a href="/">首页</a></li>
                        {volist name="categoryTree" id="c"}
						<li class="breadcrumb-item" >
                            {if $c['id'] eq $channel['id']}
                                <a href="{:url('index/channel/index',['channel_name'=>$c['name']])}">{$c['title']}</a>
                                {else/}
                                <a href="{:url('index/channel/list',['channel_name'=>$channel['name'],'cate_name'=>$c['name']])}">{$c['title']}</a>
                            {/if}
                        </li>
                        {/volist}
					</ol>
				  </nav>
			</div>
		</div>

        <div class="container">
			<div class="row">
				{include file="channel:_side" /}
				<div class="col wow slideInRight"  data-wow-delay="0.5s" data-wow-duration="0.8s">

                    <div class="subpage-title">
                        <span class="title-cn">{$channel['title']}</span>
                        <span class="title-en">{$channel['vice_title']}</span>
                    </div>
                    <div class="view-body " >
                        <div class="article-list">
                            <php>$empty='<div class="empty-box"><p class="empty">暂时没有内容</p></div>';</php>
                            {Volist name="lists" id="art" empty="$empty"}
                                <div class="card mb-3" >
                                    <div class="row no-gutters">
                                        {if !empty($art['cover'])}
                                      <a class="col-4 text-center" style="background:url('{$art['cover']}') center center no-repeat;background-size:cover;" href="{:url('index/channel/view',['channel_name'=>$channel['name'],'cate_name'=>$art['category_name'],'article_name'=>$art['name']])}">
                                        <img class="img-fluid invisible" src="{$art['cover']}" alt="{$art.title}">
                                      </a>
                                    {/if}
                                      <div class="col">
                                        <div class="card-body">
                                          <h5 class="card-title"><a href="{:url('index/channel/view',['channel_name'=>$channel['name'],'cate_name'=>$art['category_name'],'article_name'=>$art['name']])}">{$art.title}</a></h5>
                                          <p class="card-text">{$art.description}</p>
                                          <p class="card-text"><i class="ion-md-time"></i> {$art.create_time|showdate='Y-m-d'}&nbsp;&nbsp;<i class="ion-md-paper-plane"></i> {$art.views}</p>
                                        </div>
                                      </div>
                                    </div>
                                </div>
                            {/Volist}
                        </div>
                        {$page|raw}
                    </div>
                </div>
            </div>
        </div>
    </div>
{/block}