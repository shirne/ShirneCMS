<extend name="public:base" />

<block name="body">
<include file="public/bread" menu="shop_help_index" title="帮助列表" />
<div id="page-wrapper">
	<div class="row">
		<div class="col" style="width: 240px;flex-basis:240px;flex-grow: 0;">
			<div class="card side-cate" >
				<div class="card-header">
					<a href="javascript:" class="btn float-right btn-outline-primary btn-sm addcate">添加</a>
				  帮助类目
				</div>
				<ul class="list-group list-group-flush">
					<li class="list-group-item"><a class="list-cate-item" href="{:url('index',['key'=>$keyword,'cate_id'=>0])}" data-value="0">不限分类</a></li>
				<foreach name="category" item="v">
					<li class="list-group-item{$cate_id == $v['id']?' active':''}">
						<a href="javascript:" data-id="{$v['id']}" title="删除分类" class="float-right ml-2 text-danger delcate"><i class="ion-md-trash"></i></a>
						<a href="javascript:" data-pid="{$v['id']}" title="添加子类" class="float-right ml-2 addcate"><i class="ion-md-add"></i></a>
						<a href="javascript:" data-id="{$v['id']}" title="编辑分类" data-title="{$v['title']}" data-short="{$v['short']}" data-name="{$v['name']}" data-sort="{$v['sort']}" data-pid="{$v['pid']}" class="float-right addcate"><i class="ion-md-create"></i></a>
						<a class="list-cate-item" href="{:url('index',['key'=>$keyword,'cate_id'=>$v['id']])}" data-value="{$v.id}" >{$v.html} {$v.title}</a>
					</li>
				</foreach>
				
				</ul>
			</div>
		</div>
		<div class="col">
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
							<a href="javascript:" class="btn btn-outline-secondary action-btn" data-action="delete">{:lang('Delete')}</a>
						</div>
						<a href="{:url('shop.help/add',['cid'=>$cate_id])}" class="btn btn-outline-primary btn-sm mr-2"><i class="ion-md-add"></i> 添加帮助</a>
						<a href="javascript:" class="btn btn-outline-warning btn-sm action-btn" data-need-checks="false" data-action="setIncrement"><i class="ion-md-add"></i> 设置起始ID</a>
					</div>
				</div>
				<div class="col-md-6">
					<form action="{:url('shop.help/index')}" method="post">
						<div class="form-row">
							<div class="col input-group input-group-sm">
								<input type="text" class="form-control" name="key" value="{$keyword}" placeholder="按标题搜索">
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
						<th width="160">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<empty name="lists">{:list_empty(8)}</empty>
					<volist name="lists" id="v" >
						<tr>
							<td><input type="checkbox" name="id" value="{$v.id}" /></td>
							<td><a href="{:url('index/help/view',['id'=>$v['id']])}" target="_blank">{$v.title}</a> </td>
							<td>
								<span class="badge badge-info">{$types[$v['type']]}</span>
							</td>
							<td>{$v.create_time|showdate}</td>
							<td>{$v.username}</td>
							<td>{$v.category_title}</td>
							<td data-url="{:url('status')}" data-id="{$v.id}">
								<if condition="$v['status'] EQ 1">
									<span class="chgstatus" data-status="0" title="点击隐藏">已发布</span>
									<else/>
									<span class="chgstatus off" data-status="1" title="点击发布">未发布</span>
								</if>
							</td>
							<td class="operations">
							<a class="btn btn-outline-primary" title="编辑" href="{:url('shop.help/edit',array('id'=>$v['id'],'cid'=>$cate_id))}"><i class="ion-md-create"></i> </a>
							<a class="btn btn-outline-danger link-confirm" title="{:lang('Delete')}" data-confirm="您真的确定要删除吗？\n删除后将不能恢复!" href="{:url('shop.help/delete',array('id'=>$v['id']))}" ><i class="ion-md-trash"></i> </a>
							</td>
						</tr>
					</volist>
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
			<div class="input-group"><span class="input-group-prepend"><span class="input-group-text">分类名称</span></span><input class="form-control" name="title" placeholder="分类显示的名称"/></div>
			</div>
			<div class="form-group">
			<div class="input-group"><span class="input-group-prepend"><span class="input-group-text">分类简称</span></span><input class="form-control" name="short" placeholder="分类名称的简写"/></div>
			</div>
			<div class="form-group">
			<div class="input-group"><span class="input-group-prepend"><span class="input-group-text">分类别名</span></span><input class="form-control" name="name" placeholder="分类对应的唯一识别符号,只能为英文"/></div>
			</div>
			<div class="form-group">
				<div class="input-group"><span class="input-group-prepend"><span class="input-group-text">上级分类</span></span><select class="form-control" name="pid">
					<option value="0">顶级分类</option>
						<foreach name="category" item="v">
							<option value="{$v.id}" >{$v.html} {$v.title}</option>
						</foreach>
					</select>
				</div>
			</div>
		</div>
	</script>
	<script type="text/javascript">
		(function(w){
			w.actionPublish=function(ids){
				dialog.confirm('确定将选中帮助发布到前台？',function() {
				    $.ajax({
						url:"{:url('shop.help/status',['id'=>'__id__','status'=>1])}".replace('__id__',ids.join(',')),
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
                });
            };
            w.actionCancel=function(ids){
                dialog.confirm('确定取消选中帮助的发布状态？',function() {
                    $.ajax({
                        url:"{:url('shop.help/status',['id'=>'__id__','status'=>0])}".replace('__id__',ids.join(',')),
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
                });
            };
            w.actionDelete=function(ids){
                dialog.confirm('确定删除选中的帮助？',function() {
                    $.ajax({
                        url:"{:url('shop.help/delete',['id'=>'__id__'])}".replace('__id__',ids.join(',')),
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
                });
            };
			w.actionSetIncrement=function () {
				dialog.prompt('请输入新的起始ID',function (input) {
					$.ajax({
						url:"{:url('shop.help/set_increment',['incre'=>'__INCRE__'])}".replace('__INCRE__',input),
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
		
		jQuery(function($){
			$('.addcate').click(function(){
				var data=$(this).data();
				var dlg = new Dialog({
					backdrop:'static',
					onshown:function(body){
						bindData(body,data)
					},
					onsure:function(body){
						var newData=getData(body)
						newData.id=data.id?data.id:0
						$.ajax({
							url:"{:url('category')}",
							data:newData,
							type:'POST',
							dataType:'json',
							success:function(json){
								if(json.code==1){
									dialog.alert(json.msg,function(){
										location.reload()
									})
								}else{
									dialog.error(json.msg);
								}
							}
						})
						return false;
					}
				}).show($('#cate-template').html(),data.id>0?'编辑分类':'添加分类');
				
			})
			$('.delcate').click(function(e){
				var id = $(this).data('id');
				dialog.confirm('确定删除该分类？',function(){
					$.ajax({
						url:"{:url('category_delete')}",
						data:{id : id},
						type:'POST',
						dataType:'json',
						success:function(json){
							if(json.code==1){
								dialog.alert(json.msg,function(){
									location.reload()
								})
							}else{
								dialog.error(json.msg);
							}
						}
					})
				})
			});
		})
	</script>
</block>