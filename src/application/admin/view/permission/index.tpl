<extend name="public:base" />

<block name="body">

<include file="public/bread" menu="permission_index" title="菜单列表" />

<div id="page-wrapper">
    
    <div class="row list-header">
        <div class="col-6">
            <a href="{:url('permission/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加菜单</a>
            <a href="{:url('permission/clearcache')}" class="btn btn-outline-dark btn-sm"><i class="ion-md-trash"></i> 清除缓存</a>
        </div>
        <div class="col-6">
        </div>
    </div>
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th width="50">编号</th>
                <th>菜单名</th>
                <th>键值</th>
                <th>链接</th>
                <th>状态</th>
                <th width="160">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        <foreach name="model[0]" item="v">
            <tr>
                <td>{$v.id}</td>
                <td>{$v.name}</td>
                <td>{$v.key}</td>
                <td>{$v.url}</td>
                <td data-url="{:url('status')}" data-id="{$v.id}">
                    <if condition="$v['disable'] EQ 1">
                        <span class="chgstatus off" data-id="{$v.id}" data-status="0" title="点击显示">隐藏</span>
                        <else/>
                        <span class="chgstatus" data-id="{$v.id}" data-status="1" title="点击隐藏">显示</span>
                    </if>
                </td>
                <td class="operations">
                    <a class="btn btn-outline-primary" title="添加" href="{:url('permission/add',array('pid'=>$v['id']))}"><i class="ion-md-add"></i> </a>
                    <a class="btn btn-outline-primary" title="编辑" href="{:url('permission/edit',array('id'=>$v['id']))}"><i class="ion-md-create"></i> </a>
                    <a class="btn btn-outline-danger link-confirm" title="删除" data-confirm="您真的确定要删除吗？\n删除后将不能恢复!" href="{:url('permission/delete',array('id'=>$v['id']))}" ><i class="ion-md-trash"></i> </a>
                </td>
            </tr>
            <php>$soncount=empty($model[$v['id']])?0:count($model[$v['id']]);</php>
            <foreach name="model[$v['id']]" item="sv">
                <tr>
                    <td>{$sv.id}</td>
                    <td><span class="tree-pre">{$soncount==$key+1?'└─':'├─'}</span> {$sv.name}</td>
                    <td>{$sv.key}</td>
                    <td>{$sv.url}</td>
                    <td data-url="{:url('status')}" data-id="{$sv.id}">
                        <if condition="$sv['disable'] EQ 1">
                            <span class="chgstatus off" data-id="{$sv.id}" data-status="0" title="点击显示">隐藏</span>
                            <else/>
                            <span class="chgstatus" data-id="{$sv.id}" data-status="1" title="点击隐藏">显示</span>
                        </if>
                    </td>
                    <td class="operations">
                        <a class="btn btn-outline-primary" title="添加" href="{:url('permission/add',array('pid'=>$sv['id']))}"><i class="ion-md-add"></i> </a>
                        <a class="btn btn-outline-primary" title="编辑" href="{:url('permission/edit',array('id'=>$sv['id']))}"><i class="ion-md-create"></i> </a>
                        <a class="btn btn-outline-danger link-confirm" title="删除" data-confirm="您真的确定要删除吗？\n删除后将不能恢复!" href="{:url('permission/delete',array('id'=>$sv['id']))}" onclick="javascript:return del(this,'您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="ion-md-trash"></i> </a>
                    </td>
                </tr>
                <foreach name="model[$sv['id']]" item="mv">
                    <tr>
                        <td>{$mv.id}</td>
                        <td><span class="fa">&nbsp;</span><span class="fa">┣</span> {$mv.name}</td>
                        <td>{$mv.key}</td>
                        <td>{$mv.url}</td>
                        <td data-url="{:url('status')}" data-id="{$mv.id}">
                            <if condition="$mv['disable'] EQ 1">
                                <span class="chgstatus off" data-status="0" title="点击显示">隐藏</span>
                                <else/>
                                <span class="chgstatus" data-status="1" title="点击隐藏">显示</span>
                            </if>
                        </td>
                        <td class="operations">
                            <a class="btn btn-outline-primary" title="编辑" href="{:url('permission/edit',array('id'=>$mv['id']))}"><i class="ion-md-create"></i> </a>
                            <a class="btn btn-outline-danger link-confirm" title="删除" data-confirm="您真的确定要删除吗？\n删除后将不能恢复!" href="{:url('permission/delete',array('id'=>$mv['id']))}" ><i class="ion-md-trash"></i> </a>
                        </td>
                    </tr>
                </foreach>
            </foreach>
        </foreach>
        </tbody>
    </table>
    {$page|raw}
</div>

</block>
<block name="script">
    <script type="text/javascript">
        jQuery(function ($) {

        })
    </script>
</block>