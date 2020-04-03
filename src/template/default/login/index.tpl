{extend name="public:base" /}
{block name="body"}
	<div class="main">
		<div class="container loginbox">
			<div class="row justify-content-center">
				<div class="col-10 col-md-5">
			<div class="card">
				<div class="card-header">{:lang('User sign in')}</div>
				<div class="card-body">

					<form class="form-horizontal" role="form" method="post" action="{:url('index/login/index')}">
						{if !empty($wechatUser)}
							<div class="form-group">
								<div class="row">
								<div class="col" style="max-width: 65px;">
									<img src="{$wechatUser['avatar']}" style="width: 50px;display: block;border-radius: 1000px;">
								</div>
								<div class="col">
									<p style="margin-bottom:0.5rem;">{$wechatUser['nickname']}</p>
									<p style="font-size: 13px;color: #888888;">{:lang('Bind to this wechat after sign in')}</p>
								</div>
								</div>
							</div>
						{/if}
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="ion-md-person"></i></span>
								</div>
								<input type="text" class="form-control" name="username" placeholder="{:lang('Username')}" />
							</div>
						</div>
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="ion-md-lock"></i></span>
								</div>
								<input type="password" class="form-control" name="password" placeholder="{:lang('Password')}" />
							</div>
						</div>
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="ion-md-checkmark"></i></span>
								</div>
								<input type="text" class="form-control" name="verify" placeholder="{:lang('Verify')}" />
								<div class="input-group-append">
									<a href="javascript:" class="input-group-text verifybox" style="padding:0;"><img src="{:url('index/login/verify')}" alt=""></a>
								</div>
							</div>
						</div>
						<div class="form-group submitline">
							<button type="submit" class="btn btn-primary btn-block create">{:lang('Sign in')}</button>
						</div>
						<div class="form-group">
							<div class="text-center">
							{:lang('No account yet?')}<a href="{:url('index/login/register')}">{:lang('Create an account')}</a>
							</div>
						</div>
					</form>

				</div>
			</div>
				</div>
			</div>
		</div>
	</div>
{/block}
{block name="script"}
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

{/block}