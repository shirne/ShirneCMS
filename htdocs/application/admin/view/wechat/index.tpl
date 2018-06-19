<extend name="public:base" />

<block name="body">

    <include file="public/bread" menu="wechat_index" title="公众号列表" />

    <div id="page-wrapper">

        <div class="row list-header">
            <div class="col-6">
                <a href="{:url('wechat/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加公众号</a>
            </div>
            <div class="col-6">
                <form action="{:url('wechat/index')}" method="post">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" name="key" placeholder="输入名称或者关键词搜索">
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
                <th>类型</th>
                <th>名称</th>
                <th>appid</th>
                <th>功能</th>
                <th width="200">操作</th>
            </tr>
            </thead>
            <tbody>
            <foreach name="lists" item="v">
                <tr>
                    <td>{$v.id}</td>
                    <td>{$v.title}</td>
                    <td>{$v.url}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a class="btn btn-outline-dark qrcode-btn" href="javascript:" data-qrcode="{$model.qrcode}"><i class="ion-md-expand"></i> 二维码</a>
                            <a class="btn btn-outline-dark" href="{:url('wechat/menu',array('id'=>$v['id']))}"><i class="ion-md-reorder"></i> 菜单</a>
                        </div>
                    </td>
                    <td>
                        <a class="btn btn-outline-dark btn-sm" href="{:url('wechat/edit',array('id'=>$v['id']))}"><i class="ion-md-create"></i> 编辑</a>
                        <a class="btn btn-outline-dark btn-sm" href="{:url('wechat/delete',array('id'=>$v['id']))}" onclick="javascript:return del(this,'您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="ion-md-trash"></i> 删除</a>
                    </td>
                </tr>
            </foreach>
            </tbody>
        </table>
        {$page|raw}
    </div>
</block>
<block name="script">
    <script type="text/javascript">
        jQuery(function($){
            $('.qrcode-btn').click(function() {
                var qrcode=$(this).data('qrcode');
                if(qrcode){
                    dialog.alert('<figure class="figure">\n' +
                        ' <img src="'+qrcode+'" class="figure-img img-fluid rounded" alt="image">\n' +
                        ' <figcaption class="figure-caption text-center">扫描二维码关注公众号</figcaption>\n' +
                        '</figure>');
                }else{
                    toastr.info('没有上传二维码');
                }
            })
        })
    </script>
</block>