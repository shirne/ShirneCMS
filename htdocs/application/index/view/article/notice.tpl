<extend name="public:base" />

<block name="body">
	<div class="main">
		<div class="container">
			<ol class="breadcrumb">
				<li class="breadcrumb-item icon"><a href="/">首页</a></li>
				<li class="breadcrumb-item"><a href="javascript:">系统公告</a></li>
			</ol>
		</div>

		<div class="container">
			<include file="side" />

			<div class="card float-right main_right news_list">
				<div class="card-body articlebody">
					<h1>{$article.title}</h1>
					<div class="info">
						发表时间:{$article.time|showdate}
					</div>
					<div class="container-fluid">
						{$article.content|raw}
					</div>
				</div>
			</div>
		</div>
	</div>
</block>