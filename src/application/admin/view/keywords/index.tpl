<extend name="public:base" />

<block name="body">

<include file="public/bread" menu="keywords_index" title="关键字列表" />

<div id="page-wrapper">
    
    <div class="row list-header">
        <div class="col-6">
            <a href="{:url('keywords/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加关键字</a>
        </div>
        <div class="col-6">
            <form action="{:url('keywords/index')}" method="post">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" name="key" placeholder="输入关键字或者说明搜索">
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
                <th>LOGO</th>
                <th>分组</th>
                <th>标题</th>
                <th>说明</th>
                <th>热度</th>
                <th width="160">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <php>$empty=list_empty(7);</php>
        <volist name="lists" id="v" empty="$empty">
            <tr>
                <td>{$v.id}</td>
                <td>
                    <if condition="!empty($v['logo'])">
                    <figure class="figure img-view" data-img="{$v.logo}" >
                        <img src="{$v.logo}?w=100" class="figure-img img-fluid rounded" alt="image">
                    </figure>
                        <else/>
                        -
                    </if>
                </td>
                <td>{$v.group|default='无'}</td>
                <td>{$v.title}</td>
                <td>{$v.description} </td>
                <td>{$v.hot}</td> 
                <td class="operations">
                    <a class="btn btn-outline-primary" title="编辑" href="{:url('keywords/edit',array('id'=>$v['id']))}"><i class="ion-md-create"></i> </a>
                    <a class="btn btn-outline-danger link-confirm" title="删除" data-configm="您真的确定要删除吗？\n删除后将不能恢复!" href="{:url('keywords/delete',array('id'=>$v['id']))}" ><i class="ion-md-trash"></i> </a>
                </td>
            </tr>
        </volist>
        </tbody>
    </table>
    {$page|raw}
</div>
</block>