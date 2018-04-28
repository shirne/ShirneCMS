<extend name="Public:Base"/>

<block name="body">
    <div class="container">
        <div class="page-header">
            <h3>系统说明</h3>
        </div>
        <div class="panel panel-default">
            <div class="panel-body">
                <p>列表页:
                    <a href="{:U('Post/index',array('name'=>'cnsecer'))}">{:U('Post/index',array('name'=>'cnsecer'))}</a>
                </p>
                <p>详情页: <a href="{:U('Post/view',array('id'=>1))}">{:U('Post/view',array('id'=>1))}</a></p>
                <p>单页:
                    <a href="{:U('Page/index',array('name'=>'cnsecer'))}">{:U('Page/index',array('name'=>'cnsecer'))}</a>
                </p>
                <p>登录页: <a href="{:U('login/login')}">{:U('login/login')}</a></p>
                <p>QQ登录: <a href="{:U('login/login',array('type'=>'qq'))}">{:U('login/login',array('type'=>'qq'))}</a>
                </p>
                <p>会员中心：<a href="{:U('Member/index')}">{:U('Member/index')}</a></p>
                <hr>
                <p>后台地址: <a href="/admin.php">/admin.php</a></p>
                <p>账号:admin</p>
                <p>密码:123456</p>
            </div>
        </div>
    </div>
</block>
