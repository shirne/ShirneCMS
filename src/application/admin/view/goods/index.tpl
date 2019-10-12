<extend name="public:base" />

<block name="body">
<include file="public/bread" menu="goods_index" title="商品列表" />
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
				<a href="{:url('goods/add')}" class="btn btn-outline-primary btn-sm mr-2"><i class="ion-md-add"></i> 添加商品</a>
				<a href="javascript:" class="btn btn-outline-warning btn-sm action-btn" data-need-checks="false" data-action="setIncrement"><i class="ion-md-add"></i> 设置起始ID</a>
			</div>
		</div>
		<div class="col-md-6">
			<form action="{:url('goods/index')}" method="post">
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
				<th>图片</th>
				<th>商品名称</th>
				<th>价格</th>
				<th>库存</th>
				<th>已售</th>
				<th>发布时间</th>
				<th>分类</th>
				<th>状态</th>
				<th width="200">操作</th>
			</tr>
		</thead>
		<tbody>
		<empty name="lists">{:list_empty(10)}</empty>
			<foreach name="lists" item="v" >
				<tr>
					<td><input type="checkbox" name="id" value="{$v.id}" /></td>
					<td><figure class="figure" >
							<img src="{$v.image}?w=100" class="figure-img img-fluid rounded" alt="image">
						</figure></td>
					<td><if condition="$v['type'] GT 1"><span class="badge badge-info">{$types[$v['type']]}</span></if>{$v.title}</td>
					<td>
						{$v.price} 积分/{$v['vice_title']|default='件'}
					</td>
					<td>
						{$v.storage}
					</td>
					<td>
                        {$v.sale}
					</td>
					<td>{$v.create_time|showdate}</td>
					<td>{$v.category_title}</td>
					<td>
						<if condition="$v.status eq 1">
							<span class="badge badge-success">已发布</span>
							<else/>
							<span class="badge badge-secondary">未发布</span>
						</if>
					</td>
					<td>
					<a class="btn btn-outline-dark btn-sm" href="{:url('goods/edit',array('id'=>$v['id']))}"><i class="ion-md-create"></i> 编辑</a>
						<a class="btn btn-outline-dark btn-sm" href="{:url('goods/imagelist',array('aid'=>$v['id']))}"><i class="ion-md-images"></i> 图集</a>
						<!--a class="btn btn-outline-dark btn-sm" href="{:url('goods/comments',array('aid'=>$v['id']))}"><i class="ion-md-chatboxes"></i> 评论</a-->
					<a class="btn btn-outline-dark btn-sm" href="{:url('goods/delete',array('id'=>$v['id']))}" onclick="javascript:return del(this,'您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="ion-md-trash"></i> 删除</a>
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
						url:'{:url('goods/push',['id'=>'__id__','type'=>1])}'.replace('__id__',ids.join(',')),
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
                        url:'{:url('goods/push',['id'=>'__id__','type'=>0])}'.replace('__id__',ids.join(',')),
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
                        url:'{:url('goods/delete',['id'=>'__id__'])}'.replace('__id__',ids.join(',')),
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
			w.actionSetIncrement=function () {
				dialog.prompt('请输入新的起始ID',function (input) {
					$.ajax({
						url:"{:url('goods/set_increment',['incre'=>'__INCRE__'])}".replace('__INCRE__',input),
						type:'GET',
						dataType:'JSON',
						success:function(json){
							if(json.code==1){
								dialog.alert(json.msg,function() {
									location.reload();
								});
							}else{
								dialog.warning(json.msg);
							}
						}
					});
				})
			}
        })(window)
	</script>
</block>