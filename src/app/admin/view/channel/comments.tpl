{extend name="public:base" /}

{block name="body"}
    {include file="public/bread" menu="article_comments" title="文章列表" /}
    <div id="page-wrapper">

        <div class="row list-header">
            <div class="col-md-6">
                <div class="btn-toolbar list-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                    <div class="btn-group btn-group-sm mr-2" role="group" aria-label="check action group">
                        <a href="javascript:" class="btn btn-outline-secondary checkall-btn" data-toggle="button" aria-pressed="false">全选</a>
                        <a href="javascript:" class="btn btn-outline-secondary checkreverse-btn">反选</a>
                    </div>
                    <div class="btn-group btn-group-sm mr-2" role="group" aria-label="action button group">
                        <a href="javascript:" class="btn btn-outline-secondary action-btn" data-action="audit">审核</a>
                        <a href="javascript:" class="btn btn-outline-secondary action-btn" data-action="hidden">隐藏</a>
                        <a href="javascript:" class="btn btn-outline-secondary action-btn" data-action="delete">删除</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <form action="{:url('article/index')}" method="post">
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
                            <input type="text" class="form-control" name="key" value="{$keyword}" placeholder="搜索文章标题或分类">
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
                <th>文章</th>
                <th width="160">发布时间</th>
                <th width="160">昵称</th>
                <th width="160">邮箱</th>
                <th>内容摘要</th>
                <th width="60">状态</th>
                <th width="80">&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            {foreach name="lists" item="v"}
                <tr>
                    <td><input type="checkbox" name="id" value="{$v.id}" /></td>
                    <td>[{$v.category_title}]{$v.article_title}</td>
                    <td>{$v.create_time|showdate}</td>
                    <td>{$v.nickname}<br />{$v.username}</td>
                    <td>{$v.email}</td>
                    <td>{$v.content|cutstr=20}</td>
                    <td>
                        {if $v.status == 1}
                            <span class="badge badge-success">已审核</span>
                            {else/}
                            <span class="badge badge-warning">未审核</span>
                        {/if}
                    </td>

                    <td class="operations">
                        <a class="btn btn-outline-primary" title="查看" href="{:url('article/commentview',array('id'=>$v['id']))}"><i class="ion-md-create"></i> </a>
                        <a class="btn btn-outline-danger link-confirm" title="删除" data-confirm="您真的确定要删除吗？\n删除后将不能恢复!" href="{:url('article/commentdelete',array('id'=>$v['id']))}" ><i class="ion-md-trash"></i> </a>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
        <div class="clearfix"></div>
        {$page|raw}

    </div>
{/block}
{block name="script"}
    <script type="text/javascript">
        (function(w){
            w.actionAudit=function(ids){
                dialog.confirm('确定将选中的评论设为已审核？',function() {
                    $.ajax({
                        url:"{:url('article/commentstatus',['id'=>'__id__','type'=>1])}".replace('__id__',ids.join(',')),
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
            w.actionHidden=function(ids){
                dialog.confirm('确定将选中的评论隐藏？',function() {
                    $.ajax({
                        url:"{:url('article/commentstatus',['id'=>'__id__','type'=>2])}".replace('__id__',ids.join(',')),
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
                dialog.confirm('确定删除选中的评论？',function() {
                    $.ajax({
                        url:"{:url('article/commentdelete',['id'=>'__id__'])}".replace('__id__',ids.join(',')),
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
        })(window)
    </script>
{/block}