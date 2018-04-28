<extend name="Public:Base" />

<block name="body">

<include file="Public/bread" menu="Board" section="主面板" title="个人资料" />

<div id="page-wrapper">
<form action="{:U('index/profile')}" method="post">
	<div class="form-group">
		<label>用户名</label>
		<input class="form-control" type="text" name="username" value="{$model.username}" />
	</div>
	<div class="form-group">
		<label>真实姓名</label>
		<input class="form-control" type="text" name="realname" value="{$model.realname}" />
	</div>
	<div class="form-group">
		<label>头像</label>
		<input class="form-control" type="text" name="avatar" value="{$model.avatar}" />
	</div>
	<div class="form-group">
		<label>邮箱</label>
		<input class="form-control" type="text" name="email" value="{$model.email}" />
	</div>
	<div class="form-group">
		<label>新密码</label>
		<input class="form-control" type="password" name="newpassword" placeholder="不修改密码请留空" />
	</div>
	<div class="form-group">
		<label>当前密码</label>
		<input class="form-control" type="password" name="password" placeholder="填写密码才可以保存" />
	</div>
	<div class="form-group">
		<input type="hidden" name="id" value="{$model.id}">
		<button class="btn btn-primary" type="submit" >更新</button>
	</div>


</form>
</div>
<script type="text/javascript">

</script>
</block>