<extend name="public:base" />

<block name="body">

<include file="public/bread" menu="category_index" section="内容" title="分类管理" />

<div id="page-wrapper">
    
    <div class="row list-header">
        <div class="col-6">
            <a href="{:url('category/add')}" class="btn btn-outline-primary">添加分类</a>
        </div>
        <div class="col-6">
            <form action="{:url('category/index')}" method="post">
                <div class="form-group input-group">
                    <input type="text" class="form-control" name="key" placeholder="输入分类标题或者别名关键词搜索">
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
                <th>别名</th>
                <th width="250">操作</th>
            </tr>
        </thead>
        <tbody>
        <foreach name="model" item="v">
            <tr>
                <td>{$v.id}</td>
                <td>{$v.html} {$v.title}</td>
                <td>{$v.name}</td>

                <td>
                    <div class="btn-group" role="group" aria-label="Basic example">
                    <a class="btn btn-outline-dark btn-sm" href="{:url('category/add',array('pid'=>$v['id']))}"><i class="ion-plus"></i> 添加</a>
                    <a class="btn btn-outline-dark btn-sm" href="{:url('category/edit',array('id'=>$v['id']))}"><i class="ion-edit"></i> 编辑</a>
                    <a class="btn btn-outline-dark btn-sm" href="{:url('category/delete',array('id'=>$v['id']))}" onclick="javascript:return del('您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="ion-trash-a"></i> 删除</a>
                    </div>
                </td>
            </tr>
        </foreach>
        </tbody>
    </table>
</div>

</block>