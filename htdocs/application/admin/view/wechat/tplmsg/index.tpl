<extend name="public:base" />

<block name="body">

    <include file="public/bread" menu="wechat_index" title="模板消息" />

    <div id="page-wrapper">

        <div class="row list-header">
            <div class="col-6">
                <a href="{:url('wechat/index')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-arrow-back"></i> 返回列表</a>

            </div>
            <div class="col-6">

            </div>
        </div>
        <form action="" method="post">
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <th width="50">#</th>
                <th width="160">标题</th>
                <th width="160">类型</th>
                <th>模板ID</th>
                <th>关键字</th>
                <th width="160">&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <php>$empty=list_empty(6);</php>
            <volist name="msgs" id="v" empty="$empty">
                <if condition="isset($tpls[$key])">
                    <tr>
                        <td>{$tpls[$key]['id']}</td>
                        <td><input type="text" class="form-control" name="tpls[{$key}][title]" readonly value="{$tpls[$key]['title']}" /></td>
                        <td>{$key}</td>
                        <td><input type="text" class="form-control" name="tpls[{$key}][template_id]" value="{$tpls[$key]['template_id']}"/></td>
                        <td><input type="text" class="form-control" name="tpls[{$key}][keywords]" readonly value="{$tpls[$key]['keywords']}" /></td>
                        <td class="operations">
                            -
                        </td>
                    </tr>
                    <else/>
                    <tr>
                        <td>-</td>
                        <td><input type="text" class="form-control" name="tpls[{$key}][title]" readonly value="{$v['title']}" /></td>
                        <td>{$key}</td>
                        <td><input type="text" class="form-control" name="tpls[{$key}][template_id]" /></td>
                        <td><input type="text" class="form-control" name="tpls[{$key}][keywords]" readonly value="{$v['keywords']}" /></td>
                        <td class="operations">
                            -
                        </td>
                    </tr>
                </if>
            </volist>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="6">
                    <input type="submit" name="submit" value="保存设置" class="btn btn-primary">
                </td>
            </tr>
            </tfoot>
        </table>
        </form>
    </div>
</block>