<extend name="Public:Base" />

<block name="body">
	<div class="main">
		<div class="container">
			<ol class="breadcrumb">
				<li class="breadcrumb-item icon"><a href="/">首页</a></li>
				<li class="breadcrumb-item"><a href="{:url('Post/index')}">资讯中心</a></li>
			</ol>
		</div>

		<div class="container">
			<include file="post/side" />

			<div class="card float-right main_right news_list">
				<div class="card-body postbody">
					<h1>{$post.title}</h1>
					<div class="info">
						分类:<a href="{:url('Post/index',array('name'=>$category['name']))}">{$category.title}</a>
						&nbsp;&nbsp;
						发表时间:{$post.create_time|showdate}
					</div>
					<div class="container-fluid">
						{$post.content|raw}
					</div>
				</div>
			</div>
		</div>
	</div>
</block>