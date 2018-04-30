<extend name="Public:Base" />

<block name="body">

<include file="Public/bread" menu="paytype_index" section="系统" title="付款方式" />

<div id="page-wrapper">
    
    <div class="row">
        <div class="col col-xs-6">
            <a href="{:url('Paytype/add')}" class="btn btn-success">添加付款方式</a>
        </div>
        <div class="col col-xs-6">
            <form action="{:url('Paytype/index')}" method="post">
                <div class="form-group input-group">
                    <input type="text" class="form-control" name="key" placeholder="输入名称搜索">
                    <div class="input-group-append">
                      <button class="btn btn-outline-secondary" type="submit"><i class="fa fa-search"></i></button>
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
                <th>类型</th>
                <th>状态</th>
                <th width="150">操作</th>
            </tr>
        </thead>
        <tbody>
        <foreach name="lists" item="v">
            <tr>
                <td>{$v.id}</td>
                <td>{$v.title}</td>
                <td>{$v.type|payTypes}</td>
                <td>{$v['status']?'显示':'隐藏'}</td>
                <td>
                    <a class="btn btn-default btn-sm" href="{:url('Paytype/update',array('id'=>$v['id']))}"><i class="fa fa-edit"></i> 编辑</a>
                    <a class="btn btn-default btn-sm" href="{:url('Paytype/delete',array('id'=>$v['id']))}" style="color:red;" onclick="javascript:return del('您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="fa fa-trash"></i> 删除</a>
                </td>
            </tr>
        </foreach>
        </tbody>
    </table>
    {$page}
</div>

</block>