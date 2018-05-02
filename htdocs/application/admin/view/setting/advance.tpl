<extend name="public:base" />

<block name="body">

<include file="public/bread" menu="setting_index" section="系统" title="配置管理" />

<div id="page-wrapper">

    <div class="row list-header">
        <div class="col-md-6">
            <a href="{:url('setting/index')}" class="btn btn-outline-primary btn-sm">普通模式</a>&nbsp;&nbsp;
            <a href="{:url('setting/add')}" class="btn btn-outline-secondary btn-sm">添加配置</a>
        </div>
        <div class="col-md-6">
            <form action="{:url('setting/advance')}" method="post">
                <div class="form-group input-group input-group-sm">
                    <input type="text" class="form-control" name="key" value="{$keyword}" placeholder="输入字段名或者描述关键词搜索">
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
                <th>标题</th>
                <th>字段</th>
                <th>分组</th>
                <th>类型</th>
                <th>说明</th>
                <th width="200">操作</th>
            </tr>
        </thead>
        <tbody>
        <foreach name="model" item="v">
            <tr>
                <td>{$v.id}</td>
                <td>{$v.title}</td>
                <td>{$v.key}</td>
                <td>{$v.group|settingGroups}</td>
                <td>{$v.type|settingTypes}</td>
                <td>{$v.description}</td> 
                <td>
                    <a class="btn btn-outline-dark btn-sm" href="{:url('setting/edit',array('id'=>$v['id']))}"><i class="ion-edit"></i> 编辑</a>
                    <a class="btn btn-outline-dark btn-sm" href="{:url('setting/delete',array('id'=>$v['id']))}" onclick="javascript:return del('您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="ion-trash-a"></i> 删除</a>
                </td>
            </tr>
        </foreach>
        </tbody>
    </table>
    {$page|raw}
</div>

</block>