<extend name="public:base" />

<block name="body">
	<div class="main">

		<div class="container loginbox">
			<div class="card">
				<div class="card-header">会员登录</div>
				<div class="card-body">

					<form class="form-horizontal" role="form" method="post" action="{:url('Login/login')}">
						<div class="form-group form-row">
							<label for="userName" class="col-md-2 control-label">用户名：</label>
							<div class="col-md-5">
								<input type="text" class="form-control" name="username">
							</div>
						</div>
						<div class="form-group form-row">
							<label for="Password" class="col-md-2 control-label">密码：</label>
							<div class="col-md-5">
								<input type="password" class="form-control" name="password">
							</div>
						</div>
						<div class="form-group form-row">
							<label for="emailPassword" class="col-md-2 control-label">验证码：</label>
							<div class="col-md-3">
								<input type="text" class="form-control" name="verify">
							</div>
							<div class="col-md-2">
								<a href="javascript:" class="verifybox"><img src="{:url('Login/verify')}" alt=""></a>
							</div>
						</div>
						<div class="form-group form-row submitline">
							<div class="offset-md-2 col-md-10">
								<button type="submit" class="btn btn-primary create">登陆</button>&nbsp;没有账号?<a href="{:url('Login/register')}">立即注册</a>
							</div>
						</div>
					</form>

				</div>
			</div>
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