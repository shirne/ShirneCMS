<extend name="public:base" />
<block name="body">
    <div class="page panel">
        <div class="page__hd">
            <div class="page__title">
                <div class="float-right"><a class="weui-btn weui-btn_mini weui-btn_plain-primary" href="{:aurl('index/member.address/add')}" >添加地址</a></div>
                收货地址
            </div>
        </div>
        <div class="page__bd">
            <php>$empty='<p class="empty">暂时没有记录</p>';</php>
            <foreach name="addresses" empty="$empty" item="v">
                <div class="weui-panel">
                    <div class="weui-panel__bd">
                        <div class="weui-media-box weui-media-box_text">
                            <h4 class="weui-media-box__title">{$v.receive_name}  /  <span class="text-secondary">{$v.mobile}</span></h4>
                            <p class="weui-media-box__desc">{$v.province}&nbsp;{$v.city}&nbsp;{$v.area}&nbsp;{$v.address}</p>
                            <div class="weui-media-box__info">
                                <if condition="$v.is_default"><span class="weui-media-box__info__meta">默认</span></if>
                                <a href="{:aurl('index/member.address/edit',array('id'=>$v['address_id']))}" class="weui-media-box__info__meta weui-media-box__info__meta_extra">编辑</a>
                            </div>
                        </div>
                    </div>
                </div>
            </foreach>
        </div>
    </div>
</block>