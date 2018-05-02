<extend name="Public:Base" />

<block name="body">
	<div class="main">
		<div class="container">
			<ol class="breadcrumb">
				<li class="icon"><a href="/">首页</a></li>
				<li><a href="javascript:">关于我们</a></li>
			</ol>
		</div>

		<div class="container">
			<div class="card float-left main_left">
				<div class="card-header"><h3>关于我们</h3></div>
				<div class="card-body">

					<div id="news" class="list">
						<Volist name="lists" id="p">
							<div class="panel">
								<a class="Level_1" href="{:url('Page/index',array('name'=>$p['name']))}"> {$p.title} </a>
							</div>
						</Volist>
					</div>


				</div>
			</div>

			<div class="card pull-right main_right news_list">
				<div class="card-body postbody">
					<h1>{$page.title}</h1>
					<div class="info">
					</div>
					<div class="container-fluid">
						{$page.content|raw}
					</div>
				</div>
			</div>
		</div>
	</div>
</block>
