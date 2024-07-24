{extend name="public:base" /}

{block name="body"}
{include file="public/bread" menu="region_index" title="地区管理" /}
<div id="page-wrapper">
	<div class="row">
		<div class="col" style="width: 320px;flex-basis:320px;flex-grow: 0;">
			<div class="card side-cate">
				<div class="card-header">
					<a href="javascript:" class="btn float-right btn-outline-primary btn-sm addcate">添加</a>
					<a href="javascript:" class="btn float-right btn-outline-primary btn-sm btn-batch-add"><i
							class="ion-md-albums"></i> {:lang('Batch add')}</a>
					区域
				</div>
				<ul class="list-group list-group-flush" style="max-height: 80vh; overflow: auto;">
					<li class="list-group-item"><a class="list-cate-item"
							href="{:murl('index',['key'=>$keyword,'cate_id'=>0])}" data-value="0">不限区域</a></li>
					{foreach name="category" item="v"}
					<li class="list-group-item{$cate_id == $v['id']?' active':''}">
						<a href="javascript:" data-id="{$v['id']}" title="删除地区"
							class="float-right ml-2 text-danger delcate"><i class="ion-md-trash"></i></a>
						<a href="javascript:" data-id="{$v['id']}" title="编辑地区" data-title="{$v['title']}"
							data-short="{$v['short']}" data-title_en="{$v['title_en']}" data-code="{$v['code']}"
							data-name="{$v['name']}" data-sort="{$v['sort']}" data-pid="{$v['pid']}"
							class="float-right ml-2 addcate"><i class="ion-md-create"></i></a>
						<a href="javascript:" data-pid="{$v['id']}" title="添加地区" class="float-right addcate"><i
								class="ion-md-add"></i></a>
						<a class="list-cate-item" href="{:murl('index',['key'=>$keyword,'cate_id'=>$v['id']])}"
							data-value="{$v.id}">{$v.html} {$v.title}</a>
					</li>
					{/foreach}
				</ul>
			</div>
		</div>
		<div class="col">
			<table class="table table-hover table-striped">
				<thead>
					<tr>
						<th width="50">编号</th>
						<th>地区</th>
						<th>英文</th>
						<th>区号</th>
						<th>拼音</th>
						<th>地区</th>
						<th>排序</th>
						<th width="160">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					{empty name="lists"}{:list_empty(7)}{/empty}
					{volist name="lists" id="v" }
					<tr>
						<td><input type="checkbox" name="id" value="{$v.id}" /></td>
						<td>{$v.title}</td>
						<td>{$v.title_en}</td>
						<td>{$v.code}</td>
						<td>{$v.category_title}</td>
						<td>
							{$v.sort}
						</td>
						<td class="operations">
							<a class="btn btn-outline-primary addcate" href="javascript:" data-id="{$v['id']}"
								title="编辑地区" data-title="{$v['title']}" data-short="{$v['short']}"
								data-title_en="{$v['title_en']}" data-code="{$v['code']}" data-name="{$v['name']}"
								data-sort="{$v['sort']}" data-pid="{$v['pid']}"><i class="ion-md-create"></i> </a>
							<a class="btn btn-outline-danger link-confirm" title="{:lang('Delete')}"
								data-confirm="您真的确定要删除吗？\n删除后将不能恢复!"
								href="{:murl('region/delete',array('id'=>$v['id']))}"><i class="ion-md-trash"></i> </a>
						</td>
					</tr>
					{/volist}
				</tbody>
			</table>
			<div class="clearfix"></div>
			{$page|raw}
		</div>
	</div>



</div>
</block>
<block name="script">
	<script type="text/html" id="cate-template">
		<div class="form-vertical">
			<div class="form-group">
			<div class="input-group"><span class="input-group-prepend"><span class="input-group-text">区域名称</span></span><input class="form-control" name="title" placeholder="区域显示的名称"/></div>
			</div>
			<div class="form-group">
				<div class="input-group"><span class="input-group-prepend"><span class="input-group-text">英文名称</span></span><input class="form-control" name="title_en" placeholder="区域名称的英文"/></div>
			</div>
			<div class="form-group">
			<div class="input-group"><span class="input-group-prepend"><span class="input-group-text">区号</span></span><input class="form-control" name="code" placeholder="移动电话区号"/></div>
			</div>
			<div class="form-group">
			<div class="input-group"><span class="input-group-prepend"><span class="input-group-text">区域简称</span></span><input class="form-control" name="short" placeholder="区域名称的简写"/></div>
			</div>
			<div class="form-group">
			<div class="input-group"><span class="input-group-prepend"><span class="input-group-text">区域拼音</span></span><input class="form-control" name="name" placeholder="区域对应的唯一识别符号,只能为英文"/></div>
			</div>
			<div class="form-group">
				<div class="input-group"><span class="input-group-prepend"><span class="input-group-text">所属区域</span></span><select class="form-control" name="pid">
					<option value="0">顶级区域</option>
						{foreach $category as $v}
							<option value="{$v.id}" >{$v.html} {$v.title}</option>
						{/foreach}
					</select>
				</div>
			</div>
		</div>
	</script>
	<script type="text/html" id="cateselect">
        <div class="form-group">
            <select class="form-control">
                <option value="0">顶级分类</option>
                {volist name="regions" id="cate"}
                    <option value="{$cate.id}">{$cate.html|raw} {$cate.title}</option>
                {/volist}
            </select>
        </div>
        <div class="form-group text-muted">每行一个分类，每个分类以空格区分名称、简称、别名，简称、别名可依次省略，别名必须使用英文字母<br />例：分类名称 分类简称 catename</div>
    </script>
	<script type="text/javascript">

		jQuery(function ($) {
			$('.addcate').click(function () {
				var data = $(this).data();
				var dlg = new Dialog({
					backdrop: 'static',
					onshown: function (body) {
						bindData(body, data)
					},
					onsure: function (body) {
						var newData = getData(body)
						newData.id = data.id ? data.id : 0
						$.ajax({
							url: "{:murl('category')}",
							data: newData,
							type: 'POST',
							dataType: 'json',
							success: function (json) {
								if (json.code == 1) {
									dialog.alert(json.msg, function () {
										location.reload()
									})
								} else {
									dialog.error(json.msg);
								}
							}
						})
						return false;
					}
				}).show($('#cate-template').html(), data.id > 0 ? '编辑分类' : '添加分类');

			})

			$('.btn-batch-add').click(function (e) {
				var prmpt = dialog.prompt({
					title: '批量添加',
					content: $('#cateselect').html(),
					is_textarea: true
				}, function (args, body) {
					var pid = body.find('select').val();
					var loading = dialog.loading('正在提交...');
					$.ajax({
						url: "{:murl('batch')}",
						type: 'POST',
						dataType: 'json',
						data: {
							pid: pid,
							content: args
						},
						success: function (json) {
							loading.close();
							if (json.code == 1) {
								dialog.success(json.msg)
								prmpt.close()
								setTimeout(function () {
									location.reload()
								}, 1500);
							} else {
								dialog.error(json.msg)
							}

						}
					})
					return false;
				})
			})
		})
	</script>
	{/block}