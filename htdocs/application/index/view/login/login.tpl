<extend name="Public:Base" />

<block name="body">
	<div class="main">

		<div class="container loginbox">
			<fieldset>
				<legend>会员登录</legend>
				<div class="panel-body">

					<form class="form-horizontal" role="form" method="post" action="{:url('Login/login')}">
						<div class="form-group">
							<label for="userName" class="col-sm-2 control-label">用户名：</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name="username">
							</div>
						</div>
						<div class="form-group">
							<label for="Password" class="col-sm-2 control-label">密码：</label>
							<div class="col-sm-10">
								<input type="password" class="form-control" name="password">
							</div>
						</div>
						<div class="form-group">
							<label for="emailPassword" class="col-sm-2 control-label">验证码：</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" name="verify">
							</div>
							<div class="col-sm-5">
								<a href="javascript:" class="verifybox"><img src="{:url('Login/verify')}" alt=""></a>
							</div>
						</div>
						<div class="form-group submitline">
							<div class="col-sm-offset-2 col-sm-10">
								<button type="submit" class="btn btn-default create">登陆</button>&nbsp;没有账号?<a href="{:url('Login/register')}">立即注册</a>
							</div>
						</div>
					</form>

				</div>
			</fieldset>
		</div>
	</div>
	<script type="text/javascript">
		jQuery(function($){
			var verifyurl='{:url('Login/verify')}';
			if(verifyurl.indexOf('?')>0)verifyurl+='&';
			else verifyurl+= '?';
			$('.verifybox').click(function() {
				$(this).find('img').attr('src',verifyurl+'_t='+new Date().getTime());
			});
		});
	</script>

</block>