<extend name="public:base" />
<block name="body">
    <div class="container">
        <div class="page-header"><h1>问题反馈</h1></div>
        <ul class="list-group">
        <foreach name="feedbacks" item="v">
            <li class="list-group-item">
                <div>{$v.title} <span class="float_rigght">{$v.create_at|showdate}</span></div>
                <div>
                    {$v.content}
                </div>
                <if condition="$v['reply_at'] GT 1">
                    <div>管理员回复：{$v.reply}</div>
                <else/>
                    <span class="label label-danger">待回复</span>
                </if>
            </li>
        </foreach>
        <if condition="$unreplyed LT 1">
        <li class="list-group-item">
            <form action="" method="post" class="form-horizontal container-fluid">
                <div class="form-group">{$user.username}:</div>
                <div class="form-group">
                    <textarea name="content" class="form-control" id="" cols="30" rows="5"></textarea>
                </div>
                <div class="form-group"><input type="submit" class="btn btn-primary" value="提交" /></div>
            </form>
        </li>
        </if>
        </ul>
        {$page|raw}
    </div>
</block>