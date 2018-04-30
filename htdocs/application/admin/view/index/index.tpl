<extend name="Public:Base" />

<block name="body">

<include file="Public/bread" menu="Board" section="主面板" title="主面板" />
<div id="page-wrapper">

    <div class="row">
        <div class="col-lg-3">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-6">
                            <i class="fa  fa-comment fa-5x"></i>
                        </div>
                        <div class="col-xs-6 text-right">
                            <p class="announcement-heading">{$stat.feedback}</p>
                            <p class="announcement-text">留言</p>
                        </div>
                    </div>
                </div>
                <a href="{:url('feedback/index')}">
                    <div class="panel-footer announcement-bottom">
                        <div class="row">
                            <div class="col-xs-6">
                                查看留言
                            </div>
                            <div class="col-xs-6 text-right">
                                <i class="fa fa-arrow-circle-right"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-6">
                            <i class="fa fa-users fa-5x"></i>
                        </div>
                        <div class="col-xs-6 text-right">
                            <p class="announcement-heading">{$stat.member}</p>
                            <p class="announcement-text">会员</p>
                        </div>
                    </div>
                </div>
                <a href="{:url('member/index')}">
                    <div class="panel-footer announcement-bottom">
                        <div class="row">
                            <div class="col-xs-6">
                                管理会员
                            </div>
                            <div class="col-xs-6 text-right">
                                <i class="fa fa-arrow-circle-right"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-6">
                            <i class="fa fa-edit fa-5x"></i>
                        </div>
                        <div class="col-xs-6 text-right">
                            <p class="announcement-heading">{$stat.post}</p>
                            <p class="announcement-text">文章</p>
                        </div>
                    </div>
                </div>
                <a href="{:url('post/index')}">
                    <div class="panel-footer announcement-bottom">
                        <div class="row">
                            <div class="col-xs-6">
                                管理文章
                            </div>
                            <div class="col-xs-6 text-right">
                                <i class="fa fa-arrow-circle-right"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-6">
                            <i class="fa fa-link fa-5x"></i>
                        </div>
                        <div class="col-xs-6 text-right">
                            <p class="announcement-heading">{$stat.links}</p>
                            <p class="announcement-text">链接</p>
                        </div>
                    </div>
                </div>
                <a href="{:url('links/index')}">
                    <div class="panel-footer announcement-bottom">
                        <div class="row">
                            <div class="col-xs-6">
                                管理链接
                            </div>
                            <div class="col-xs-6 text-right">
                                <i class="fa fa-arrow-circle-right"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div><!-- /.row -->

    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">会员统计</h3>
                </div>
                <div class="panel-body">
                    <table class="table table-striped">
                        <tr>
                            <th width="80">总会员</th>
                            <td>{$mem.total}</td>
                        </tr>
                        <tr>
                            <th width="80">正常会员</th>
                            <td>{$mem.avail}</td>
                        </tr>
                        <tr>
                            <th width="80">总代理数</th>
                            <td>{$mem.agent}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">资金统计</h3>
                </div>
                <div class="panel-body">
                    <table class="table table-striped">
                        <tr>
                            <th width="80">总充值</th>
                            <td>{$money.total_charge|showmoney}</td>
                        </tr>
                        <tr>
                            <th width="80">总提现</th>
                            <td>{$money.total_cash|showmoney}</td>
                        </tr>
                        <tr>
                            <th width="80">总余额</th>
                            <td>{$money.total_money|showmoney}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->

    <!-- /.row -->

</div>

</block>