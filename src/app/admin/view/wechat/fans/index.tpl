{extend name="public:base" /}

{block name="body"}
    {include  file="public/bread" menu="wechat_index" title="" /}

    <div id="page-wrapper">
        <div class="row list-header">
            <div class="col col-6">
                <div class="btn-toolbar list-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                    {if $support_sync}
                    <a href="{:url('wechat.fans/sync',['wid'=>$wid])}" class="btn btn-outline-info btn-sm mr-2"><i class="ion-md-sync"></i> 同步粉丝</a>
                    {/if}
                </div>
            </div>
            <div class="col col-6">
                <form action="{:url('wechat.fans/index')}" method="post">
                    <div class="form-row">
                        <div class="form-group col input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">关键字</span>
                            </div>
                            <input type="text" class="form-control" value="{$keyword|default=''}" name="keyword" placeholder="填写会员id或昵称">
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
            {php}$empty=list_empty(6);{/php}
            {volist name="lists" id="v" empty="$empty"}
                <tr>
                    <td><input type="checkbox" name="id" value="{$v.id}" /></td>
                    <td>
                        {if !empty($v['avatar'])}
                            <div class="avatar float-left rounded-circle" style="width: 50px;height: 50px;background-image:url({$v.avatar});background-size:100%;"></div>
                        {/if}
                        <div class="float-left pl-2" style="white-space: nowrap">
                            {$v.nickname}
                            {if $v['gender'] EQ 2}<i class="ion-md-female text-danger" ></i>{else/}<i class="ion-md-male text-success" ></i>{/if}<br />
                            {$v.openid}
                        </div>
                    </td>
                    <td>[{$v.member_id}]{$v.username}</td>
                    <td>{$v.subscribe_time|showdate}<br />{$v.create_time|showdate}</td>
                    <td>
                        <span class="badge badge-{$levels[$v['level_id']]['style']}">{$levels[$v['level_id']]['level_name']}</span>
                    </td>
                    <td class="operations">
                        {if $support_message}
                        <a class="btn btn-outline-primary sendmsg" title="发消息" href="javascript:" data-openid="{$v.openid}"><i class="ion-md-text"></i> </a>
                        {/if}
                        {if $support_sync}
                        <a class="btn btn-outline-primary" title="同步" href="{:url('wechat.fans/sync',array('openid'=>$v['openid'],'single'=>1))}"><i class="ion-md-sync"></i> </a>
                        {/if}
                    </td>
                </tr>
            {/volist}
            </tbody>
        </table>
        {$page|raw}
    </div>

{/block}
{block name="script"}
    <script type="text/javascript">
        jQuery(function ($) {
            $('.sendmsg').click(function (e) {
                var openid=$(this).data('openid');
                dialog.action([
                    '发送文本消息',
                    '发送文章',
                    '发送产品',
                    '发送素材'
                ],function (type) {
                    if(type==0){
                        dialog.prompt('请填写发送内容',function (text) {
                            if(text){
                                sendMessage(openid,'text',text)
                            }
                        })
                    }else if(type==1){
                        dialog.pickArticle(function (article) {
                            sendMessage(openid,'news',{
                                title:article.title,
                                description:article.description,
                                image:article.cover,
                                url:"{:url('index/article/view',['id'=>'__ID__'])}".replace('__ID__',article.id)
                            });
                        })
                    }else if(type==2){
                        dialog.pickProduct(function (product) {
                            sendMessage(openid,'news',{
                                title:product.title,
                                description:product.description,
                                image:product.image,
                                url:"{:url('index/product/view',['id'=>'__ID__'])}".replace('__ID__',article.id)
                            });
                        })
                    }else if(type==3){
                        dialog.pickList({
                            url:"{:url('wechat.material/search')}",
                            title:'素材类型',
                            htmlRow:'<option value="{@id}">{@title}</option>',
                            list:[
                                { id: 'image',title:'图片'},
                                { id: 'video',title:'视频'},
                                { id: 'voice',title:'语音'},
                                { id: 'news',title:'图文'},
                            ]
                        },function (material) {
                            sendMessage(openid,'media',{media_id:material.media_id,type:material.type});
                        })
                    }
                })
            })
            function sendMessage(openid,type,content){
                var dlg=dialog.loading('正在发送')
                $.ajax({
                    url:"{:url('wechat.fans/sendmsg')}",
                    data:{
                        openid:openid,
                        msgtype:type,
                        content:content
                    },
                    dataType:'json',
                    success:function (json) {
                        dlg.close()
                        if(json.code==1){
                            dialog.success(json.msg)
                        }else{
                            dialog.error(json.msg)
                        }
                    }
                })
            }

        })
    </script>
{/block}