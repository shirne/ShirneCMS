<extend name="public:base" />

<block name="body">
	<div class="main">
		<div class="container">
			<ol class="breadcrumb">
				<li class="breadcrumb-item icon"><a href="/">首页</a></li>
				<notempty name="group" >
					<li class="breadcrumb-item"><a href="{:url('index/page/index',['group'=>$group['group']])}">{$group['group_name']}</a></li>
				</notempty>
				<li class="breadcrumb-item active"><a href="javascript:">{$page.title}</a></li>
			</ol>
		</div>

		<div class="container">
			<div class="row">
				<div class="col-md-3">
					<div class="card main_left">
						<div class="card-header">
							<empty name="group" >
								<h3>关于我们</h3>
								<else/>
								<h3>{$group['group_name']}</h3>
							</empty>
						</div>
						<div class="card-body">
							<div id="news" class="list-group">
								<Volist name="lists" id="p">
									<a class="list-group-item Level_1" href="{:url('index/page/index',['group'=>$p['group'],'name'=>$p['name']])}">{$p.title}</a>
								</Volist>
							</div>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card main_right news_list">
						<div class="card-body articlebody">
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
		</div>
	</div>
</block>
