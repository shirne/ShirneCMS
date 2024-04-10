{extend name="public:base" /}

{block name="body"}

{include file="public/bread" menu="credit_category_index" title="" /}

<div id="page-wrapper">
    
    <div class="row list-header">
        <div class="col-6">
            <a href="{:url('credit.category/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> {:lang('Add Category')}</a>
            <a href="javascript:" class="btn btn-outline-primary btn-sm btn-batch-add"><i class="ion-md-albums"></i> {:lang('Batch add Categories')}</a>
        </div>
        <div class="col-6">
            <form action="{:url('credit.category/index')}" method="post">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" name="key" placeholder="{:lang('Search title or slug')}">
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
                <th>名称</th>
                <th>别名</th>
                <th>排序</th>
                <th width="250">操作</th>
            </tr>
        </thead>
        <tbody>
        {empty name="lists"}{:list_empty(5)}{/empty}
        {volist name="lists" id="v" }
            <tr>
                <td>{$v.id}</td>
                <td>{$v.html} {$v.title}&nbsp;<span class="badge badge-info">{$v.short}</span>{if $v['use_template'] == 1}&nbsp;<span class="badge badge-warning">独立模板</span>{/if}</td>
                <td>{$v.name}</td>
                <td>{$v.sort}</td>
                <td>
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <a class="btn btn-outline-dark btn-sm" href="{:url('credit.goods/add',array('cid'=>$v['id']))}"><i class="ion-md-add"></i> 发布</a>
                    <a class="btn btn-outline-dark btn-sm" href="{:url('credit.category/add',array('pid'=>$v['id']))}"><i class="ion-md-add"></i> 添加</a>
                    <a class="btn btn-outline-dark btn-sm" href="{:url('credit.category/edit',array('id'=>$v['id']))}"><i class="ion-md-create"></i> 编辑</a>
                    <a class="btn btn-outline-dark btn-sm" href="{:url('credit.category/delete',array('id'=>$v['id']))}" onclick="javascript:return del(this,'您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="ion-md-trash"></i> 删除</a>
                    </div>
                </td>
            </tr>
        {/volist}
        </tbody>
    </table>
</div>

{/block}

{block name="script"}
    <script type="text/html" id="cateselect">
        <div class="form-group">
            <select class="form-control">
                <option value="0">顶级分类</option>
                {volist name="lists" id="cate"}
                    <option value="{$cate.id}">{$cate.html|raw} {$cate.title}</option>
                {/volist}
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
{/block}