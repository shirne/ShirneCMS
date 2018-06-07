<extend name="public:base" />

<block name="body">

<include file="public/bread" menu="manager_log" title="" />

<div id="page-wrapper">
    <div class="row list-header">
        <div class="col-md-6">
            <a href="{:url('member/logclear')}" class="btn btn-outline-secondary btn-sm"><i class="ion-md-trash"></i> 清理日志</a>
        </div>
        <div class="col-md-6">
            <form action="{:url('member/index')}" method="post">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" name="key" placeholder="输入用户名或者关键词搜索">
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
        <foreach name="logs" item="v">
            <tr>
                <td>{$v.id}</td>
                <td>{$v.username}</td>
                <td>{$v.action}</td>
                <td><if condition="$v.result EQ 1"><span class="badge badge-success">成功</span><else/><span class="badge badge-danger">失败</span> </if></td>
                <td>{$v.create_time|showdate}</td>
                <td>{$v.ip}</td>
                <td>{$v.remark}</td>
                <td>
                    <a class="btn btn-outline-dark btn-sm" rel="ajax" href="{:url('manager/logview',array('id'=>$v['id']))}"><i class="ion-md-file-text"></i> 查看</a>
                </td>
            </tr>
        </foreach>
        </tbody>
    </table>
    {$page|raw}
</div>

</block>