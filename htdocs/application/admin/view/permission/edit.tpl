<extend name="public:base" />

<block name="body">
<include file="public/bread" menu="permission_index" title="菜单配置" />

<div id="page-wrapper">
    <div class="page-header">修改菜单</div>
    <div id="page-content">
    <form method="post" action="">
        <div class="form-group">
            <label for="page-title">所属菜单</label>
            <select name="parent_id" class="form-control">
                <option value="0">顶级菜单</option>
                <foreach name="menus[0]" item="m">
                    <option value="{$m['id']}" {$m['id']==$perm['id']?'disabled':''} {$m['id']==$perm['parent_id']?'selected':''}>{$m['name']}</option>
                    <foreach name="menus[$m['id']]" item="sm">
                        <option value="{$sm['id']}" <?php echo ($sm['id']==$perm['id']||$m['id']==$perm['id'])?'disabled':'';?> {$sm['id']==$perm['parent_id']?'selected':''}>┣{$sm['name']}</option>
                    </foreach>
                </foreach>
            </select>
        </div>
        <div class="form-group">
            <label for="page-title">菜单名称</label>
            <input type="text" name="name" class="form-control" value="{$perm.name}" id="perm-title" placeholder="输入菜单名称">
        </div>
        <div class="form-group">
            <label for="page-title">键名</label>
            <input type="text" name="key" class="form-control" value="{$perm.key}" id="perm-title" placeholder="输入键名">
        </div>
        <div class="form-group">
            <label for="page-name">菜单链接</label>
            <input type="text" name="url" class="form-control" value="{$perm.url}" id="perm-name" placeholder="输入链接，包含下级的一级菜单不需要链接">
        </div>
        <div class="form-group">
            <label for="page-name">菜单图标</label>
            <input type="text" name="icon" class="form-control" value="{$perm.icon}" id="perm-icon" placeholder="图标类名,从font-awasome中查找">
        </div>
        <div class="form-group">
            <label for="p-content">排序</label>
            <input type="text" name="order_id" class="form-control" value="{$perm.order_id}" id="perm-order_id" placeholder="排序，从小到大">
        </div>
        <div class="form-group">
        <input type="hidden" name="id" value="{$perm.id}">
        <button type="submit" class="btn btn-primary">提交</button>
            </div>
    </form>
        </div>
</div>
    </block>
<block name="script">
<script type="text/javascript">
</script>
</block>