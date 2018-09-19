<extend name="public:base" />

<block name="body">

    <include file="public/bread" menu="member_level_index" title="会员组列表" />

    <div id="page-wrapper">

        <div class="row list-header">
            <div class="col-6">
                <a href="{:url('MemberLevel/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加等级</a>
            </div>
            <div class="col-6">
            </div>
        </div>
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <th width="50">编号</th>
                <th>名称</th>
                <th>默认</th>
                <th>区域分红</th>
                <th>大区分红</th>
                <th>推荐奖励</th>
                <th>推荐分红</th>
            </tr>
            </thead>
            <tbody>
            <foreach name="lists" item="v">
                <tr>
                    <td>{$v.id}</td>
                    <td><input type="text" name="agents[{$v.id}][name]" value="{$v.name}"></td>
                    <td><input type="radio" name="is_default" value="{$v.id}" {$v['is_default']?'checked':''}></td>
                    <td><input type="text" name="agents[{$v.id}][area_sale_award]" value="{$v.area_sale_award}"></td>
                    <td><input type="text" name="agents[{$v.id}][sibling_sale_award]" value="{$v.sibling_sale_award}"></td>
                    <td><input type="text" name="agents[{$v.id}][recom_award]" value="{$v.recom_award}"></td>
                    <td><input type="text" name="agents[{$v.id}][resom_sale_award]" value="{$v.resom_sale_award}"></td>
                </tr>
            </foreach>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7">
                        <input type="submit" class="btn btn-primary" value="保存"/>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</block>