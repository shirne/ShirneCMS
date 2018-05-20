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
                                <a class="dropdown-item" href="{:url('money_log',searchKey('field',$k))}">{$t}</a></a>
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
                        <a href="{:url('money_log',searchKey('id',0))}" class="btn btn-outline-secondary">不限会员</a>
                        &nbsp;&nbsp;
                    </if>
                    <if condition="$from_id">
                        <a href="{:url('money_log',searchKey('from_id',0))}" class="btn btn-outline-secondary">不限来源</a>
                        &nbsp;&nbsp;
                    </if>
                    <div class="btn-group ml-3">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {$types[$type]} <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu">
                            <foreach name="types" item="t" key="k">
                                <a class="dropdown-item" href="{:url('money_log',searchKey('type',$k))}">{$t}</a></a>
                            </foreach>
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
                    <td><if condition="$v['member_id']">
                            <a href="{:url('money_log',array('id'=>$v['member_id'],'fromdate'=>$fromdate,'todate'=>$todate,'from_id'=>$from_id,'type'=>$type))}" >[{$v['member_id']} {$levels[$v['level_id']]['level_name']}]{$v['username']}</a>
                            <else/>
                            -
                        </if></td>
                    <td class="{$v['amount']>0?'text-success':'text-danger'}">{$v.amount|showmoney}</td>
                    <td>
                        <if condition="$v['from_member_id']">
                            <a href="{:url('money_log',array('id'=>$id,'fromdate'=>$fromdate,'todate'=>$todate,'from_id'=>$v['from_member_id'],'type'=>$type))}" >[{$v['from_member_id']} {$levels[$v['from_level_id']]['level_name']}]{$v['from_username']}</a>
                            <else/>
                            -
                        </if>
                    </td>
                    <td>{$v.create_at|showdate}</td>
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