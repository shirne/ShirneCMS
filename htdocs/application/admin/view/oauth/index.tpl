<extend name="public:base" />

<block name="body">

<include file="public/bread" menu="setting_index" title="授权登录接口" />

<div id="page-wrapper">
    
    <div class="row list-header">
        <div class="col-6">
            <a href="{:url('oauth/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加接口</a>
        </div>
        <div class="col-6">
            &nbsp;
        </div>
    </div>
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th width="50">编号</th>
                <th>名称</th>
                <th>类型</th>
                <th>appid</th>
                <th width="200">操作</th>
            </tr>
        </thead>
        <tbody>
        <foreach name="lists" item="v">
            <tr>
                <td>{$v.id}</td>
                <td>{$types[$v['type']]}</td>
                <td>{$v.title}</td>
                <td>{$v.appid}</td>
                <td>
                    <a class="btn btn-outline-dark btn-sm" href="{:url('oauth/edit',array('id'=>$v['id']))}"><i class="ion-md-create"></i> 编辑</a>
                    <a class="btn btn-outline-dark btn-sm" href="{:url('oauth/delete',array('id'=>$v['id']))}" onclick="javascript:return del('您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="ion-md-trash"></i> 删除</a>
                </td>
            </tr>
        </foreach>
        </tbody>
    </table>
    {$page|raw}
</div>
</block>