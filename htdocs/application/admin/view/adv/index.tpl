<extend name="Public:Base" />

<block name="body">

<include file="Public/bread" menu="adv_index" section="其它" title="广告位管理" />

<div id="page-wrapper">
    
    <div class="row">
        <div class="col-6">
            <a href="{:url('adv/add')}" class="btn btn-primary">添加广告位</a>
        </div>
        <div class="col-6">
            <form action="{:url('adv/index')}" method="post">
                <div class="form-group input-group">
                    <input type="text" class="form-control" name="key" placeholder="输入标题或者地址关键词搜索">
                    <div class="input-group-append">
                      <button class="btn btn-outline-secondary" type="submit"><i class="ion-search"></i></button>
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
                    <a class="btn btn-default btn-sm" href="{:url('adv/update',array('id'=>$v['id']))}"><i class="ion-edit"></i> 编辑</a>
                    <div class="btn-group">
                        <a class="btn btn-default btn-sm" href="{:url('adv/itemlist',array('gid'=>$v['id']))}"><i class="ion-list-ul"></i> 广告列表</a>
                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="{:url('adv/itemadd',array('gid'=>$v['id']))}">添加广告</a></li>
                        </ul>
                    </div>
                    <a class="btn btn-default btn-sm" href="{:url('adv/delete',array('id'=>$v['id']))}" style="color:red;" onclick="javascript:return del('您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="ion-trash"></i> 删除</a>
                </td>
            </tr>
        </foreach>
        </tbody>
    </table>
    {$page}
</div>
</block>