<extend name="public:base" />
<block name="body">
    <div class="main">
        <div class="container loginbox">
            <div class="row justify-content-center">
                <div class="col-10 col-lg-5">
                    <div class="card my-card">
                        <div class="card-header">{:lang('Reset your password')}</div>
                        <div class="card-body">

                            <form class="form-horizontal" role="form" method="post" action="{:url('index/login/getpassword')}">
                                <div class="form-group steprow step-1 step-2 step-3">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="ion-md-person"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="username" placeholder="{:lang('Username')}" />
                                    </div>
                                    <div class="text-muted">请填写您要找回的用户名</div>
                                </div>
                                <div class="form-group steprow step-1">
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <label class="btn btn-outline-secondary active">
                                            <input type="radio" name="authtype" value="mobile" autocomplete="off" checked> 手机短信找回
                                        </label>
                                        <label class="btn btn-outline-secondary">
                                            <input type="radio" name="authtype" value="email" autocomplete="off"> 邮箱验证码找回
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group steprow step-1">
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
                                <div class="form-group checkcodebox steprow step-2">
                                    <div class="text-muted"></div>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="ion-md-checkmark"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="checkcode" />
                                    </div>
                                </div>
                                <div class="form-group steprow step-3">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="ion-md-lock"></i></span>
                                        </div>
                                        <input type="password" class="form-control" name="password" placeholder="{:lang('Password')}" />
                                    </div>
                                    <div class="text-muted">请填写您要修改的密码</div>
                                </div>
                                <div class="form-group steprow step-3">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="ion-md-lock"></i></span>
                                        </div>
                                        <input type="password" class="form-control" name="repassword" placeholder="{:lang('Confirm Password')}" />
                                    </div>
                                    <div class="text-muted">请再次输入以确认无误</div>
                                </div>
                                <div class="form-group submitline">
                                    <input type="hidden" name="step" value="2" />
                                    <button type="button" class="btn btn-info btn-block btn-step">{:lang('Next step')}</button>
                                </div>
                                <div class="form-group">
                                    <div class="text-center">
                                        <a href="{:url('index/login/index')}">{:lang('Back to login')}</a>
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
            var step=1;
            var verifyurl='{:url('index/login/verify')}';
            if(verifyurl.indexOf('?')>0)verifyurl+='&';
            else verifyurl+= '?';
            $('.verifybox').click(function() {
                $(this).find('img').attr('src',verifyurl+'_t='+new Date().getTime());
            });
            
            function showStep(step) {
                $('.steprow').hide();
                $('.step-'+step).show();
            }

            showStep(step);
            $('.loginbox form').submit(function (e) {
                e.preventDefault();
                var form=$(this);
                $('.btn-step').prop('disabled',true);
                $.ajax({
                    url:'',
                    data:form.serialize(),
                    dataType:'JSON',
                    type:'POST',
                    success:function (json) {
                        $('.btn-step').prop('disabled',false);
                        if(json.code==1){
                            step++;
                            dialog.alert(json.msg,function () {
                                if(step==4 && json.url){
                                    location.href=json.url;
                                }
                            });
                            showStep(step);
                            if(step===2) {
                                $('[name=username]').prop('readonly',true);
                                var data=json.data;
                                $('.checkcodebox .text-muted').text('验证码已发送至您的'+data.sendtoname+' '+data.sendto+'，请查收并填写您收到的验证码');
                            }
                            $('[name=step]').val(step+1);
                        }else{
                            dialog.alert(json.msg);
                        }
                    },
                    error:function () {
                        $('.btn-step').prop('disabled',false);
                        dialog.alert('系统错误');
                    }
                });
                return false;
            })
        });
    </script>

</block>