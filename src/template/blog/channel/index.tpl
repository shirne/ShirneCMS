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
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
{/block}