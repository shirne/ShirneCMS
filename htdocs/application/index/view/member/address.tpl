<extend name="public:base" />
<block name="body">
    <div class="container">
        <div class="page-header">
            <div class="pull-right"><a class="btn btn-default btn-confirm" href="{:url('index/member/addressAdd')}" >添加地址</a></div>
            <h1>收货地址</h1>
        </div>
        <ul class="list-group">
            <foreach name="address" item="v">
                <li class="list-group-item">
                    <div>{$v.recive_name} <if condition="$v.is_default"><span class="label label-info">默认</span></if><span class="pull-right">{$v.mobile}</span></div>
                    <div>
                        {$v.province}&nbsp;{$v.city}&nbsp;{$v.area}&nbsp;{$v.address}
                    </div>
                    <div class="order-btns">
                        <a class="btn btn-default btn-confirm"  href="{:url('index/member/addressAdd',array('id'=>$v['address_id']))}">编辑</a>
                    </div>
                </li>
            </foreach>
        </ul>
    </div>
</block>