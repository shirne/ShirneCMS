<extend name="Public:Base" />

<block name="body">

<include file="Public/bread" menu="adv_index" section="其它" title="广告管理" />

<div id="page-wrapper">
    
    <div class="row">
        <div class="col col-xs-6">
            <a href="{:url('adv/itemupdate',array('gid'=>$gid))}" class="btn btn-success">添加广告</a>
        </div>
        <div class="col col-xs-6">
            <form action="{:url('adv/itemlist')}" method="post">
                <div class="form-group input-group">
                    <input type="text" class="form-control" name="key" placeholder="输入标题或者地址关键词搜索">
                    <div class="input-group-append">
                      <button class="btn btn-outline-secondary" type="submit"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th width="50">编号</th>
                <th>图片</th>
                <th>标题</th>
                <th>链接</th>
                <th>有效期</th>
                <th>排序</th>
                <th>状态</th>
                <th width="150">操作</th>
            </tr>
        </thead>
        <tbody>
        <foreach name="lists" item="v">
            <tr>
                <td>{$v.id}</td>
                <td><img src="" width="80" height="60" /></td>
                <td>{$v.title}</td>
                <td>{$v.url}</td>
                <td>{$v.start_date|showdate}<br />{$v.end_date|showdate}</td>
                <td>{$v.sort}</td>
                <td>{$v.status|v_status}</td>
                <td>
                    <a class="btn btn-default btn-sm" href="{:url('adv/itemupdate',array('id'=>$v['id'],'gid'=>$gid))}"><i class="fa fa-edit"></i> 编辑</a>
                    <a class="btn btn-default btn-sm" href="{:url('adv/itemdelete',array('id'=>$v['id'],'gid'=>$gid))}" style="color:red;" onclick="javascript:return del('您真的确定要删除吗？\n\n删除后将不能恢复!');"><i class="fa fa-trash"></i> 删除</a>
                </td>
            </tr>
        </foreach>
        </tbody>
    </table>
    {$page}
</div>
</block>