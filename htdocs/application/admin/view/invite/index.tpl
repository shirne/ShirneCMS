<extend name="public:base" />

<block name="body">

<include file="public/bread" menu="invite_index" section="会员" title="邀请码" />

<div id="page-wrapper">
    <div class="row list-header">
        <div class="col-6">
            <a href="{:url('Invite/add')}" class="btn btn-outline-primary">生成邀请码</a>
        </div>
        <div class="col-6">
            <form action="{:url('member/index')}" method="post">
                <div class="form-group input-group">
                    <input type="text" class="form-control" name="key" placeholder="输入用户id或邀请码搜索">
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
                <th>邀请码</th>
                <th>所属会员</th>
                <th>创建日期</th>
                <th>使用会员</th>
                <th>使用日期</th>
                <th>有效期</th>
                <th>状态</th>
                <th width="200">操作</th>
            </tr>
        </thead>
        <tbody>
        <foreach name="lists" item="v">
            <tr>
                <td>{$v.id}</td>
                <td>{$v.code}</td>
                <td>{$v.member_id}</td>
                <td>{$v.create_time|showdate}</td>
                <td>{$v.member_use}</td>
                <td>{$v.use_at|showdate}</td>
                <td>{$v.valid_at|showdate}</td>
                <td><if condition="$v.status eq 1"><span class="badge badge-danger">锁定</span><else/><span class="badge badge-secondary">正常</span></if></td>
                <td>
                    <a class="btn btn-outline-dark btn-sm" href="{:url('Invite/update',array('id'=>$v['id']))}"><i class="ion-edit"></i> 转赠</a>
                    <if condition="$v.status eq 0">
                        <a class="btn btn-outline-dark btn-sm" href="{:url('Invite/lock',array('id'=>$v['id']))}" onclick="javascript:return del('锁定后将不能使用此激活码注册!\n\n请确认!!!');"><i class="ion-close"></i> 锁定</a>
                    <else/>
                        <a class="btn btn-outline-dark btn-sm" href="{:url('Invite/unlock',array('id'=>$v['id']))}" style="color:#50AD1E;"><i class="ion-check"></i> 解锁</a>
                    </if>
                </td>
            </tr>
        </foreach>
        </tbody>
    </table>
    {$page|raw}
</div>

</block>