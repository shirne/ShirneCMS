<extend name="Public:Base" />

<block name="body">

    <include file="Public/bread" menu="memberlevel_index" section="会员" title="会员等级" />

    <div id="page-wrapper">

        <div class="row">
            <div class="col-xs-6">
                <a href="{:U('MemberLevel/add')}" class="btn btn-success">添加等级</a>
            </div>
            <div class="col-xs-6">
            </div>
        </div>
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <th width="50">编号</th>
                <th>名称</th>
                <th>排序</th>
                <th>购买价格</th>
                <th width="150">操作</th>
            </tr>
            </thead>
            <tbody>
            <foreach name="model" item="v">
                <tr>
                    <td>{$v.level_id}</td>
                    <td>{$v.level_name}[{$v.short_name}]<if condition="$v['is_default']"><span class="label label-info">默认</span> </if></td>
                    <td>{$v.sort}</td>
                    <td>{$v.level_price}</td>
                    <td>
                        <a class="btn btn-default btn-sm" href="{:U('memberLevel/update',array('id'=>$v['level_id']))}"><i class="fa fa-edit"></i> 编辑</a>
                        <a class="btn btn-default btn-sm" href="{:U('memberLevel/delete',array('id'=>$v['level_id']))}" style="color:red;" onclick="javascript:return del('您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="fa fa-trash"></i> 删除</a>
                    </td>
                </tr>
            </foreach>
            </tbody>
        </table>
        {$page}
    </div>
</block>