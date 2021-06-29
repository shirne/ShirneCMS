{extend name="public:base" /}

{block name="body"}

    {include  file="public/bread" menu="member_level_index" title="会员组列表"  /}

    <div id="page-wrapper">

        <div class="row list-header">
            <div class="col-6">
                <a href="{:url('MemberLevel/index')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-arrow-back"></i> 返回</a>
            </div>
            <div class="col-6">
            </div>
        </div>
        <form name="agents_form" method="post">
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <th width="50">编号</th>
                <th>名称</th>
                <th width="80">简称</th>
                <th>样式</th>
                <th>默认</th>
                <th>条件</th>
                <th>奖励比例</th>
                <th>全国分红</th>
            </tr>
            </thead>
            <tbody>
            {foreach name="lists" item="v"}
                <tr>
                    <td>{$v.id}</td>
                    <td><input type="text" class="form-control" name="agents[{$v.id}][name]" value="{$v.name}"></td>
                    <td><input type="text" class="form-control" name="agents[{$v.id}][short_name]" value="{$v.short_name}"></td>
                    <td>
                        <select name="agents[{$v.id}][style]" class="form-control text-{$v.style}"
                                onchange="$(this).attr('class','form-control text-'+$(this).val())">
                            {foreach name="styles" id="style"}
                                <option value="{$style}" {$v['style']==$style?'selected':''}
                                        class="text-{$style}">██████████
                                </option>
                            {/foreach}
                        </select>
                    </td>
                    <td><input type="radio" name="is_default" value="{$v.id}" {$v['is_default']?'checked':''}></td>
                    <td>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">直推</span>
                            </div>
                            <input type="text" class="form-control" name="agents[{$v.id}][recom_count]" value="{$v.recom_count}">
                            <div class="input-group-middle">
                                <span class="input-group-text">团队</span>
                            </div>
                            <input type="text" class="form-control" name="agents[{$v.id}][team_count]" value="{$v.team_count}">
                        </div>
                    </td>
                    <td>
                        <div class="input-group">
                            <input type="text" class="form-control" name="agents[{$v.id}][sale_award]" value="{$v.sale_award}">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="input-group">
                            <input type="text" class="form-control" name="agents[{$v.id}][global_sale_award]" value="{$v.global_sale_award}">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </td>
                </tr>
            {/foreach}
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7">
                        <input type="submit" class="btn btn-primary" value="保存"/>
                    </td>
                </tr>
            </tfoot>
        </table>
        </form>
    </div>
{/block}