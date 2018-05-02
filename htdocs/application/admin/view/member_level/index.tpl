<extend name="public:base" />

<block name="body">

    <include file="public/bread" menu="member_level_index" section="会员" title="会员等级" />

    <div id="page-wrapper">

        <div class="row list-header">
            <div class="col-6">
                <a href="{:url('MemberLevel/add')}" class="btn btn-outline-primary">添加等级</a>
            </div>
            <div class="col-6">
            </div>
        </div>
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <th width="50">编号</th>
                <th>名称</th>
                <th>排序</th>
                <th>购买价格</th>
                <th width="200">操作</th>
            </tr>
            </thead>
            <tbody>
            <foreach name="lists" item="v">
                <tr>
                    <td>{$v.level_id}</td>
                    <td>{$v.level_name}[{$v.short_name}]<if condition="$v['is_default']"><span class="label label-info">默认</span> </if></td>
                    <td>{$v.sort}</td>
                    <td>{$v.level_price}</td>
                    <td>
                        <a class="btn btn-outline-dark btn-sm" href="{:url('memberLevel/update',array('id'=>$v['level_id']))}"><i class="ion-edit"></i> 编辑</a>
                        <a class="btn btn-outline-dark btn-sm" href="{:url('memberLevel/delete',array('id'=>$v['level_id']))}" onclick="javascript:return del('您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="ion-trash-a"></i> 删除</a>
                    </td>
                </tr>
            </foreach>
            </tbody>
        </table>
    </div>
</block>