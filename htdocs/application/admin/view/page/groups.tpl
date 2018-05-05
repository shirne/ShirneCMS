<extend name="public:base" />

<block name="body">

    <include file="public/bread" menu="page_index" title="页面分组" />

    <div id="page-wrapper">

        <div class="row list-header">
            <div class="col-6">
                <a href="{:url('page/index')}" class="btn btn-outline-secondary btn-sm">页面管理</a>
                <a href="{:url('page/groupedit')}" class="btn btn-outline-primary btn-sm">添加分组</a>
            </div>
            <div class="col-6">&nbsp;
            </div>
        </div>
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <th width="50">编号</th>
                <th>分组</th>
                <th>组名</th>
                <th>排序</th>
                <th width="200">操作</th>
            </tr>
            </thead>
            <tbody>
            <foreach name="lists" item="v">
                <tr>
                    <td>{$v.id}</td>
                    <td>{$v.group}</td>
                    <td>{$v.group_name}</td>
                    <td>{$v.sort}</td>
                    <td>
                        <a class="btn btn-outline-dark btn-sm" href="{:url('page/groupedit',array('id'=>$v['id']))}" ><i class="ion-edit"></i> 编辑</a>
                        <a class="btn btn-outline-dark btn-sm" href="{:url('page/groupdelete',array('id'=>$v['id']))}" onclick="javascript:return del('您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="ion-trash-a"></i> 删除</a>
                    </td>
                </tr>
            </foreach>
            </tbody>
        </table>
        {$page|raw}
    </div>

</block>