<extend name="public:base" />

<block name="body">
<include file="public/bread" menu="member_index" title=""/>

<div id="page-wrapper">
    <div class="row list-header">
        <div class="col col-6">
            <div class="btn-toolbar list-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                <div class="btn-group btn-group-sm mr-2" role="group" aria-label="check action group">
                    <a href="javascript:" class="btn btn-outline-secondary checkall-btn" data-toggle="button" aria-pressed="false">全选</a>
                    <a href="javascript:" class="btn btn-outline-secondary checkreverse-btn">反选</a>
                </div>
                <div class="btn-group btn-group-sm mr-2" role="group" aria-label="action button group">
                    <a href="javascript:" class="btn btn-outline-secondary action-btn" data-action="enable">启用</a>
                    <a href="javascript:" class="btn btn-outline-secondary action-btn" data-action="disable">禁用</a>
                </div>
                <a href="{:url('member/statics')}" class="btn btn-outline-info btn-sm mr-2">会员统计</a>
                <a href="{:url('member/add')}" class="btn btn-outline-primary btn-sm">添加会员</a>
            </div>
        </div>
        <div class="col col-6">
            <form action="{:url('member/index')}" method="post">
                <div class="form-row">
                <div class="form-group col input-group input-group-sm">
                    <div class="input-group-prepend">
                    <span class="input-group-text">上级</span>
                    </div>
                    <input type="text" class="form-control" name="referer" placeholder="填写id或会员名" value="{$referer}">
                </div>
                <div class="form-group col input-group input-group-sm">
                    <div class="input-group-prepend">
                    <span class="input-group-text">关键字</span>
                    </div>
                    <input type="text" class="form-control" value="{$keyword}" name="keyword" placeholder="输入用户名或者邮箱关键词搜索">
                    <div class="input-group-append">
                      <button class="btn btn-outline-secondary" type="submit"><i class="ion-md-search"></i></button>
                    </div>
                </div>
                </div>
            </form>
        </div>
    </div>
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th width="50">编号</th>
                <th>用户名</th>
                <th>手机/邮箱</th>
                <th>余额</th>
                <th>积分</th>
                <th>推荐人</th>
                <th>注册时间</th>
                <th>上次登陆</th>
                <th>代理</th>
                <th>状态</th>
                <th width="200">操作</th>
            </tr>
        </thead>
        <tbody>
        <foreach name="lists" item="v">
            <tr>
                <td><input type="checkbox" name="id" value="{$v.id}" /></td>
                <td>{$v.username}<br/>{$v.realname}</td>
                <td>{$v.mobile}<br />{$v.email}</td>
                <td>{$v.money|showmoney}</td>
                <td>{$v.credit}</td>
                <td>
                    <empty name="v.refer_name">
                        -
                        <else/>
                        {$v.refer_name}[{$v.refer_agent}]<br />
                        {$v.refer_realname}
                    </empty>
                </td>
                <td>{$v.create_time|showdate}</td>
                <td>{$v.login_ip}<br />{$v.logintime|showdate}</td>
                <td>
                    <if condition="0">
                    <if condition="$v.type eq 1"> <span class="label label-success">普通会员</span>
                    <elseif condition="$v.type eq 2"/><span class="label label-danger">VIP</span>
                    </if>
                    </if>

                    <if condition="$v.isagent neq 0">
                        <div class="btn-group">
                                <a class="btn btn-outline-dark btn-sm" href="{:url('member/cancel_agent',array('id'=>$v['id']))}" style="color:green;" onclick="javascript:return del('取消代理不能更改已注册的用户!!!');"><i class="ion-md-close"></i> 取消代理[{$v.isagent}]</a>
                            <button type="button" class="btn btn-sm btn-dark dropdown-toggle" data-toggle="dropdown">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <!--<li><a href="{:url('member/set_agent',array('id'=>$v['id'],'level'=>1))}">设为一级代理</a></li>-->
                                <li><a href="{:url('member/index',array('referer'=>$v['id']))}">查看下线</a></li>
                            </ul>
                        </div>
                    <else/>
                    <a class="btn btn-outline-dark btn-sm {$v.refer_agent>2?'disabled':''}" href="{:url('member/set_agent',array('id'=>$v['id']))}" ><i class="ion-md-check"></i> 设置代理[<php> echo $v['refer_agent']+1;</php>]</a>
                    </if>

                </td> 
                <td><if condition="$v.status eq 1">正常<else/><span style="color:red">禁用</span></if></td>
                <td>

                    <a class="btn btn-outline-dark btn-sm" href="{:url('member/update',array('id'=>$v['id']))}"><i class="ion-md-create"></i> 编辑</a>
                    <if condition="$v.status eq 1">
                        <a class="btn btn-outline-dark btn-sm" href="{:url('member/delete',array('id'=>$v['id'],'type'=>0))}" onclick="javascript:return del('禁用后用户将不能登陆!\n\n请确认!!!');"><i class="ion-md-close"></i> 禁用</a>
                    <else/>
                        <a class="btn btn-outline-dark btn-sm" href="{:url('member/delete',array('id'=>$v['id'],'type'=>1))}" style="color:#50AD1E;"><i class="ion-md-check"></i> 启用</a>
                    </if>
                </td>
            </tr>
        </foreach>
        </tbody>
    </table>
    {$page|raw}
</div>

</block>
<block name="script">
    <script type="text/javascript">
        (function(w){
            w.actionEnable=function(ids){
                dialog.confirm('确定将选中会员设置为正常状态？',function() {
                    $.ajax({
                        url:'{:url('member/delete',['id'=>'__id__','type'=>1])}'.replace('__id__',ids.join(',')),
                        type:'GET',
                        dataType:'JSON',
                        success:function(json){
                            if(json.code==1){
                                dialog.alert(json.msg,function() {
                                    location.reload();
                                });
                            }else{
                                toastr.warning(json.msg);
                            }
                        }
                    });
                });
            };
            w.actionDisable=function(ids){
                dialog.confirm('确定禁用选中会员？',function() {
                    $.ajax({
                        url:'{:url('member/delete',['id'=>'__id__','type'=>0])}'.replace('__id__',ids.join(',')),
                        type:'GET',
                        dataType:'JSON',
                        success:function(json){
                            if(json.code==1){
                                dialog.alert(json.msg,function() {
                                    location.reload();
                                });
                            }else{
                                toastr.warning(json.msg);
                            }
                        }
                    });
                });
            };
        })(window)
    </script>
</block>