{extend name="public:base"/}

{block name="body"}

    {include  file="public/bread" menu="manager_index" title="角色信息" /}

    <div id="page-wrapper">
        <div class="page-header">{$model['id']>0?'编辑':'添加'}角色</div>
        <div id="page-content">

            <form action="" method="post">
                <div class="form-row">
                    <div class="form-group col">
                        <label>角色名</label>
                        <input class="form-control" type="text" name="role_name" value="{$model.role_name}" />
                    </div>
                    <div class="form-group col">
                        <label>角色等级</label>
                        <input class="form-control" type="text" {$model['type']==1?'readonly':''} name="type" value="{$model.type}"/>
                    </div>
                    <div class="form-group col">
                        <label>标签颜色</label>
<<<<<<< HEAD:src/app/admin/view/manager_role/update.tpl
                        <select name="style" class="form-control text-{$model.label_type}" onchange="$(this).attr('class','form-control text-'+$(this).val())">
                            {foreach name="styles" id="style"}
=======
                        <select name="label_type" class="form-control text-{$model.label_type}" onchange="$(this).attr('class','form-control text-'+$(this).val())">
                            <foreach name="styles" id="style">
>>>>>>> v2:src/application/admin/view/manager_role/update.tpl
                                <option value="{$style}" {$model['label_type']==$style?'selected':''} class="text-{$style}">██████████</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                {if $model['type']==1}
                    <div class="card">
                        <div class="card-header">权限设置</div>
                        <div class="card-body">
                            <div class="text-muted">该级别角色为最高权限，不可设置详细权限</div>
                        </div>
                    </div>
                    {else/}
                <div class="card">
                    <div class="card-header">全局权限</div>
                    <div class="card-body">
                        <label><input type="checkbox" name="global[]" value="edit" {if in_array('edit',$model['global'])}checked{/if} />&nbsp;编辑</label>
                        <label><input type="checkbox" name="global[]" value="del" {if in_array('del',$model['global'])}checked{/if} />&nbsp;删除</label>
                    </div>
                </div>
                <div class="card mt-4 mb-4">
                    <div class="card-header">详细权限&nbsp;&nbsp;<label><input type="checkbox" onclick="checkall(this)" />&nbsp;全选</label></div>

                    <ul class="list-group list-group-flush">
                        {foreach name="perms" item="perm" key="key"}
                            <li class="list-group-item">
                                <div class="row">
                                    <label class="col-2"><input type="checkbox" onclick="checkline(this)" />&nbsp;{$perm.title}</label>
                                    <div class="col-10">
                                        {foreach name="perm.items" item="item" key="k"}
                                            <label title="{$item}"><input type="checkbox" name="detail[]" value="{$key}_{$k}" {if in_array($key.'_'.$k,$model['detail'])}checked{/if} />&nbsp;{$item}</label>
                                        {/foreach}
                                    </div>
                                </div>
                            </li>
                        {/foreach}
                    </ul>
                </div>

                {/if}
                <div class="form-group mt-2">
                    <input type="hidden" name="id" value="{$model.id}">
                    <button class="btn btn-primary" type="submit">{$model['id']>0?'保存':'添加'}</button>
                </div>


            </form>
        </div>
    </div>

{/block}
{block name="script"}

    <script type="text/javascript">
        function checkall(src){
            var checked=$(src).is(':checked');
            $('[name^=global]').prop('checked',checked);
            $('[name^=detail]').prop('checked',checked);
            $('[onclick^=checkline]').prop('checked',checked);
        }
        function checkline(src){
            var checked=$(src).is(':checked');
            $(src).parents('li').find('[name^=detail]').prop('checked',checked);
        }
        $('input[name^=detail]').click(function(){
            var row=$(this).parents('li.list-group-item');
            var p=row.find('div.col-10');
            if(p.find(':checked').length==p.find('input').length){
                row.find('label.col-2 input').prop('checked',true);
            }else{
                row.find('label.col-2 input').prop('checked',false);
            }
        });
        jQuery(function(){
            $('.detail-line').each(function(){
                var row=$(this);
                var p=row.find('div.col-10');
                if(p.find(':checked').length==p.find('input').length){
                    row.find('label.col-2 input').prop('checked',true);
                }else{
                    row.find('label.col-2 input').prop('checked',false);
                }
            })
        });
    </script>

{/block}