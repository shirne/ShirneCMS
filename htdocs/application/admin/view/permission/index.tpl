<extend name="public:base" />

<block name="body">

<include file="public/bread" menu="permission_index" section="系统" title="菜单管理" />

<div id="page-wrapper">
    
    <div class="row list-header">
        <div class="col-6">
            <a href="{:url('permission/add')}" class="btn btn-outline-primary">添加菜单</a>
            <a href="{:url('permission/clearcache')}" class="btn btn-outline-dark">清除缓存</a>
        </div>
        <div class="col-6">
        </div>
    </div>
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th width="50">编号</th>
                <th>菜单名</th>
                <th>键值</th>
                <th>链接</th>
                <th width="300">操作</th>
            </tr>
        </thead>
        <tbody>
        <foreach name="model[0]" item="v">
            <tr>
                <td>{$v.id}</td>
                <td>{$v.name}</td>
                <td>{$v.key}</td>
                <td>{$v.url}</td>
                <td>
                    <a class="btn btn-outline-dark btn-sm" href="{:url('permission/add',array('pid'=>$v['id']))}"><i class="ion-plus"></i> 添加</a>
                    <a class="btn btn-outline-dark btn-sm" href="{:url('permission/edit',array('id'=>$v['id']))}"><i class="ion-edit"></i> 编辑</a>
                    <a class="btn btn-outline-dark btn-sm" href="{:url('permission/delete',array('id'=>$v['id']))}" onclick="javascript:return del('您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="ion-trash-a"></i> 删除</a>
                </td>
            </tr>
            <foreach name="model[$v['id']]" item="sv">
                <tr>
                    <td>{$sv.id}</td>
                    <td><span class="fa">┣</span> {$sv.name}</td>
                    <td>{$sv.key}</td>
                    <td>{$sv.url}</td>
                    <td>
                        <a class="btn btn-outline-dark btn-sm" href="{:url('permission/add',array('pid'=>$sv['id']))}"><i class="ion-plus"></i> 添加</a>
                        <a class="btn btn-outline-dark btn-sm" href="{:url('permission/edit',array('id'=>$sv['id']))}"><i class="ion-edit"></i> 编辑</a>
                        <a class="btn btn-outline-dark btn-sm" href="{:url('permission/delete',array('id'=>$sv['id']))}" onclick="javascript:return del('您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="ion-trash-a"></i> 删除</a>
                    </td>
                </tr>
                <foreach name="model[$sv['id']]" item="mv">
                    <tr>
                        <td>{$mv.id}</td>
                        <td><span class="fa">&nbsp;</span><span class="fa">┣</span> {$mv.name}</td>
                        <td>{$mv.key}</td>
                        <td>{$mv.url}</td>
                        <td>
                            <a class="btn btn-outline-dark btn-sm" href="{:url('permission/edit',array('id'=>$mv['id']))}"><i class="ion-edit"></i> 编辑</a>
                            <a class="btn btn-outline-dark btn-sm" href="{:url('permission/delete',array('id'=>$mv['id']))}" onclick="javascript:return del('您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="ion-trash-a"></i> 删除</a>
                        </td>
                    </tr>
                </foreach>
            </foreach>
        </foreach>
        </tbody>
    </table>
    {$page|raw}
</div>

</block>