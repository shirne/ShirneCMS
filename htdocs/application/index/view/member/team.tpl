<extend name="public:base" />
<block name="body">
    <div class="container">
        <div class="page-header"><h1>我的会员</h1></div>
        <if condition="!empty($paths)">
            <ol class="breadcrumb">
                <foreach name="paths" item="v">
                    <li><a href="{:url('index/member/team',array('pid'=>$v['id']))}">{$v.username}</a></li>
                </foreach>
            </ol>
        </if>
        <ul class="list-group">
            <li class="row list-group-item">
                <div class="col-xs-5">会员/等级</div>
                <div class="col-xs-2">下级</div>
                <div class="col-xs-3">注册日期</div>
                <div class="col-xs-2">排位</div>
            </li>
            <foreach name="users" item="v">
            <li class="row list-group-item">
                <a href="{:url('team',array('pid'=>$v['id']))}">
                    <div class="col-xs-5"><i class="fa fa-user"></i> {$v['username']}<br />{$levels[$v['level_id']]['level_name']}</div>
                    <div class="col-xs-2"><if condition="$soncounts[$v['id']]">{$soncounts[$v['id']]}<else/>0</if></div>
                <div class="col-xs-3"><span style="color: #999;">{$v.create_at|showdate}</span> </div>
                <div class="col-xs-2">
                    <if condition="$v['position']">
                        <span class="label label-info">{$v['position']}</span>
                        <else/>
                        <span class="label label-default">未排位</span>
                    </if>
                </div>
                </a>
            </li>
            </foreach>
        </ul>
        {$page|raw}
    </div>
</block>