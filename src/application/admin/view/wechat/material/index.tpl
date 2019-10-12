<extend name="public:base" />

<block name="body">

    <include file="public/bread" menu="wechat_index" title="素材管理" />

    <div id="page-wrapper">

        <div class="row list-header">
            <div class="col-6">
                <a href="{:url('wechat/index')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-arrow-back"></i> 返回列表</a>
                <a href="{:url('wechat.material/sync',array('type'=>'__TYPE__'))}" class="btn btn-outline-primary btn-sm btn-sync"><i class="ion-md-sync"></i> 同步素材</a>
                <a href="javascript:" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加素材</a>
            </div>
            <div class="col-6">
                <form action="{:url('wechat.material/index',['wid'=>$wid])}" method="post">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" name="key" placeholder="输入标题或者名称关键词搜索">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit"><i class="ion-md-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <th width="50">编号</th>
                <th>素材标题</th>
                <th>类型</th>
                <th>更新日期</th>
                <th>关键字</th>
                <th width="160">&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <php>$empty=list_empty(6);</php>
            <volist name="lists" id="v" empty="$empty">
                <tr>
                    <td>{$v.id}</td>
                    <td>{$v.title|default='无标题'}<br /><span class="text-muted" style="font-size: 12px;">{$v.media_id}</span></td>
                    <td>{$v.type}</td>
                    <td>{$v.update_time|showdate}</td>
                    <td>{$v.keyword}</td>
                    <td class="operations">
                        <a class="btn btn-outline-primary link-confirm" title="删除" data-confirm="您真的确定要删除吗？\n删除后将不能恢复!" href="{:url('wechat.material/delete',array('media_id'=>$v['media_id'],'wid'=>$wid))}" ><i class="ion-md-trash"></i> </a>
                    </td>
                </tr>
            </volist>
            </tbody>
        </table>
        {$page|raw}
    </div>
</block>
<block name="script">
    <script type="text/javascript">
        jQuery(function ($) {
            $('.btn-sync').click(function (e) {
                e.stopPropagation();
                e.preventDefault();
                var self=$(this);
                if(self.data('syncing'))return;
                dialog.action(['图片（image）','视频（video）','语音 （voice）','图文（news）'],function (idx) {
                    self.data('syncing',1);
                    self.addClass('disabled');
                    var loading = dialog.loading('正在同步...',100)
                    var url=self.attr('href');
                    $.ajax({
                        url:url.replace('__TYPE__',['image','video','voice','news'][idx]),
                        dataType:'JSON',
                        type:'GET',
                        success:function (json) {
                            loading.close()
                            if(json.msg.indexOf('成功')>-1) {
                                self.data('syncing', 0);
                                self.removeClass('disabled');

                                if (json.code == 1) {
                                    dialog.success(json.msg);
                                    setTimeout(function () {
                                        location.reload()
                                    }, 600)
                                } else {
                                    dialog.error(json.msg)
                                }
                            }else{
                                var func=arguments.callee
                                loading = dialog.loading(json.msg,100)
                                $.ajax({
                                    url:json.url,
                                    dataType:'JSON',
                                    type:'GET',
                                    success:func
                                })
                            }
                        }
                    })
                })

            })
        })
    </script>
</block>