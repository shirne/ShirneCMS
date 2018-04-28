<extend name="Public:Base" />

<block name="body">

<include file="Public/bread" menu="feedback_index" section="其它" title="留言管理" />

<div id="page-wrapper">
    
    <div class="row">
        <div class="col-xs-6">

        </div>
        <div class="col-xs-6">
            <form action="{:U('links/index')}" method="post">
                <div class="form-group input-group">
                    <input type="text" class="form-control" name="key" placeholder="输入邮箱或者关键词搜索">
                    <span class="input-group-btn">
                      <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
                    </span>
                </div>
            </form>
        </div>
    </div>
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th width="50">编号</th>
                <th>会员</th>
                <th>类型</th>
                <th>IP</th>
                <th>日期</th>
                <th>状态</th>
                <th width="150">操作</th>
            </tr>
        </thead>
        <tbody>
        <foreach name="lists" item="v">
            <tr>
                <td>{$v.id}</td>
                <td>{$v.username}</td>
                <td>{$v.type}</td>
                <td>{$v.ip}</td>
                <td>{$v.create_at|showdate}</td>
                <td>{$v.status|f_status}</td>
                <td>
                    <a class="btn btn-default btn-sm" href="{:U('feedback/reply',array('id'=>$v['id']))}"><i class="fa fa-reply"></i> 回复</a>
                    <a class="btn btn-default btn-sm" href="{:U('feedback/delete',array('id'=>$v['id']))}" style="color:red;" onclick="javascript:return del('您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="fa fa-trash"></i> 删除</a>
                </td>
            </tr>
        </foreach>
        </tbody>
    </table>
    {$page}
</div>

</block>