<extend name="public:base" />

<block name="body">
<include file="public/bread" menu="notice_index" title="公告列表" />

<div id="page-wrapper">
    
    <div class="row list-header">
        <div class="col-6">
            <a href="{:url('Notice/add')}" class="btn btn-outline-primary btn-sm"><i class="ion-md-add"></i> 添加公告</a>
        </div>
        <div class="col-6">
            <form action="{:url('Notice/index')}" method="post">
                <div class="form-group input-group input-group-sm">
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
                <th>标题</th>
                <th>地址</th>
                <th>状态</th>
                <th width="200">操作</th>
            </tr>
        </thead>
        <tbody>
        <foreach name="lists" item="v">
            <tr>
                <td>{$v.id}</td>
                <td>{$v.title}</td>
                <td>{$v.url}</td>
                <td><if condition="$v['status']">显示<else/>隐藏</if></td>
                <td>
                    <a class="btn btn-outline-dark btn-sm" href="{:url('Notice/edit',array('id'=>$v['id']))}"><i class="ion-md-create"></i> 编辑</a>
                    <a class="btn btn-outline-dark btn-sm" href="{:url('Notice/delete',array('id'=>$v['id']))}" onclick="javascript:return del('您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="ion-md-trash"></i> 删除</a>
                </td>
            </tr>
        </foreach>
        </tbody>
    </table>
    {$page|raw}
</div>

</block>