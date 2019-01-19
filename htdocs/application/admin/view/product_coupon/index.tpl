<extend name="public:base" />

<block name="body">

<include file="public/bread" menu="product_coupon_index" title="优惠券管理" />

<div id="page-wrapper">
    
    <div class="row list-header">
        <div class="col-6">
            <a href="{:url('productCoupon/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加优惠券</a>
        </div>
        <div class="col-6">
            <form action="{:url('productCoupon/index')}" method="post">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" name="key" placeholder="输入优惠券名称搜索">
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
                <th>名称</th>
                <th>类型</th>
                <th>适用</th>
                <th>有效期</th>
                <th>数量</th>
                <th>已领</th>
                <th width="160">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        <foreach name="lists" item="v">
            <tr>
                <td>{$v.id}</td>
                <td>{$v.title}</td>
                <td>{$v.type}</td>
                <td>{$v.brand_id}-{$v.cate_id}-{$v.product_id}-{$v.sku_id}</td>
                <td>{$v.start_time|showdate} - {$v.end_time|showdate}</td>
                <td>{$v.stock}</td>
                <td>{$v.receive}</td>
                <td class="operations">
                    <a class="btn btn-outline-primary" title="编辑" href="{:url('productCoupon/update',array('id'=>$v['id']))}"><i class="ion-md-create"></i></a>
                    <a class="btn btn-outline-primary" title="领取记录" href="{:url('productCoupon/itemlist',array('gid'=>$v['id']))}"><i class="ion-md-menu"></i></a>
                    <a class="btn btn-outline-danger link-confirm" data-confirm="您真的确定要删除吗？\n删除后将不能恢复!" title="删除" href="{:url('productCoupon/delete',array('id'=>$v['id']))}" ><i class="ion-md-trash"></i></a>
                </td>
            </tr>
        </foreach>
        </tbody>
    </table>
    {$page|raw}
</div>
</block>