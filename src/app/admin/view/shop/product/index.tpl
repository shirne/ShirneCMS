{extend name="public:base" /}

{block name="body"}
{include  file="public/bread" menu="shop_product_index" title="商品列表"  /}
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
				<a href="{:url('shop.product/add')}" class="btn btn-outline-primary btn-sm mr-2"><i class="ion-md-add"></i> 添加商品</a>
				<a href="javascript:" class="btn btn-outline-warning btn-sm action-btn" data-need-checks="false" data-action="increment"><i class="ion-md-add"></i> 设置起始ID</a>
			</div>
		</div>
		<div class="col-md-6">
			<form action="{:url('shop.product/index')}" method="post">
				<div class="form-row">
					<div class="col input-group input-group-sm mr-2">
						<div class="input-group-prepend">
							<span class="input-group-text">分类</span>
						</div>
						<select name="cate_id" class="form-control">
							<option value="0">不限分类</option>
							{foreach name="category" item="v"}
								<option value="{$v.id}" {$cate_id == $v['id']?'selected="selected"':""}>{$v.html} {$v.title}</option>
							{/foreach}
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
				<th>产品名称</th>
				<th>SKU</th>
				<th>发布时间</th>
				<th>分类</th>
				<th>状态</th>
				<th width="200">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		{empty name="lists"}{:list_empty(8)}{/empty}
			{volist name="lists" id="v" }
				<tr>
					<td><input type="checkbox" name="id" value="{$v.id}" /></td>
					<td>
						<figure class="figure img-view" data-img="{$v.image}" >
							<img src="{$v.image|default='/static/images/nopic.png'}?w=100" class="figure-img img-fluid rounded" alt="image">
						</figure>
					</td>
					<td>
						{if $v['type'] GT 1}<span class="badge badge-warning">{$types[$v['type']]}</span>{/if}
						<a href="{:url('index/product/view',['id'=>$v['id']])}" target="_blank">{$v.title}</a>
						<if condition="!empty($v['unit'])"><span class="badge badge-info">{$v.unit}</span></if>
						<span class="text-muted">销量: {$v.sale}</span>
					</td>
					<td>
						{foreach name="v['skus']" item="sku"}
							<div class="input-group input-group-sm mb-2">
								<span class="input-group-prepend">
									<span class="input-group-text">{$sku.goods_no}</span>
								</span>
								<span class="form-control">￥{$sku.price}</span>
								<span class="input-group-middle">
									<span class="input-group-text">库存</span>
								</span>
								<span class="form-control">{$sku.storage}</span>
								<span class="input-group-append">
									<a href="javascript:" data-price="{$sku.price}" data-skuid="{$sku.sku_id}" data-storage="{$sku.storage}" class="btn btn-outline-primary btn-edit-sku"><i class="ion-md-create"></i></a>
								</span>
							</div>
						{/foreach}
					</td>
					<td>{$v.create_time|showdate}</td>
					<td>{$v.category_title}</td>
					<td data-url="{:url('push')}" data-id="{$v.id}">
						{if $v['status'] EQ 1}
							<span class="chgstatus" data-status="0" title="点击下架">已上架</span>
							{else/}
							<span class="chgstatus off" data-status="1" title="点击上架">已下架</span>
						{/if}
					</td>
					<td class="operations">
						<a class="btn btn-outline-primary" title="编辑" href="{:url('shop.product/edit',array('id'=>$v['id']))}"><i class="ion-md-create"></i> </a>
						<a class="btn btn-outline-primary qrcode-btn" data-id="{$v.id}" title="二维码" href="javascript:"><i class="ion-md-qr-scanner"></i> </a>
						<a class="btn btn-outline-primary" title="图集" href="{:url('shop.product/imagelist',array('aid'=>$v['id']))}"><i class="ion-md-images"></i> </a>
						<a class="btn btn-outline-primary" title="评论" href="{:url('shop.product/comments',array('aid'=>$v['id']))}"><i class="ion-md-chatboxes"></i> </a>
						<a class="btn btn-outline-danger link-confirm" title="删除" data-confirm="您真的确定要删除吗？\n删除后将不能恢复!" href="{:url('shop.product/delete',array('id'=>$v['id']))}" ><i class="ion-md-trash"></i> </a>
					</td>
				</tr>
			{/volist}
		</tbody>
	</table>
	<div class="clearfix"></div>
	{$page|raw}

