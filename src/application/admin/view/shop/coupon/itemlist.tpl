<extend name="public:base" />

<block name="body">

<include file="public/bread" menu="shop_coupon_index" title="优惠券领取列表" />

<div id="page-wrapper">
    
    <div class="row list-header">
        <div class="col-6">
            &nbsp;
        </div>
        <div class="col-6">
            <form action="{:url('shop.coupon/itemlist')}" method="post">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" name="key" placeholder="输入优惠券名或会员信息搜索">
                    <div class="input-group-append">
                      <button class="btn btn-outline-secondary" type="submit"><i class="ion-md-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th width="50">编号</th>
                <th>优惠券</th>
                <th>领取人</th>
                <th>领取时间</th>
                <th>有效期</th>
                <th>状态</th>
                <th width="160">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        <foreach name="lists" item="v">
            <tr>
                <td>{$v.id}</td>
                <td><figure class="figure" >
                        <img src="{$v.image}?w=100" class="figure-img img-fluid rounded" alt="image">
                    </figure></td>
                <td>{$v.title}</td>
                <td>{$v.member_id}{$v.member_name}</td>
                <td>{$v.create_time|showdate}</td>
                <td>{$v.start_date|showdate}<br />{$v.end_date|showdate}</td>
                <td>{$v.status|show_status|raw}</td>
                <td class="operations">
                    <a class="btn btn-outline-danger link-confirm" title="删除" data-confirm="您真的确定要删除吗？\n\n删除后将不能恢复!" href="{:url('shop.coupon/itemdelete',array('id'=>$v['id'],'gid'=>$gid))}" ><i class="ion-md-trash"></i> </a>
                </td>
            </tr>
        </foreach>
        </tbody>
    </table>
    {$page|raw}
</div>
</block>