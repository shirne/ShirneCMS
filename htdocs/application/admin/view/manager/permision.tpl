<extend name="public:base" />
<block name="header">
	<style type="text/css">
	label{font-weight:normal;}
	div.col-sm-2{font-weight:bold;white-space: nowrap;}
	.col-sm-10 label{width:120px;overflow: hidden;white-space:nowrap;text-overflow: ellipsis;margin-bottom:0;}
	
	</style>
</block>
<block name="body">

<include file="public/bread" menu="manager_index" title="管理员权限" />

<div id="page-wrapper">
	<div class="page-header">管理员权限</div>
	<div id="page-content">
<form action="{:url('manager/permision',array('id'=>$model['manager_id']))}" class="form-horizontal" method="post">
	<div class="card">
		<div class="card-header">全局权限</div>
		<div class="card-body">
			<label><input type="checkbox" name="global[]" value="edit" <if condition="in_array('edit',$model['global'])">checked</if> />&nbsp;编辑</label>
			<label><input type="checkbox" name="global[]" value="del" <if condition="in_array('del',$model['global'])">checked</if> />&nbsp;删除</label>
		</div>
	</div>
	<div class="card mt-4 mb-4">
		<div class="card-header">详细权限&nbsp;&nbsp;<label><input type="checkbox" onclick="checkall(this)" />&nbsp;全选</label></div>

		<ul class="list-group list-group-flush">
		<foreach name="perms" item="perm" key="key">
			<li class="list-group-item">
				<div class="row">
				<label class="col-2"><input type="checkbox" onclick="checkline(this)" />&nbsp;{$perm.title}</label>
				<div class="col-10">
					<foreach name="perm.items" item="item" key="k">
						<label title="{$item}"><input type="checkbox" name="detail[]" value="{$key}_{$k}" <if condition="in_array($key.'_'.$k,$model['detail'])">checked</if> />&nbsp;{$item}</label>
					</foreach>
				</div>
				</div>
			</li>
		</foreach>
		</ul>
	</div>

	<div class="form-group">
		<div class="col-sm-10 col-sm-offset-2">
		<button class="btn btn-primary" type="submit" >保存</button>
		</div>
	</div>


</form>
		</div>
</div>
<script type="text/javascript">
function checkall(src){
	var checked=$(src).is(':checked');
	$('[name^=global]').prop('checked',checked);
	$('[name^=detail]').prop('checked',checked);
	$('[onclick^=checkline]').prop('checked',checked);
}
function checkline(src){
	var checked=$(src).is(':checked');
	$(src).parents('li').find('[name^=detail]').prop('checked',checked);
}
$('input[name^=detail]').click(function(){
	var row=$(this).parents('div.form-group');
	var p=row.find('div.col-sm-10');
	if(p.find(':checked').length==p.find('input').length){
		row.find('label.col-2 input').prop('checked',true);
	}else{
		row.find('label.col-2 input').prop('checked',false);
	}
});
jQuery(function(){
	$('.detail-line').each(function(){
		var row=$(this);
		var p=row.find('div.col-sm-10');
		if(p.find(':checked').length==p.find('input').length){
			row.find('label.col-2 input').prop('checked',true);
		}else{
			row.find('label.col-2 input').prop('checked',false);
		}
	})
});
</script>

</block>
