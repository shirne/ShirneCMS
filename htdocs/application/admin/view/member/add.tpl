<extend name="public:base" />

<block name="body">

<include file="public/bread" menu="member_index" section="会员" title="会员管理" />

<div id="page-wrapper">
	<div class="page-header">添加会员</div>
	<div id="page-content">
<form action="{:url('member/add')}" method="post">
	<div class="form-group">
		<label>用户名</label>
		<input class="form-control" type="text" name="username" placeholder="username">
	</div>
	<div class="form-group">
		<label>真实姓名</label>
		<input class="form-control" type="text" name="realname" />
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
          <input type="radio" name="type" id="type" value="1" checked="checked">普通会员
        </label>
        <label class="radio-inline">
          <input type="radio" name="type" id="type" value="2">VIP
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
