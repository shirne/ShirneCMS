<extend name="public:base" />

<block name="body">

<include file="public/bread" menu="page_index" title="单页列表" />

<div id="page-wrapper">
    
    <div class="row list-header">
        <div class="col-6">
            <div class="btn-group btn-group-sm mr-2" role="group" aria-label="check action group">
                <a href="javascript:" class="btn btn-outline-secondary checkall-btn" data-toggle="button" aria-pressed="false">全选</a>
                <a href="javascript:" class="btn btn-outline-secondary checkreverse-btn">反选</a>
            </div>
            <div class="btn-group btn-group-sm mr-2" role="group" aria-label="action button group">
                <a href="javascript:" class="btn btn-outline-secondary action-btn" data-action="show">显示</a>
                <a href="javascript:" class="btn btn-outline-secondary action-btn" data-action="hide">隐藏</a>
                <a href="javascript:" class="btn btn-outline-secondary action-btn" data-action="delete">删除</a>
            </div>
            <a href="{:url('page/add')}" class="btn btn-outline-primary btn-sm">添加单页</a>
            <a href="{:url('page/groups')}" class="btn btn-outline-secondary btn-sm">分组管理</a>
        </div>
        <div class="col-6">
            <form action="{:url('page/index')}" method="post">
                <div class="form-group input-group input-group-sm">
                    <input type="text" class="form-control" name="key" placeholder="输入单页标题或者别名关键词搜索">
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
                <th>分组</th>
                <th>别名</th>
                <th>标题</th>
                <th>排序</th>
                <th>状态</th>
                <th width="200">操作</th>
            </tr>
        </thead>
        <tbody>
        <foreach name="lists" item="v">
            <tr>
                <td><input type="checkbox" name="id" value="{$v.id}" /></td>
                <td>{$v.group}</td>
                <td>{$v.name}</td>
                <td>{$v.title}</td>
                <td>{$v.sort}</td>
                <td>
                    <if condition="$v.status eq 1">
                        <span class="badge badge-success">显示</span>
                        <else/>
                        <span class="badge badge-secondary">隐藏</span>
                    </if>
                </td>
                <td>
                    <a class="btn btn-outline-dark btn-sm" href="{:url('page/edit',array('id'=>$v['id']))}"><i class="ion-edit"></i> 编辑</a>
                    <a class="btn btn-outline-dark btn-sm" href="{:url('page/delete',array('id'=>$v['id']))}" onclick="javascript:return del('您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="ion-trash-a"></i> 删除</a>
                </td>
            </tr>
        </foreach>
        </tbody>
    </table>
    {$page|raw}
</div>

</block>
<block name="script">
    <script type="text/javascript">
        (function(w){
            w.actionShow=function(ids){
                dialog.confirm('确定将选中页面显示？',function() {
                    $.ajax({
                        url:'{:url('page/status',['id'=>'__id__','type'=>1])}'.replace('__id__',ids.join(',')),
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
            w.actionHide=function(ids){
                dialog.confirm('确定将选中页面隐藏？',function() {
                    $.ajax({
                        url:'{:url('page/status',['id'=>'__id__','type'=>0])}'.replace('__id__',ids.join(',')),
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
                dialog.confirm('确定删除选中的页面？',function() {
                    $.ajax({
                        url:'{:url('page/delete',['id'=>'__id__'])}'.replace('__id__',ids.join(',')),
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