{extend name="public:base" /}

{block name="body"}
    {include file="public/bread" menu="member_authen_index" title="升级申请" /}

    <div id="page-wrapper" class="page-form">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">会员信息</h3>
            </div>
            <div class="panel-body">
                <table class="table">
                    <tbody>
                    <tr>
                        <td colspan="4">
                            <div class="media">
                                {if !empty($member['avatar'])}
                                    <img src="{$member.avatar}" class="mr-2 rounded" width="60"/>
                                {/if}
                                <div class="media-body">
                                    <h5 class="text-nowrap mt-0 mb-1">
                                        [{$member.id}]
                                        {if !empty($member['nickname'])}
                                            {$member.nickname}
                                            {else/}
                                            {$member.username}
                                        {/if}
                                    </h5>
                                    <div style="font-size:12px;">
                                    {if !empty($member['realname'])}真实姓名：{$member.realname}{/if}
                                    注册时间：{$member.create_time|showdate}<br/>
                                        上次登陆：{$member.logintime|showdate} {if !empty($member['login_ip'])}[{$member.login_ip}]{/if}
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>会员等级</td>
                        <td>{$member->getLevelName()}</td>
                        <td>代理等级</td>
                        <td>{$member->getAgentName()}</td>
                    </tr>
                    <tr>
                        <td>直推人数</td>
                        <td>{$member.recom_count|number_format}</td>
                        <td>团队人数</td>
                        <td>{$member.team_count|number_format}</td>
                    </tr>
                    <tr>
                        <td>直推业绩</td>
                        <td>{$member.recom_performance|showmoney}</td>
                        <td>团队业绩</td>
                        <td>{$member.total_performance|showmoney}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">申请信息</h3>
            </div>
            <div class="panel-body">
                <form method="post">
                <table class="table">
                    <tbody>
                    <tr>
                        <td>真实姓名</td>
                        <td>{$model.realname}</td>
                        <td>手机号码</td>
                        <td>{$model.mobile}</td>
                    </tr>
                    <tr>
                        <td>所在省份</td>
                        <td>{$model.province}</td>
                        <td>所在城市</td>
                        <td>{$model.city}</td>
                    </tr>
                    <tr>
                        <th>审核状态</th>
                        <th colspan="3">
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-outline-success {$model['status']==1?"active":""}">
                                    <input type="radio" name="status" value="1"
                                           autocomplete="off" {$model['status']==1?"checked":""}> 通过
                                </label>
                                <label class="btn btn-outline-danger {$model['status']?"":"active"}">
                                    <input type="radio" name="status" value="0"
                                           autocomplete="off" {$model['status']?"":"checked"}> 驳回
                                </label>
                                <label class="btn btn-outline-warning {$model['status']==-1?"active":""}">
                                    <input type="radio" name="status" value="-1"
                                           autocomplete="off" {$model['status']==-1?"checked":""}> 审核中
                                </label>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="4">驳回备注</th>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <textarea class="form-control">
                                {$model.remark}
                            </textarea>
                        </td>
                    </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4">
                                <button type="submit" class="btn btn-primary">提交</button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                </form>
            </div>
        </div>
    </div>
{/block}
{block name="script"}
    <script type="text/javascript">
        jQuery(function($){
            

        })
    </script>
{/block}