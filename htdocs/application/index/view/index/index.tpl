<extend name="public:base"/>

<block name="body">
    <div class="container">

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">系统说明</h5>
                <p>列表页:
                    <a href="{:url('Post/index',array('name'=>'cnsecer'))}">{:url('Post/index',array('name'=>'cnsecer'))}</a>
                </p>
                <p>详情页: <a href="{:url('Post/view',array('id'=>1))}">{:url('Post/view',array('id'=>1))}</a></p>
                <p>单页:
                    <a href="{:url('Page/index',array('name'=>'cnsecer'))}">{:url('Page/index',array('name'=>'cnsecer'))}</a>
                </p>
                <p>登录页: <a href="{:url('login/login')}">{:url('login/login')}</a></p>
                <p>QQ登录: <a href="{:url('login/login',array('type'=>'qq'))}">{:url('login/login',array('type'=>'qq'))}</a>
                </p>
                <p>会员中心：<a href="{:url('Member/index')}">{:url('Member/index')}</a></p>
                <hr>
                <p>后台地址: <a href="/admin/">/admin/</a></p>
                <p>账号:admin</p>
                <p>密码:123456</p>
            </div>
        </div>
    </div>
</block>
