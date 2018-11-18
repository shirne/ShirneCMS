<extend name="public:base" />

<block name="body">
	<div class="main">
		<div class="subbanner">
			<div class="inner" style="background-image:url({:getAdImage($page['group'])})"></div>
		</div>

		<div class="container view-body">
			<div class="row">
				<div class="col-md-9">
					<div class="article-body">
						<div class="page-content">
							{$page.content|raw}
						</div>
					</div>
				</div>
				<div class="col-md-3 sidecolumn">
					<div class="card">
						<div class="card-header">
                            {$group.group_name}
						</div>
						<div class="card-body">
							<div class="list-group">
								<Volist name="lists" id="p">
									<a class="list-group-item {$p['name']==$page['name']?'active':''}" href="{:url('index/page/index',['group'=>$p['group'],'name'=>$p['name']])}">{$p.title}</a>
								</Volist>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</block>
