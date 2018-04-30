<extend name="Public:Base"/>

<block name="body">

<include file="Public/bread" menu="manager_index" section="系统" title="管理员" />

<div id="page-wrapper">
	<div class="page-header">添加管理员</div>
	<div id="page-content">
<form action="{:url('manager/add')}" method="post">
	<div class="form-group">
		<label>用户名</label>
		<input class="form-control" type="text" name="username" placeholder="username">
	</div>
	<div class="form-group">
		<label>真实姓名</label>
		<input class="form-control" type="text" name="realname"  />
	</div>
	<div class="form-group">
		<label>手机号码</label>
		<input class="form-control" type="text" name="mobile" placeholder="mobile" />
	</div>
	<div class="form-group">
		<label>邮箱</label>
		<input class="form-control" type="text" name="email" placeholder="Email">
	</div>
	<div class="form-group">
		<label>密码</label>
		<input class="form-control" type="password" name="password" placeholder="password">
	</div>
	<div class="form-group">
		<label>确认密码</label>
		<input class="form-control" type="password" name="repassword" placeholder="repassword">
	</div>
	<div class="form-group">
        <label>用户类型</label>
        <label class="radio-inline">
          <input type="radio" name="type" id="type" value="1">超级管理员
        </label>
        <label class="radio-inline">
          <input type="radio" name="type" id="type" value="2" checked="checked">管理员
        </label>
    </div>
	<div class="form-group">
        <label>用户状态</label>
        <label class="radio-inline">
          <input type="radio" name="status" id="status" value="0">禁止登陆
        </label>
        <label class="radio-inline">
          <input type="radio" name="status" id="status" value="1"  checked="checked">正常
        </label>
    </div>
	<div class="form-group">
		<button class="btn btn-primary" type="submit" >添加</button>
	</div>


</form>
		</div>
</div>

</block>
