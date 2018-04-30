<extend name="Public:Base" />

<block name="body">
	<div class="main">
		<div class="container">
			<ol class="breadcrumb">
				<li class="icon"><a href="/">首页</a></li>
				<li><a href="javascript:">资讯中心</a></li>
			</ol>
		</div>

		<div class="container">
			<include file="side" />

			<div class="panel pull-right main_right news_list">
				<div class="panel-body">
					<ul>
						<php>$empty='<li class="empty">暂时没有内容</li>'</php>
						<Volist name="lists" id="post" empty="$empty">
						<li>
							<h2><a href="{:url('Post/view',array('id'=>$post['id']))}">{$post.title}</a></h2>
							<div>{$post.content|cutstr=250}</div>
						</li>
						</Volist>

					</ul>
					{$page}
				</div>
			</div>
		</div>
	</div>
</block>