<extend name="public:base" />

<block name="body">
	<div class="main">
		<div class="container">
			<ol class="breadcrumb">
				<li class="breadcrumb-item icon"><a href="/">首页</a></li>
				<li class="breadcrumb-item active"><a href="javascript:">资讯中心</a></li>
			</ol>
		</div>

		<div class="container">
			<include file="article/side" />

			<div class="card float-right main_right news_list">
				<div class="card-body">
					<ul>
						<php>$empty='<li class="empty">暂时没有内容</li>';</php>
						<Volist name="lists" id="article" empty="$empty">
						<li>
							<h2><a href="{:url('Article/view',array('id'=>$article['id']))}">{$article.title}</a></h2>
							<div>{$article.content|cutstr=250}</div>
						</li>
						</Volist>

					</ul>
					{$page}
				</div>
			</div>
		</div>
	</div>
</block>