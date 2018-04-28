<extend name="Public:Base" />

<block name="body">

<include file="Public/bread" menu="category_index" section="内容" title="分类管理" />

<div id="page-wrapper">
    
    <div class="row">
        <div class="col-xs-6">
            <a href="{:U('category/edit')}" class="btn btn-success">添加分类</a>
        </div>
        <div class="col-xs-6">
            <form action="{:U('category/index')}" method="post">
                <div class="form-group input-group">
                    <input type="text" class="form-control" name="key" placeholder="输入分类标题或者别名关键词搜索">
                    <span class="input-group-btn">
                      <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
                    </span>
                </div>
            </form>
        </div>
    </div>
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th width="50">编号</th>
                <th>标题</th>
                <th>别名</th>
                <th width="150">操作</th>
            </tr>
        </thead>
        <tbody>
        <foreach name="model" item="v">
            <tr>
                <td>{$v.id}</td>
                <td>{$v.html} {$v.title}</td>
                <td>{$v.name}</td>

                <td>
                    <a class="btn btn-default btn-sm" href="{:U('category/edit',array('id'=>$v['id']))}"><i class="fa fa-edit"></i> 编辑</a>
                    <a class="btn btn-default btn-sm" href="{:U('category/delete',array('id'=>$v['id']))}" style="color:red;" onclick="javascript:return del('您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="fa fa-trash"></i> 删除</a>
                </td>
            </tr>
        </foreach>
        </tbody>
    </table>
    {$page}
</div>

</block>