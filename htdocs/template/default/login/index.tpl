<extend name="public:base" />
<block name="body">
	<div class="main">
		<div class="container loginbox">
			<div class="row justify-content-center">
				<div class="col-10 col-md-5">
			<div class="card">
				<div class="card-header">会员登录</div>
				<div class="card-body">

					<form class="form-horizontal" role="form" method="post" action="{:url('index/login/index')}">
						<if condition="!empty($wechatUser)">
							<div class="form-group">
								<div class="row">
								<div class="col" style="max-width: 65px;">
									<img src="{$wechatUser['avatar']}" style="width: 50px;display: block;border-radius: 1000px;">
								</div>
								<div class="col">
									<p style="margin-bottom:0.5rem;">{$wechatUser['nickname']}</p>
									<p style="font-size: 13px;color: #888888;">登录后将与微信账号绑定</p>
								</div>
								</div>
							</div>
						</if>
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">用户名</span>
								</div>
								<input type="text" class="form-control" name="username">
							</div>
						</div>
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">密&emsp;码</span>
								</div>
								<input type="password" class="form-control" name="password">
							</div>
						</div>
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">验证码</span>
								</div>
								<input type="text" class="form-control" name="verify">
								<div class="input-group-append">
									<a href="javascript:" class="input-group-text verifybox" style="padding:0;"><img src="{:url('index/login/verify')}" alt=""></a>
								</div>
							</div>
						</div>
						<div class="form-group submitline">
							<button type="submit" class="btn btn-primary btn-block create">登陆</button>
						</div>
						<div class="form-group">
							<div class="text-center">
							没有账号?<a href="{:url('index/login/register')}">立即注册</a>
							</div>
						</div>
					</form>

				</div>
			</div>
				</div>
			</div>
		</div>
	</div>
</block>
<block name="script">
	<script type="text/javascript">
		jQuery(function($){
			var verifyurl='{:url('index/login/verify')}';
			if(verifyurl.indexOf('?')>0)verifyurl+='&';
			else verifyurl+= '?';
			$('.verifybox').click(function() {
				$(this).find('img').attr('src',verifyurl+'_t='+new Date().getTime());
			});
		});
	</script>

</block>