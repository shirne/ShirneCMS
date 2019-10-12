<extend name="public:base" />

<block name="body">

    <include file="public/bread" menu="wechat_index" title="模板消息" />

    <div id="page-wrapper">

        <div class="row list-header">
            <div class="col-6">
                <a href="{:url('wechat/index')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-arrow-back"></i> 返回列表</a>
                <a href="{:url('sync')}" class="btn btn-outline-primary btn-sm btnsync"><i class="ion-md-sync"></i> 同步模板</a>
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
                            <a class="btn btn-outline-danger delbtn" href="{:url('del',['id'=>$tpls[$key]['id']])}" title="删除模板"><i class="ion-md-trash"></i></a>
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
                            <a class="btn btn-outline-primary addbtn" href="{:url('add',['id'=>$v['title_id']])}" title="添加模板"><i class="ion-md-add"></i></a>
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
<block name="script">
    <script>
        jQuery(function ($) {
            $('.btnsync').click(function (e) {
                e.preventDefault()
                var dlg=dialog.loading('正在同步...')
                var url=$(this).attr('href')
                $.ajax({
                    url:url,
                    dataType:'JSON',
                    success:function (json) {
                        var func=arguments.callee
                        dlg.close();
                        if(json.url && json.data.next){
                            dialog.loading(json.msg)
                            $.ajax({
                                url: json.url,
                                dataType: 'JSON',
                                success: func
                            });
                        }else {
                            dialog.alert(json.msg, function () {
                                if (json.code == 1) {
                                    location.reload()
                                }
                            })
                        }
                    }
                })
            })
            $('.addbtn').click(function (e) {
                e.preventDefault()
                var url=$(this).attr('href')
                $.ajax({
                    url:url,
                    dataType:'JSON',
                    success:function (json) {
                        dialog.alert(json.msg,function () {
                            if(json.code==1){
                                location.reload()
                            }
                        })
                    }
                })
            })

            $('.delbtn').click(function (e) {
                e.preventDefault()
                var url=$(this).attr('href')
                $.ajax({
                    url:url,
                    dataType:'JSON',
                    success:function (json) {
                        dialog.alert(json.msg,function () {
                            if(json.code==1){
                                location.reload()
                            }
                        })
                    }
                })
            })
        })
    </script>
</block>