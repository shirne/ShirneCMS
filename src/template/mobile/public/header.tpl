<div class="weui-tabbar">
    <a href="{:url('index/index/index')}" class="weui-tabbar__item{:is_nav('index',$navmodel)?' weui-bar__item_on':''}">
        <i class="weui-tabbar__icon ion-md-home"></i>
        <p class="weui-tabbar__label">首页</p>
    </a>
    <a href="{:url('index/product/index')}" class="weui-tabbar__item{:is_nav('product',$navmodel)?' weui-bar__item_on':''}">
        <i class="weui-tabbar__icon ion-md-cube"></i>
        <p class="weui-tabbar__label">产品</p>
    </a>
    <a href="{:url('index/page/index',['group'=>'about'])}" class="weui-tabbar__item{:is_nav('page',$navmodel)?' weui-bar__item_on':''}">
        <i class="weui-tabbar__icon ion-md-information-circle"></i>
        <p class="weui-tabbar__label">关于</p>
    </a>
    <a href="{:aurl('index/member/index')}" class="weui-tabbar__item{:is_nav('member',$navmodel)?' weui-bar__item_on':''}">
        <i class="weui-tabbar__icon ion-md-person"></i>
        <p class="weui-tabbar__label">我</p>
    </a>
</div>