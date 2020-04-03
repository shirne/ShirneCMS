{extend name="public:base" /}

{block name="body"}
	<div class="main">
		<div class="subbanner">
			<div class="inner" style="background-image:url({:getAdImage($page['group'])})"></div>
		</div>

		<div class="nav-row">
			<div class="container">
				<div class="row">
					{volist name="lists" id="p"}
						<a class="col row-item {$p['name']==$page['name']?'active':''}" href="{:url('index/page/index',['group'=>$p['group'],'name'=>$p['name']])}">{$p.title}</a>
					{/volist}
				</div>
			</div>
		</div>

		<div class="container">
			<h1 class="page-title">{$page.title}</h1>
			<div class="page-content">
                {$page.content|raw}
			</div>
		</div>
	</div>
{/block}
