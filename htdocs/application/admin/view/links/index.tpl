<extend name="Public:Base" />

<block name="body">

<include file="Public/bread" menu="links_index" section="其它" title="链接管理" />

<div id="page-wrapper">
    
    <div class="row">
        <div class="col-6">
            <a href="{:url('links/add')}" class="btn btn-primary">添加链接</a>
        </div>
        <div class="col-6">
            <form action="{:url('links/index')}" method="post">
                <div class="form-group input-group">
                    <input type="text" class="form-control" name="key" placeholder="输入链接标题或者地址关键词搜索">
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
                <th>标题</th>
                <th>地址</th>
                <th>优先级</th>
                <th width="250">操作</th>
            </tr>
        </thead>
        <tbody>
        <foreach name="lists" item="v">
            <tr>
                <td>{$v.id}</td>
                <td>{$v.title}</td>
                <td>{$v.url}</td>
                <td>{$v.sort}</td> 
                <td>
                    <a class="btn btn-dark btn-sm" href="{:url('links/edit',array('id'=>$v['id']))}"><i class="ion-edit"></i> 编辑</a>
                    <a class="btn btn-dark btn-sm" href="{:url('links/delete',array('id'=>$v['id']))}" onclick="javascript:return del('您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="ion-trash-a"></i> 删除</a>
                </td>
            </tr>
        </foreach>
        </tbody>
    </table>
    {$page}
</div>
</block>