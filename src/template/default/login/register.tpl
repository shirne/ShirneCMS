<extend name="public:base" />

<block name="body">
	<div class="main">
		<div class="container register">
			<div class="row justify-content-center">
				<div class="col-10 col-md-5">
			<div class="card">
				<div class="card-header">{:lang('User sign up')}</div>
				<div class="card-body">

					<form class="form-horizontal registerForm" role="form" method="post" action="{:url('index/login/register')}">
						<if condition="!empty($agent)">
							<div class="form-group">
								<div class="row">
									<div class="col" style="max-width: 65px;">
										<img src="{$agent.avatar|default='/static/images/avatar.png'}" style="width: 50px;display: block;border-radius: 1000px;">
									</div>
									<div class="col">
										<p style="margin-bottom:0.5rem;">{$agent['username']}</p>
										<p style="font-size: 13px;color: #888888;">{:lang('Your reference')}</p>
									</div>
								</div>
							</div>
						</if>
						<if condition="!empty($wechatUser)">
							<div class="form-group">
								<div class="row">
									<div class="col" style="max-width: 65px;">
										<img src="{$wechatUser['avatar']}" style="width: 50px;display: block;border-radius: 1000px;">
									</div>
									<div class="col">
										<p style="margin-bottom:0.5rem;">{$wechatUser['nickname']}</p>
										<p style="font-size: 13px;color: #888888;">{:lang('Bind to this wechat after sign up')}</p>
									</div>
								</div>
							</div>
						</if>
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">{:lang('Username')}</span>
								</div>
								<input type="text" class="form-control" name="username">
							</div>
							<div class="col-md-10">
								<span class="form-text text-muted">用户名以6—10位数字和字母组成 <i>*</i></span>
							</div>
						</div>
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">{:lang('Password')}</span>
								</div>
								<input type="password" class="form-control" name="password">
							</div>
							<div class="col-md-10">
								<span class="form-text text-muted">密码以6—20位字符，可包含大小写字母，数字及特殊符号<i>*</i></span>
							</div>
						</div>
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">{:lang('Confirm password')}</span>
								</div>
								<input type="password" class="form-control" name="repassword">
							</div>
							<div class="col-md-10">
								<span class="form-text text-muted">请再次确认您输入的密码<i>*</i></span>
							</div>
						</div>
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">{:lang('Mobile')}</span>
								</div>
								<input type="text" class="form-control" name="mobile">
								<if condition="$config['sms_code'] EQ 1">
									<div class="input-group-append">
										<a class="btn btn-outline-secondary input-group-addon sms_send_btn">发送验证码</a>
									</div>
								</if>
							</div>
							<div class="col-md-10">
								<span class="form-text text-muted">请填写11位手机号码<i>*</i></span>
							</div>
						</div>
						<if condition="$config['sms_code'] EQ 1">
							<div class="form-group">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">短信验证</span>
								</div>
								<input type="text" class="form-control" name="mobilecheck">
							</div>
							</div>
						</if>
						<if condition="$nocode">
							<else/>
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">激活码</span>
								</div>
								<input type="text" class="form-control" name="invite_code">
							</div>
							<div class="col-md-10">
								<span class="form-text text-muted">您的推荐人提供给你的激活码<if condition="$config['m_invite'] eq 2"><i>*</i></if></span>
							</div>
						</div>
						</if>
						<div class="form-group submitline">
							<button type="submit" class="btn btn-primary btn-block create">{:lang('Create account')}</button>
						</div>
						<div class="form-group">
							<div class="text-center">
								{:lang('Have an account?')}<a href="{:url('index/login/index')}">{:lang('Goto sign in')}</a>
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
			var second_limit=120;
            var last_send=0;
            var send_btn=$('.sms_send_btn');
            var origText=send_btn.text();
            setInterval(function () {
				var nowtick=new Date().getTime();
				if(nowtick-last_send<second_limit*1000){
				    if(!send_btn.is('.disabled')){
                        send_btn.addClass('disabled');
                    }
                    var seconds=parseInt((nowtick-last_send)/1000);
                    send_btn.text((second_limit-seconds)+'s后重新发送');
                }else if(send_btn.is('.disabled')){
                    send_btn.removeClass('disabled').text(origText);

                }
            },200);
            send_btn.click(function (e) {
				var nowtick=new Date().getTime();
				if(nowtick-last_send<second_limit*1000){
				    return;
                }
                var mobile=$(this).parents('.form-group').find('input[type=text]').val();
				if(!mobile || !mobile.match(/^1[2-9]\d{9}$/)){
				    dialog.alert('请填写手机号码');
				    return false;
                }
                var is_sending=false;
				var dlg=dialog.prompt({
					title:'请填写验证码',
					content:'<div class="form-group"><div class="text-center"><img src="" class="verify_img" width="208" height="64" /></div></div>',
					onshow:function (body) {
					    var imgurl='{:url("index/login/verify")}';
						body.find('.verify_img').click(function() {
                            this.src=imgurl+'?_t='+new Date().getTime();
                        }).trigger('click');
                    }
				},function(code) {
				    if(!is_sending) {
                        is_sending = true;
                        $.ajax({
                            url: "{:url('index/login/send_checkcode')}",
                            type: 'POST',
                            data: {
                                code: code,
                                mobile: mobile
                            },
                            dataType: 'JSON',
                            success: function (json) {
                                is_sending=false;
                                console.log(json);
                                dialog.alert(json.msg);
                                if (json.code == 1) {
                                    last_send = nowtick;
                                    dlg.hide();
                                }else{
                                    dlg.box.find('.verify_img').trigger('click');
                                    dlg.box.find('input').val('');
                                }
                            }
                        });
                    }
				    return false;
                });
            });
			
			$('.registerForm').submit(function(e) {
				e.preventDefault();
				e.stopPropagation();
				
				var username=$(this).find('[name=username]').val();
				if(username=='')return $(this).find('[name=username]').trigger('blur').focus();

				var password=$(this).find('[name=password]').val();
				if(password=='')return $(this).find('[name=password]').trigger('blur').focus();

				var realname=$(this).find('[name=realname]').val();
				if(realname=='')return $(this).find('[name=realname]').trigger('blur').focus();

				var email=$(this).find('[name=email]').val();
				if(email=='')return $(this).find('[name=email]').trigger('blur').focus();

				var mobile=$(this).find('[name=mobile]').val();
				if(mobile=='')return $(this).find('[name=mobile]').trigger('blur').focus();

                if ('{$config["m_invite"]}' == '2') {
                    var invite_code = $(this).find('[name=invite_code]').val();
                    if (invite_code == '') return $(this).find('[name=invite_code]').trigger('blur').focus();
                }

				if($(this).find('.error').length>0){
					return alert('请按要求填写表单');
				}else {
					$.ajax({
						url: $(this).attr('action'),
						data: $(this).serialize(),
						dataType: 'JSON',
						type: 'POST',
						success: function (j) {
							if (j.code == 1) {
                                dialog.alert('注册成功！',function() {
                                    location.href = j.url;
                                });
							} else {
                                dialog.alert(j.msg);
							}
						}
					});
				}
			});
			var ajaxtime=new Object();
			$('.registerForm .form-control').blur(function() {
				var val=$(this).val(),form=$(this.form);
				var error='',fname=this.name,self=this;
				var time=new Date().getTime();
				switch (fname){
					case 'username':
						if(val=='') {
							error = '请填写用户名';
						}else if(!val.match(/^[a-zA-Z][a-zA-Z0-9\-]{5,9}$/)) {
							error = '用户名必须由字母和数字且6-10位';
						}else{
							ajaxtime[fname]=new Date().getTime();
							$.ajax({
								url:'{:url('index/login/checkunique',array('type'=>'username'))}',
								data:{value:val},
								dataType:'JSON',
								type:'POST',
								success:function(j){
									if(time != ajaxtime[fname])return;
									if(j.error){
										showError(self,'用户名已被占用');
									}
								}
							})
						}
						break;
					case 'password':
						if(val=='') {
							error = '请填写密码';
						}else if(val.length<6 || val.length>20) {
							error = '密码必须达到6-20位';
						}
						form.find('[name=repassword]').trigger('blur');
						break;
					case 'repassword':
						if($(this).is(':focus'))return;
						if(val!==form.find('[name=password]').val()) {
							error = '两次密码输入不一致';
						}
						break;
					case 'email':
						if(val=='') {
							error = '请填写邮箱';
						}else if(!val.match(/^([0-9A-Za-z\-_\.]+)@([0-9a-z]+\.[a-z]{2,3}(\.[a-z]{2})?)$/)) {
							error = '邮箱格式不正确';
						}else{
							ajaxtime[fname]=new Date().getTime();
							$.ajax({
								url:'{:url('index/login/checkunique',array('type'=>'email'))}',
								data:{value:val},
								dataType:'JSON',
								type:'POST',
								success:function(j){
									if(time != ajaxtime[fname])return;
									if(j.error){
										showError(self,'邮箱已被占用');
									}
								}
							})
						}
						break;
					case 'mobile':
						if(val=='') {
							error = '请填写手机号码';
						}else if(!val.match(/^1[3458679][0-9]{9}$/)) {
							error = '手机号码格式错误';
						}else{
							ajaxtime[fname]=new Date().getTime();
							$.ajax({
								url:"{:url('index/login/checkunique',array('type'=>'mobile'))}",
								data:{value:val},
								dataType:'JSON',
								type:'POST',
								success:function(j){
									if(time != ajaxtime[fname])return;
									if(j.error){
										showError(self,'手机号码已被占用');
									}
								}
							})
						}
						break;
					case 'realname':
						if(val=='') {
							error = '请填写真实姓名';
						}
						break;
                    case 'invite_code':
                        if (val == '') {
                            if ('{$config["m_invite"]}' == '2') error = '请填写激活码';
                        } else if (!val.match(/^[a-zA-Z0-9\-]{9,20}$/)) {
                            error = '激活码格式错误';
                        }
						break;
					case 'mobilecheck':
                        if(val=='') {
                            error = '请填写短信验证码';
                        }
                        break;
				}
				if(error) {
					showError(this, error);
					return false;
				}
				return true;
			}).keyup(function() {
				hideError(this);
			});
			$('.form-text').each(function() {
				$(this).data('origin',$(this).html());
			});
			function showError(field,msg){
				$(field).addClass('is-invalid');
				var msgbox=$(field).parents('.form-group').find('.form-text');
				if(msgbox.length<1)return;
				if(!msgbox.data('origin'))msgbox.data('origin',msgbox.html());
				msgbox.text(msg);
			}
			function hideError(field){
				$(field).removeClass('is-invalid');
				var msgbox=$(field).parents('.form-group').find('.form-text');
				if(msgbox.length<1)return;
				if(msgbox.data('origin')) {
					msgbox.html(msgbox.data('origin'));
				}else {
					msgbox.text('');
				}
			}
		});
	</script>
</block>