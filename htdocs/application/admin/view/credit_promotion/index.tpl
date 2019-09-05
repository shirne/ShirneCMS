<extend name="public:base" />

<block name="body">

    <include file="public/bread" menu="credit_promotion_index" title="积分策略" />

    <div id="page-wrapper">

        <div class="row list-header">
            <div class="col-6">
                <a href="{:url('creditPromotion/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加策略</a>
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
                <th>平分比例</th>
                <th>赠送总积分</th>
                <th width="200">操作</th>
            </tr>
            </thead>
            <tbody>
            <foreach name="lists" item="v">
                <tr>
                    <td>{$v.id}</td>
                    <td>{$v.name}<if condition="$v['is_default']"><span class="badge badge-info">默认</span> </if></td>
                    <td>{$v.sort}</td>
                    <td>{$v['share_percent']}%</td>
                    <td>{$v.send_percent}%</td>
                    <td>
                        <a class="btn btn-outline-dark btn-sm" href="{:url('creditPromotion/update',array('id'=>$v['id']))}"><i class="ion-md-create"></i> 编辑</a>
                        <a class="btn btn-outline-dark btn-sm" href="{:url('creditPromotion/delete',array('id'=>$v['id']))}" onclick="javascript:return del(this,'您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="ion-md-trash"></i> 删除</a>
                    </td>
                </tr>
            </foreach>
            </tbody>
        </table>
    </div>
</block>