</div>
{/block}
{block name="script"}
	<script type="text/html" id="qrdialog-tpl">
		<form type="post" target="_blank" action="{:url('qrcode')}" >
			<input type="hidden" name="id" />
			<div class="form-group">
				<label for="qrtype">生成类型</label>
				<div class="btn-group btn-group-toggle" data-toggle="buttons">
					<label class="btn btn-outline-primary"> <input type="radio" name="qrtype" value="url" autocomplete="off" > 网址二维码</label>
					<label class="btn btn-outline-primary"> <input type="radio" name="qrtype" value="minicode" autocomplete="off" > 小程序码</label>
					<label class="btn btn-outline-primary"> <input type="radio" name="qrtype" value="miniqr" autocomplete="off" > 小程序二维码</label>

				</div>
			</div>
			<div class="form-group miniprogramrow">
				<label for="miniprogram">小程序</label>
				<div class="input-group">
					<select name="miniprogram" class="form-control">
						<option value="">请选择小程序</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="size">图片大小</label>
				<div class="input-group">
					<input type="text" name="size" class="form-control" value="430">
				</div>
			</div>
		</form>
	</script>
	<script type="text/javascript">
		(function(w){
			w.actionPublish=function(ids){
				dialog.confirm('确定将选中产品发布到前台？',function() {
				    $.ajax({
						url:"{:url('shop.product/push',['id'=>'__id__','status'=>1])}".replace('__id__',ids.join(',')),
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
                dialog.confirm('确定取消选中产品的发布状态？',function() {
                    $.ajax({
                        url:"{:url('shop.product/push',['id'=>'__id__','status'=>0])}".replace('__id__',ids.join(',')),
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
                dialog.confirm('确定删除选中的产品？',function() {
                    $.ajax({
                        url:"{:url('shop.product/delete',['id'=>'__id__'])}".replace('__id__',ids.join(',')),
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
						url:"{:url('shop.product/set_increment',['incre'=>'__INCRE__'])}".replace('__INCRE__',input),
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
        })(window);
		jQuery(function($){
			var html=$('#qrdialog-tpl').text();
			$('.qrcode-btn').click(function(){
				var id=$(this).data('id')
				var dlg = new Dialog({
					onshown:function(body){
						body.find('[name=id]').val(id);
						var mprow=body.find('.miniprogramrow');
						body.find('[name=qrtype]').change(function(e){
							if($(this).val()=='url'){
								mprow.hide();
							}else{
								mprow.show();
							}
						}).eq(0).parent().trigger('click');
						$.ajax({
							url:"{:url('wechat/search',['type'=>'miniprogram'])}",
							dataType:'json',
							success:function(json){
								if(json.code==1){
									var select=body.find('[name=miniprogram]')
									if(json.data && json.data.length>0){
										select.append('<option value="{@id}">{@title}</option>'.compile(json.data,true))
										select.val(json.data[0].id)
										body.find('[name=qrtype]').eq(1).parent().trigger('click');
									}else{
										select.find('option').eq(0).text('暂无可用的小程序')
									}
								}
							}
						})
					},
					onsure:function(body){
						body.find('form').submit();
					}
				}).show(html,'生成二维码');
			})
			$('.btn-edit-sku').click(function(e){
				e.preventDefault();
				var sku_id = $(this).data('skuid');
				var price = $(this).data('price');
				var storage = $(this).data('storage');
				var pdlg = dialog.prompt({title:'编辑库存价格',multi:{
					price:{title:'价格',value:price},
					storage:{title:'库存',value:storage}
				}},function(data){
					data.price = parseFloat(data.price)
					data.storage = parseInt(data.storage)
					if(!data.price){
						dialog.alert('请填写价格')
						return false;
					}
					if(isNaN(data.storage)){
						dialog.alert('请填写库存')
						return false;
					}
					$.ajax({
						url:"{:url('shop.product/editsku')}",
						dataType:'json',
						type:'POST',
						data:{
							sku_id:sku_id,
							price:data.price,
							storage:data.storage
						},
						success:function(json){
							if(json.code == 1){
								dialog.alert(json.msg,function(){
									location.reload();
								})
							}else{
								dialog.error(json.msg);
							}
						}
					});
					return false;
				})
			})
		})
	</script>
{/block}