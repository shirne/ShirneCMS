{extend name="public:base" /}
{block name="body"}
    <div class="container">
        <div class="page-header"><h1>修改密码</h1></div>
    <form class="form-horizontal registerForm" role="form" method="post" onsubmit="return checkForm(this)" action="{:aurl('index/member/password')}">
        <div class="form-group">
            <label for="receive_name" class="col-sm-2 control-label">登录账号：</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="username" readonly value="{$user.username}"/>
            </div>
        </div>
        <div class="form-group">
            <label for="mobile" class="col-sm-2 control-label">新密码：</label>
            <div class="col-sm-10">
                <input type="password" class="form-control" name="newpassword" />
            </div>
        </div>
        <div class="form-group">
            <label for="mobile" class="col-sm-2 control-label">新密码确认：</label>
            <div class="col-sm-10">
                <input type="password" class="form-control" name="newpassword2" />
            </div>
        </div>

        <div class="form-group">
            <label for="mobile" class="col-sm-2 control-label">旧密码：</label>
            <div class="col-sm-10">
                <input type="password" class="form-control" name="password" />
            </div>
        </div>
        <div class="form-group submitline">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-block btn-primary create">提交保存</button>
            </div>
        </div>
    </form>
    </div>
{/block}
{block name="script"}
    <script type="text/javascript">
        function checkForm(){
            var newpassword=$('[name=newpassword]').val();
            var newpassword2=$('[name=newpassword2]').val();
            if(!newpassword){
                alert('请填写新密码');
                return false;
            }
            if(newpassword!=newpassword2){
                alert('两次密码输入不一致');
                return false;
            }
            var opassword=$('[name=password]').val();
            if(!opassword){
                alert('请填写密码再提交');
                return false;
            }
            return true;
        }
    </script>
{/block}