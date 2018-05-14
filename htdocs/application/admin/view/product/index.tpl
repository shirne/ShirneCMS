<extend name="public:base" />

<block name="body">
<include file="public/bread" menu="product_index" title="商品列表" />
<div id="page-wrapper">

	<div class="row list-header">
		<div class="col-md-6">
			<div class="btn-toolbar list-toolbar" role="toolbar" aria-label="Toolbar with button groups">
				<div class="btn-group btn-group-sm mr-2" role="group" aria-label="check action group">
					<a href="javascript:" class="btn btn-outline-secondary checkall-btn" data-toggle="button" aria-pressed="false">全选</a>
					<a href="javascript:" class="btn btn-outline-secondary checkreverse-btn">反选</a>
				</div>
				<div class="btn-group btn-group-sm mr-2" role="group" aria-label="action button group">
					<a href="javascript:" class="btn btn-outline-secondary action-btn" data-action="publish">发布</a>
					<a href="javascript:" class="btn btn-outline-secondary action-btn" data-action="cancel">撤销</a>
					<a href="javascript:" class="btn btn-outline-secondary action-btn" data-action="delete">删除</a>
				</div>
				<a href="{:url('product/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加商品</a>
			</div>
		</div>
		<div class="col-md-6">
			<form action="{:url('product/index')}" method="post">
				<div class="form-row">
					<div class="col input-group input-group-sm mr-2">
						<div class="input-group-prepend">
							<span class="input-group-text">分类</span>
						</div>
						<select name="cate_id" class="form-control">
							<option value="0">不限分类</option>
							<foreach name="category" item="v">
								<option value="{$v.id}" {$cate_id == $v['id']?'selected="selected"':""}>{$v.html} {$v.title}</option>
							</foreach>
						</select>
					</div>
					<div class="col input-group input-group-sm">
						<input type="text" class="form-control" name="key" value="{$keyword}" placeholder="搜索标题、作者或分类">
						<div class="input-group-append">
							<button class="btn btn-outline-secondary" type="submit"><i class="ion-md-search"></i></button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<table class="table table-hover table-striped">
		<thead>
			<tr>
				<th width="50">编号</th>
				<th>标题</th>
				<th>类型</th>
				<th>发布时间</th>
				<th>作者</th>
				<th>分类</th>
				<th>状态</th>
				<th width="200">操作</th>
			</tr>
		</thead>
		<tbody>
			<foreach name="lists" item="v">
				<tr>
					<td><input type="checkbox" name="id" value="{$v.id}" /></td>
					<td>{$v.title}</td>
					<td>
						<if condition="$v.type eq 1"><span class="label label-default">普通</span>
							<elseif condition="$v.type eq 2" /><span class="label label-success">置顶</span>
							<elseif condition="$v.type eq 3" /><span class="label label-danger">热门</span>
							<elseif condition="$v.type eq 4" /><span class="label label-success">推荐</span>
						</if>
					</td>
					<td>{$v.create_time|showdate}</td>
					<td>{$v.username}</td>
					<td>{$v.category_title}</td>
					<td>
						<if condition="$v.status eq 1">
							<span class="badge badge-success">已发布</span>
							<else/>
							<span class="badge badge-secondary">未发布</span>
						</if>
					</td>
					<td>
					<a class="btn btn-outline-dark btn-sm" href="{:url('product/edit',array('id'=>$v['id']))}"><i class="ion-md-create"></i> 编辑</a>
					<a class="btn btn-outline-dark btn-sm" href="{:url('product/delete',array('id'=>$v['id']))}" onclick="javascript:return del('您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="ion-md-trash"></i> 删除</a>
					</td>
				</tr>
			</foreach>
		</tbody>
	</table>
	<div class="clearfix"></div>
	{$page|raw}

</div>
</block>
<block name="script">
	<script type="text/javascript">
		(function(w){
			w.actionPublish=function(ids){
				dialog.confirm('确定将选中文章发布到前台？',function() {
				    $.ajax({
						url:'{:url('article/push',['id'=>'__id__','type'=>1])}'.replace('__id__',ids.join(',')),
						type:'GET',
						dataType:'JSON',
						success:function(json){
						    if(json.code==1){
                                dialog.alert(json.msg,function() {
                                    location.reload();
                                });
                            }else{
						        toastr.warning(json.msg);
                            }
                        }
					});
                });
            };
            w.actionCancel=function(ids){
                dialog.confirm('确定取消选中文章的发布状态？',function() {
                    $.ajax({
                        url:'{:url('article/push',['id'=>'__id__','type'=>0])}'.replace('__id__',ids.join(',')),
                        type:'GET',
                        dataType:'JSON',
                        success:function(json){
                            if(json.code==1){
                                dialog.alert(json.msg,function() {
                                    location.reload();
                                });
                            }else{
                                toastr.warning(json.msg);
                            }
                        }
                    });
                });
            };
            w.actionDelete=function(ids){
                dialog.confirm('确定删除选中的文章？',function() {
                    $.ajax({
                        url:'{:url('article/delete',['id'=>'__id__'])}'.replace('__id__',ids.join(',')),
                        type:'GET',
                        dataType:'JSON',
                        success:function(json){
                            if(json.code==1){
                                dialog.alert(json.msg,function() {
                                    location.reload();
                                });
                            }else{
                                toastr.warning(json.msg);
                            }
                        }
                    });
                });
            };
        })(window)
	</script>
</block>