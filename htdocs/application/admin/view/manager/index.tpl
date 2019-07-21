<extend name="public:base" />

<block name="body">
<include file="public/bread" menu="manager_index" title="管理员列表" />

<div id="page-wrapper">
    <div class="row list-header">
        <div class="col-md-6">
            <a href="{:url('manager/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加管理员</a>
        </div>
        <div class="col-md-6">
            <form action="{:url('manager/index')}" method="post">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" name="key" placeholder="输入用户名或者邮箱关键词搜索">
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
                <th>邮箱</th>
                <th>注册时间/修改时间</th>
                <th>上次登陆</th>
                <th>类型</th>
                <th>状态</th>
                <th width="160">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        <php>$empty=list_empty(8);</php>
        <foreach name="lists" item="v" empty="$empty">
            <tr>
                <td>{$v.id}</td>
                <td>{$v.username}</td>
                <td>{$v.email}</td>
                <td>{$v.create_time|showdate}<br />{$v.update_time|showdate}</td>
                <td>{$v.login_ip}<br />{$v.logintime|showdate}</td>
                <td>
                    <if condition="$v.type eq 1"> <span class="label label-success">超级管理员</span>
                    <elseif condition="$v.type eq 2"/><span class="label label-danger">管理员</span>
                    </if>
                </td> 
                <td><if condition="$v.status eq 1">正常<else/><span style="color:red">禁用</span></if></td>
                <td class="operations">
                    <a class="btn btn-outline-primary" title="编辑" href="{:url('manager/update',array('id'=>$v['id']))}"><i class="ion-md-create"></i> </a>
                <if condition="$v.type neq 1">
                    <a class="btn btn-outline-primary" title="权限" href="{:url('manager/permision',array('id'=>$v['id']))}"><i class="ion-md-key"></i> </a>
                </if>
                <if condition="$v.status eq 1">	
                    <a class="btn btn-outline-danger link-confirm" title="禁用" data-confirm="禁用后用户将不能登陆后台!\n请确认!!!" href="{:url('manager/status',array('id'=>$v['id'],'status'=>0))}" ><i class="ion-md-close"></i> </a>
            	<else/>
                    <a class="btn btn-outline-success" title="启用" href="{:url('manager/status',array('id'=>$v['id'],'status'=>1))}" ><i class="ion-md-checkmark-circle"></i> </a>
            	</if>
                </td>
            </tr>
        </foreach>
        </tbody>
    </table>
</div>

</block>