<extend name="public:base" />
<block name="body">
    <div class="main-content">
        <div class="page-header"><h1>系统公告</h1></div>
        <foreach name="notices" item="v">
            <li class="list-group-item">
                <div>{$v.title} <span class="float_rigght">{$v.create_at|showdate}</span></div>
                <div>
                    {$v.content}
                </div>
            </li>
        </foreach>
    </div>
</block>