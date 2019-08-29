<extend name="public:base" />

<block name="body">
	<div class="main">
		<php>$adimg=getAdImage($page['group']);</php>
		<if condition="!empty($adimg)">
			<div class="subbanner">
				<div class="inner" style="background-image:url({$adimg})"></div>
			</div>
		</if>

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
