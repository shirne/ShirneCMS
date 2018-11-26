<extend name="public:base" />

<block name="body">

    <include file="public/bread" menu="wechat_index" title="消息回复" />

    <div id="page-wrapper">

        <div class="row list-header">
            <div class="col-6">
                <a href="{:url('wechat/index')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-arrow-back"></i> 返回列表</a>
                <a href="{:url('wechat/replyadd',array('wid'=>$wid))}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加回复</a>
            </div>
            <div class="col-6">
                <form action="{:url('wechat/reply',['wid'=>$wid])}" method="post">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" name="key" placeholder="输入标题或者地址关键词搜索">
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
                <th>标题</th>
                <th>类型</th>
                <th>回复类型</th>
                <th>关键字</th>
                <th>优先级</th>
                <th width="200">操作</th>
            </tr>
            </thead>
            <tbody>
            <foreach name="lists" item="v">
                <tr>
                    <td>{$v.id}</td>
                    <td>{$v.title}</td>
                    <td>{$v.type}</td>
                    <td>{$v.reply_type}</td>
                    <td>{$v.keyword}</td>
                    <td>{$v.sort}</td>
                    <td>
                        <a class="btn btn-outline-dark btn-sm" href="{:url('wechat/replyedit',array('id'=>$v['id'],'wid'=>$wid))}"><i class="ion-md-create"></i> 编辑</a>
                        <a class="btn btn-outline-dark btn-sm" href="{:url('wechat/replydelete',array('id'=>$v['id'],'wid'=>$wid))}" onclick="javascript:return del(this,'您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="ion-md-trash"></i> 删除</a>
                    </td>
                </tr>
            </foreach>
            </tbody>
        </table>
        {$page|raw}
    </div>
</block>