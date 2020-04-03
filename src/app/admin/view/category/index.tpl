{extend name="public:base" /}

{block name="body"}

{include  file="public/bread" menu="category_index" title=""  /}

<div id="page-wrapper">
    
    <div class="row list-header">
        <div class="col-6">
            <a href="{:url('category/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> {:lang('Add Category')}</a>
        </div>
        <div class="col-6">
            <form action="{:url('category/index')}" method="post">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" name="key" placeholder="{:lang('Search title or slug')}">
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
                <th width="50">{:lang('ID')}</th>
                <th>{:lang('Title')}</th>
                <th>{:lang('Slug')}</th>
                <th>{:lang('Sort')}</th>
                <th width="160">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        {volist name="model" id="v" empty="$empty"}
            <tr>
                <td>{$v.id}</td>
                <td>{$v.html|raw} {$v.title}&nbsp;<span class="badge badge-info">{$v.short}</span>{if $v.use_template EQ 1}&nbsp;<span class="badge badge-warning">{:lang('Independ Template')}</span>{/if}</td>
                <td>{$v.name}</td>
                <td>{$v.sort}</td>
                <td class="operations">
                    <a class="btn btn-outline-primary" title="{:lang('Publish Article')}" href="{:url('article/add',array('cid'=>$v['id']))}"><i class="ion-md-paper-plane"></i> </a>
                    <a class="btn btn-outline-primary" title="{:lang('Add Sub Category')}" href="{:url('category/add',array('pid'=>$v['id']))}"><i class="ion-md-add"></i> </a>
                    <a class="btn btn-outline-primary" title="{:lang('Edit')}" href="{:url('category/edit',array('id'=>$v['id']))}"><i class="ion-md-create"></i> </a>
                    <a class="btn btn-outline-danger link-confirm" title="{:lang('Delete')}" data-confirm="{:lang('Confirm to delete? The operation can not restore!')}" href="{:url('category/delete',array('id'=>$v['id']))}"  ><i class="ion-md-trash"></i> </a>
                </td>
            </tr>
        {/volist}
        </tbody>
    </table>
</div>

{/block}