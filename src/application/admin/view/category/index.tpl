<extend name="public:base" />

<block name="body">

<include file="public/bread" menu="category_index" title="" />

<div id="page-wrapper">
    
    <div class="row list-header">
        <div class="col-6">
            <a href="{:url('category/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> {:lang('Add Category')}</a>
            <a href="javascript:" class="btn btn-outline-primary btn-sm btn-batch-add"><i class="ion-md-albums"></i> {:lang('Batch add Categories')}</a>
        </div>
        <div class="col-6">
            <form action="{:url('category/index')}" method="post">
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
                <th width="50">{:lang('ID')}</th>
                <th>{:lang('Title')}</th>
                <th>{:lang('Slug')}</th>
                <th>{:lang('Sort')}</th>
                <th width="160">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        <volist name="model" id="v" empty="$empty">
            <tr>
                <td>{$v.id}</td>
                <td>{$v.html|raw} {$v.title}&nbsp;<span class="badge badge-info">{$v.short}</span><if condition="$v.use_template EQ 1">&nbsp;<span class="badge badge-warning">{:lang('Independ Template')}</span></if></td>
                <td>{$v.name}</td>
                <td>{$v.sort}</td>
                <td class="operations">
                    <a class="btn btn-outline-primary" title="{:lang('Publish Article')}" href="{:url('article/add',array('cid'=>$v['id']))}"><i class="ion-md-paper-plane"></i> </a>
                    <a class="btn btn-outline-primary" title="{:lang('Add Sub Category')}" href="{:url('category/add',array('pid'=>$v['id']))}"><i class="ion-md-add"></i> </a>
                    <a class="btn btn-outline-primary" title="{:lang('Edit')}" href="{:url('category/edit',array('id'=>$v['id']))}"><i class="ion-md-create"></i> </a>
                    <a class="btn btn-outline-danger link-confirm" title="{:lang('Delete')}" data-confirm="{:lang('Confirm to delete? The operation can not restore!')}" href="{:url('category/delete',array('id'=>$v['id']))}"  ><i class="ion-md-trash"></i> </a>
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