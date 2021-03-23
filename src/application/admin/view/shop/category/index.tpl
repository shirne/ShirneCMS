<extend name="public:base" />

<block name="body">

<include file="public/bread" menu="shop_category_index" title="" />

<div id="page-wrapper">
    
    <div class="row list-header">
        <div class="col-6">
            <a href="{:url('shop.specifications/index')}" class="btn btn-outline-info btn-sm"><i class="ion-md-pricetags"></i> 规格管理</a>
            <a href="{:url('shop.category/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加分类</a>
            <a href="javascript:" class="btn btn-outline-primary btn-sm btn-batch-add"><i class="ion-md-albums"></i> {:lang('Batch add Categories')}</a>
        </div>
        <div class="col-6">
            <form action="{:url('shop.category/index')}" method="post">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" name="key" placeholder="输入分类标题或者别名关键词搜索">
                    <div class="input-group-append">
                      <button class="btn btn-outline-secondary" type="submit"><i class="ion-md-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th width="50">编号</th>
                <th width="70">图标</th>
                <th>名称</th>
                <th>别名</th>
                <th>排序</th>
                <th width="160">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        <volist name="model" id="v" empty="$empty">
            <tr>
                <td>{$v.id}</td>
                <td><img src="{$v.icon}" class="rounded" width="60"/></td>
                <td>{$v.html|raw} {$v.title}&nbsp;<span class="badge badge-info">{$v.short}</span></td>
                <td>{$v.name}</td>
                <td>{$v.sort}</td>
                <td class="operations">
                    <a class="btn btn-outline-primary" title="发布商品" href="{:url('shop.product/add',array('cid'=>$v['id']))}"><i class="ion-md-paper-plane"></i> </a>
                    <a class="btn btn-outline-primary" title="添加子分类" href="{:url('shop.category/add',array('pid'=>$v['id']))}"><i class="ion-md-add"></i> </a>
                    <a class="btn btn-outline-primary" title="编辑" href="{:url('shop.category/edit',array('id'=>$v['id']))}"><i class="ion-md-create"></i> </a>
                    <a class="btn btn-outline-danger link-confirm" title="删除" data-confirm="您真的确定要删除吗？\n删除后将不能恢复!" href="{:url('shop.category/delete',array('id'=>$v['id']))}" ><i class="ion-md-trash"></i> </a>
                </td>
            </tr>
        </volist>
        </tbody>
    </table>
</div>

</block>
<block name="script">
    <script type="text/html" id="cateselect">
        <div class="form-group">
            <select class="form-control">
                <option value="0">顶级分类</option>
                <volist name="model" id="cate">
                    <option value="{$cate.id}">{$cate.html|raw} {$cate.title}</option>
                </volist>
            </select>
        </div>
        <div class="form-group text-muted">每行一个分类，每个分类以空格区分名称、简称、别名，简称、别名可依次省略，别名必须使用英文字母<br />例：分类名称 分类简称 catename</div>
    </script>
    <script>
        jQuery(function(){
            $('.btn-batch-add').click(function(e){
                var prmpt=dialog.prompt({
                    title:'批量添加',
                    content:$('#cateselect').html(),
                    is_textarea:true
                },function(args,body){
                    var pid=body.find('select').val();
                    var loading = dialog.loading('正在提交...');
                    $.ajax({
                        url:"{:url('batch')}",
                        type:'POST',
                        dataType:'json',
                        data:{
                            pid: pid,
                            content: args
                        },
                        success:function(json){
                            loading.close();
                            if(json.code == 1){
                                dialog.success(json.msg)
                                prmpt.close()          
                                setTimeout(function(){
                                    location.reload()
                                },1500);                      
                            }else{
                                dialog.error(json.msg)
                            }

                        }
                    })
                    return false;
                })
            })
        })
    </script>
</block>