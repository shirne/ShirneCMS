<extend name="public:base" />

<block name="body">
    <include file="public/bread" menu="member_money_log" title="" />

    <div id="page-wrapper">
        <div class="row list-header">
            <div class="col-md-12">
                <form action="{:url('member/money_log',searchKey('fromdate,todate',''))}" class="form-inline" method="post">
                    <div class="btn-group">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {$fields[$field]} <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu">
                            <foreach name="fields" item="t" key="k">
                                <a class="dropdown-item" href="{:url('money_log',searchKey('field',$k))}">{$t}</a>
                            </foreach>
                        </div>
                    </div>
                    <div class="input-group date-range ml-3">
                        <div class="input-group-prepend"><span class="input-group-text">时间范围</span></div>
                        <input type="text" class="form-control" name="fromdate" value="{$fromdate}">
                        <div class="input-group-middle"><span class="input-group-text">-</span></div>
                        <input type="text" class="form-control" name="todate" value="{$todate}">
                        <div class="input-group-append">
                          <button class="btn btn-outline-dark" type="submit"><i class="ion-md-search"></i></button>
                        </div>
                    </div>
                    <if condition="$id">
                        <div class="btn-group ml-3">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">会员: {$member.username}<span class="caret"></span>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{:url('money_log',searchKey('id',0))}">不限会员</a>
                            </div>
                        </div>
                    </if>
                    <if condition="$from_id">
                        <div class="btn-group ml-3">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">来源: {$from_member.username}<span class="caret"></span>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{:url('money_log',searchKey('from_id',0))}">不限来源</a>
                            </div>
                        </div>
                    </if>
                    <div class="btn-group ml-3">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {$types[$type]} <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu">
                            <foreach name="types" item="t" key="k">
                                <a class="dropdown-item" href="{:url('money_log',searchKey('type',$k))}">{$t}</a>
                            </foreach>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <table class="table table-hover table-bordered">
            <thead>
            <tr>
                <th class="text-center">\</th>
                <foreach name="types" item="t" key="k">
                    <if condition="$k NEQ 'all'">
                    <th>{$t}</th>
                    </if>
                </foreach>
                <th>合计</th>
            </tr>
            <tbody>
                <foreach name="fields" item="f" key="fk">
                    <if condition="$fk NEQ 'all'">
                    <tr>
                        <th>{$f}</th>
                        <foreach name="types" item="t" key="tk">
                            <if condition="$tk NEQ 'all'">
                            <td>{$statics[$fk][$tk]|showmoney}</td>
                            </if>
                        </foreach>
                        <td>{$statics[$fk]['sum']|showmoney}</td>
                    </tr>
                    </if>
                </foreach>
            </tbody>
            </thead>
        </table>
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <th width="50">编号</th>
                <th>用户名</th>
                <th>金额</th>
                <th>来源</th>
                <th>时间</th>
                <th>备注</th>
                <th width="70"></th>
            </tr>
            </thead>
            <tbody>
            <foreach name="logs" item="v">
                <tr>
                    <td>{$v.id}</td>
                    <td>
                        <if condition="$v['member_id']">
                            <a href="{:url('money_log',array('id'=>$v['member_id'],'fromdate'=>$fromdate,'todate'=>$todate,'from_id'=>$from_id,'type'=>$type))}" class="media">
                                <if condition="!empty($v['avatar'])">
                                    <img src="{$v.avatar}" class="mr-2 rounded" width="30"/>
                                </if>
                                <div class="media-body">
                                    <h5 class="mt-0 mb-1" style="font-size:13px;">
                                        <if condition="!empty($v['nickname'])">
                                            {$v.nickname}
                                            <else/>
                                            {$v.username}
                                        </if>
                                    </h5>
                                    <div style="font-size:12px;">
                                        [{$v.member_id} {$levels[$v['level_id']]['level_name']}]
                                    </div>
                                </div>
                            </a>
                            <else/>
                            -
                        </if>
                    </td>
                    <td class="{$v['amount']>0?'text-success':'text-danger'}">{$v.field|money_type|raw}&nbsp;{$v.amount|showmoney}</td>
                    <td>
                        <if condition="$v['from_member_id']">
                            <a href="{:url('money_log',array('id'=>$id,'fromdate'=>$fromdate,'todate'=>$todate,'from_id'=>$v['from_member_id'],'type'=>$type))}" class="media">
                                <if condition="!empty($v['from_avatar'])">
                                    <img src="{$v.from_avatar}" class="mr-2 rounded" width="30"/>
                                </if>
                                <div class="media-body">
                                    <h5 class="mt-0 mb-1" style="font-size:13px;">
                                        <if condition="!empty($v['from_nickname'])">
                                            {$v.from_nickname}
                                            <else/>
                                            {$v.from_username}
                                        </if>
                                    </h5>
                                    <div style="font-size:12px;">
                                        [{$v.from_member_id} {$levels[$v['from_level_id']]['level_name']}]
                                    </div>
                                </div>
                            </a>
                            <else/>
                            -
                        </if>
                    </td>
                    <td>{$v.create_time|showdate}</td>
                    <td>{$v.reson}</td>
                    <td>

                    </td>
                </tr>
            </foreach>
            </tbody>
        </table>
        {$page|raw}
    </div>

</block>