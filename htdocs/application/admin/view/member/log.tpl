<extend name="public:base" />

<block name="body">
<include file="public/bread" menu="member_log" section="会员" title="操作日志" />

<div id="page-wrapper">
    <div class="row list-header">
        <div class="col-md-6">
            <a href="{:url('member/logclear')}" class="btn btn-outline-secondary">清理日志</a>
        </div>
        <div class="col-md-6">
            <form action="{:url('member/index')}" method="post">
                <div class="form-group input-group">
                    <input type="text" class="form-control" name="key" placeholder="输入用户名或者关键词搜索">
                    <div class="input-group-append">
                      <button class="btn btn-outline-secondary" type="submit"><i class="ion-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th width="50">编号</th>
                <th>用户名</th>
                <th>操作</th>
                <th>状态</th>
                <th>时间</th>
                <th>IP</th>
                <th>备注</th>
                <th width="80">操作</th>
            </tr>
        </thead>
        <tbody>
        <foreach name="lists" item="v">
            <tr>
                <td>{$v.id}</td>
                <td>{$v.username}</td>
                <td>{$v.action}</td>
                <td>{$v.result}</td>
                <td>{$v.create_time|showdate}</td>
                <td>{$v.ip}</td>
                <td>{$v.remark}</td>
                <td>
                    <a class="btn btn-outline-dark btn-sm" rel="ajax" href="{:url('member/logview',array('id'=>$v['id']))}"><i class="ion-file-text"></i> 查看</a>
                </td>
            </tr>
        </foreach>
        </tbody>
    </table>
    {$page|raw}
</div>

</block>