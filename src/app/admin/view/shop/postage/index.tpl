{extend name="public:base" /}

{block name="body"}

    {include  file="public/bread" menu="shop_postage_index" title="运费模板列表"  /}

    <div id="page-wrapper">

        <div class="row list-header">
            <div class="col-6">
                <a href="{:url('shop.postage/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加运费模板</a>
            </div>
            <div class="col-6">
            </div>
        </div>
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <th width="50">编号</th>
                <th width="200">名称</th>
                <th width="80">计费类型</th>
                <th>配送限制</th>
                <th width="160">&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            {foreach name="lists" item="v"}
                <tr>
                    <td>{$v.id}</td>
                    <td>{$v.title}{if $v['is_default']}<span class="badge badge-info">默认</span> {/if}</td>
                    <td>
                        {if $v['calc_type'] EQ 2}
                            <span class="badge badge-warning">按体积计算</span>
                            {elseif $v['calc_type'] EQ 1/}
                            <span class="badge badge-info">按件计算</span>
                            {else/}
                            <span class="badge badge-secondary">按重量计算</span>
                        {/if}
                    </td>
                    <td>
                        {$area_type==1?'仅':'不'}配送地区:
                        {:implode(', ',$v['specials'])}
                    </td>
                    <td class="operations">
                        <a class="btn btn-outline-primary" title="编辑" href="{:url('shop.postage/update',array('id'=>$v['id']))}"><i class="ion-md-create"></i> </a>
                        <a class="btn btn-outline-danger link-confirm" title="删除" data-confirm="您真的确定要删除吗？\n删除后将不能恢复!" href="{:url('shop.postage/delete',array('id'=>$v['id']))}" ><i class="ion-md-trash"></i> </a>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
{/block}