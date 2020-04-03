{extend name="public:base"/}

{block name="body"}
    <div class="page__hd">
        <h1 class="page__title">Signin</h1>
        <p class="page__desc">会员注册</p>
    </div>
    <form class="registerForm" method="post" action="{:url('index/login/register')}">
    <div class="weui-cells weui-cells_form">
        {if !empty($wechatUser)}
            <div class="weui-cell">
                <div class="weui-cell__hd" style="position: relative;margin-right: 10px;">
                    <img src="{$wechatUser['avatar']}" style="width: 50px;display: block;border-radius: 1000px;">
                </div>
                <div class="weui-cell__bd">
                    <p>{$wechatUser['nickname']}</p>
                    <p style="font-size: 13px;color: #888888;">注册成功后将与微信账号绑定</p>
                </div>
            </div>
        {/if}
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">用户名</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="text" name="username" placeholder="用户名以6—10位数字和字母组成">
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd">
                <label class="weui-label">密&emsp;码</label>
            </div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="password" name="password" placeholder="密码以6—20位字符，可包含大小写字母，数字及特殊符号">
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd">
                <label class="weui-label">确认密码</label>
            </div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="password" name="repassword" placeholder="请再次确认您输入的密码">
            </div>
        </div>
        {if 0}
            <div class="weui-cell weui-cell_vcode">
                <div class="weui-cell__hd">
                    <label class="weui-label">手机号</label>
                </div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="text" name="mobile" placeholder="请输入手机号">
                </div>
                <div class="weui-cell__ft">
                    <button class="weui-vcode-btn">获取验证码</button>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd">
                    <label class="weui-label">短信验证</label>
                </div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="number" name="mobilecheck" placeholder="请输入手机号">
                </div>
            </div>
            {else/}
            <div class="weui-cell">
                <div class="weui-cell__hd">
                    <label class="weui-label">手机号</label>
                </div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="text" name="mobile" placeholder="请输入手机号">
                </div>
            </div>
        {/if}
        {if $nocode}
            {else/}
            <div class="weui-cell">
                <div class="weui-cell__hd">
                    <label class="weui-label">激活码</label>
                </div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="text" name="invite_code" placeholder="您的推荐人提供给你的激活码">
                </div>
            </div>
        {/if}
    </div>
        <label for="weuiAgree" class="weui-agree">
            <input id="weuiAgree" type="checkbox" class="weui-agree__checkbox">
            <span class="weui-agree__text">
                阅读并同意<a href="javascript:void(0);">《会员注册协议》</a>
            </span>
        </label>
        <div class="weui-btn-area">
            <a class="weui-btn weui-btn_primary" href="javascript:" id="showTooltips">创建我的账户</a>
            <div class="text-center">
                已有账号?<a href="{:url('index/login/index')}">前往登录</a>
            </div>
        </div>
    </form>
{/block}
{block name="script"}
    <script type="text/javascript">
        jQuery(function($){
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
            $('.registerForm .weui-input').blur(function() {
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
                                url:'{:url('index/login/checkunique',array('type'=>'mobile'))}',
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
                var group=$(field).parents('.weui-cell');
                group.addClass('weui-cell_warn');
                var msgbox=group.find('.form-text');
                if(msgbox.length<1)return;
                if(!msgbox.data('origin'))msgbox.data('origin',msgbox.html());
                msgbox.text(msg);
            }
            function hideError(field){
                var group=$(field).parents('.weui-cell');
                group.removeClass('weui-cell_warn');
                var msgbox=group.find('.form-text');
                if(msgbox.length<1)return;
                if(msgbox.data('origin')) {
                    msgbox.html(msgbox.data('origin'));
                }else {
                    msgbox.text('');
                }
            }
        });
    </script>
{/block}