<extend name="public:base" />

<block name="body">
    <include file="public/bread" menu="wechat_index" title=""/>

    <div id="page-wrapper">
        <div class="row list-header">
            <div class="col col-6">
                <div class="btn-toolbar list-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                    <a href="{:url('wechat/syncfans',['wid'=>$wid])}" class="btn btn-outline-info btn-sm mr-2"><i class="ion-md-sync"></i> 同步粉丝</a>
                </div>
            </div>
            <div class="col col-6">
                <form action="{:url('wechat/fans')}" method="post">
                    <div class="form-row">
                        <div class="form-group col input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">关键字</span>
                            </div>
                            <input type="text" class="form-control" value="{$keyword}" name="keyword" placeholder="填写会员id或昵称">
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
                <th>粉丝</th>
                <th>会员</th>
                <th>关注时间</th>
                <th>状态</th>
                <th width="160">&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <foreach name="lists" item="v">
                <tr>
                    <td><input type="checkbox" name="id" value="{$v.id}" /></td>
                    <td>
                        <if condition="!empty($v['avatar'])">
                            <div class="avatar float-left rounded-circle" style="width: 50px;height: 50px;background-image:url({$v.avatar});background-size:100%;"></div>
                        </if>
                        <div class="float-left pl-2" style="white-space: nowrap">
                            {$v.nickname}
                            <if condition="$v['gender'] EQ 2"><i class="ion-md-female text-danger" ></i><else/><i class="ion-md-male text-success" ></i></if><br />
                            {$v.openid}
                        </div>
                    </td>
                    <td>[{$v.member_id}]{$v.username}</td>
                    <td>{$v.create_time|showdate}</td>
                    <td>
                        <span class="badge badge-{$levels[$v['level_id']]['style']}">{$levels[$v['level_id']]['level_name']}</span>
                    </td>
                    <td class="operations">
                        <a class="btn btn-outline-primary" title="同步" href="{:url('member/update',array('id'=>$v['id']))}"><i class="ion-md-sync"></i> </a>
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

    </script>
</block>