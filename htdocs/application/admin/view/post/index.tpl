<extend name="Public:Base" />

<block name="body">
<include file="Public/bread" menu="post_index" section="内容" title="文章管理" />
<div id="page-wrapper">

	<div class="row list-header">
		<div class="col-6">
			<a href="{:url('post/add')}" class="btn btn-outline-primary">添加文章</a>
		</div>
		<div class="col-6">
			<form action="{:url('post/index')}" method="post">
				<div class="form-group input-group">
					<input type="text" class="form-control" name="key" value="{$keyword}" placeholder="输入文章标题、作者或者分类关键词搜索">
					<div class="input-group-append">
                      <button class="btn btn-outline-secondary" type="submit"><i class="ion-search"></i></button>
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
					<td>{$v.id}</td>
					<td>{$v.title}</td>
					<td>
						<if condition="$v.type eq 1"><span class="label label-default">普通</span>
							<elseif condition="$v.type eq 2" /><span class="label label-success">置顶</span>
							<elseif condition="$v.type eq 3" /><span class="label label-danger">热门</span>
							<elseif condition="$v.type eq 4" /><span class="label label-success">推荐</span>
						</if>
					</td>
					<td>{$v.time|showdate}</td>
					<td>{$v.username}</td>
					<td>{$v.category_title}</td>
					<td>
						<if condition="$v.status eq 1">
							<a class="btn btn-outline-warning btn-sm" href="{:url('post/push',array('id'=>$v['id']))}"><i class="ion-reply-all"></i> 撤销</a>
							<else/>
							<a class="btn btn-outline-success btn-sm" href="{:url('post/push',array('id'=>$v['id']))}"><i class="ion-paper-airplane"></i> 发布</a>
						</if>
					</td>

					<td>
					<a class="btn btn-outline-dark btn-sm" href="{:url('post/edit',array('id'=>$v['id']))}"><i class="ion-edit"></i> 编辑</a>
					<a class="btn btn-outline-dark btn-sm" href="{:url('post/delete',array('id'=>$v['id']))}" onclick="javascript:return del('您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="ion-trash-a"></i> 删除</a>
					</td>
				</tr>
			</foreach>
		</tbody>
	</table>
	<div class="clearfix"></div>
	{$page|raw}

</div>
</block>