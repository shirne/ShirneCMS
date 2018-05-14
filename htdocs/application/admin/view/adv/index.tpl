<extend name="public:base" />

<block name="body">

<include file="public/bread" menu="adv_index" title="广告位" />

<div id="page-wrapper">
    
    <div class="row list-header">
        <div class="col-6">
            <a href="{:url('adv/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加广告位</a>
        </div>
        <div class="col-6">
            <form action="{:url('adv/index')}" method="post">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" name="key" placeholder="输入标题或者地址关键词搜索">
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
                <th>调用标识</th>
                <th width="300">操作</th>
            </tr>
        </thead>
        <tbody>
        <foreach name="lists" item="v">
            <tr>
                <td>{$v.id}</td>
                <td>{$v.title}</td>
                <td>{$v.flag}</td>
                <td>
                    <a class="btn btn-outline-dark btn-sm" href="{:url('adv/update',array('id'=>$v['id']))}"><i class="ion-md-create"></i> 编辑</a>
                    <div class="btn-group btn-group-sm">
                        <a class="btn btn-outline-dark" href="{:url('adv/itemlist',array('gid'=>$v['id']))}"><i class="ion-md-list"></i> 广告列表</a>
                        <button type="button" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{:url('adv/itemadd',array('gid'=>$v['id']))}">添加广告</a>
                        </div>
                    </div>
                    <a class="btn btn-outline-dark btn-sm" href="{:url('adv/delete',array('id'=>$v['id']))}" onclick="javascript:return del('您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="ion-md-trash"></i> 删除</a>
                </td>
            </tr>
        </foreach>
        </tbody>
    </table>
    {$page|raw}
</div>
</block>