<extend name="public:base" />

<block name="body">

    <include file="public/bread" menu="wechat_index" title="文章图集" />

    <div id="page-wrapper">
        <div class="page-header">{$id>0?'编辑':'添加'}回复消息</div>
        <div class="page-content">
            <form method="post" action="" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="col lform-group">
                        <label for="title">名称</label>
                        <input type="text" name="title" class="form-control" value="{$model.title}" placeholder="名称">
                    </div>
                    <div class="col form-group">
                        <label for="image">优先级</label>
                        <input type="text" name="sort" class="form-control" value="{$model.sort}" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="text">类型</label>
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <foreach name="types" id="type" key="key">
                        <label class="btn btn-outline-secondary {$key==$model['type']?'active':''}">
                            <input type="radio" name="type" value="{$key}" autocomplete="off" {$key==$model['type']?'checked':''}> {$type}
                        </label>
                        </foreach>
                    </div>
                    <div class="text-muted type-tip"></div>
                </div>
                <div class="form-group keyword-row">
                    <label for="text">关键字</label>
                    <input type="text" name="keyword" class="form-control" value="{$model.keyword}" placeholder="关键字">
                </div>
                <div class="form-group">
                    <label for="text">回复类型</label>
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <foreach name="reply_types" id="type" key="key">
                            <label class="btn btn-outline-secondary {$key==$model['reply_type']?'active':''}">
                                <input type="radio" name="reply_type" value="{$key}" autocomplete="off" {$key==$model['reply_type']?'checked':''}> {$type}
                            </label>
                        </foreach>
                    </div>
                </div>
                <div class="form-group rtype-row rtype-text">
                    <label for="text">回复内容</label>
                    <div>
                        <script id="reply-content" name="content" type="text/plain">{$reply.content|raw}</script>
                    </div>
                </div>
                <div class="form-group rtype-row rtype-article">
                    <label for="text">回复内容</label>
                    <div style="width:480px;">
                        <div class="list-group news-group">
                            <foreach name="$model['news']" id="news" key="k">
                                <if condition="$k EQ 0">
                                    <a href="{$news.Url}" class="list-group-item list-group-first" target="_blank" style="background-image:url({$news.PicUrl})">
                                        <div class="text-danger delbtn"><i class="ion-md-remove-circle"></i></div>
                                        <h3>{$news.Title}</h3>
                                        <div class="text-muted">{$news.Description}</div>
                                    </a>
                                    <else/>
                                    <a href="{$news.Url}" class="list-group-item" target="_blank">
                                        <div class="text-danger delbtn"><i class="ion-md-remove-circle"></i></div>
                                        <h3>{$news.Title}</h3>
                                        <if condition="!empty($news['PicUrl'])">
                                            <div class="imgbox" style="background-image:url({$news.PicUrl})">
                                                <img src="{$news.PicUrl}" />
                                            </div>
                                        </if>
                                    </a>
                                </if>
                            </foreach>
                            <if condition="count($model['news']) LT 8">
                                <a href="{$news.Url}" class="list-group-item list-group-add">
                                    <h3><i class="ion-md-add"></i> 添加</h3>
                                </a>
                            </if>
                        </div>
                    </div>
                </div>
                <div class="form-group rtype-row rtype-image">
                    <label for="text">回复图片</label>
                    <div>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="upload_image"/>
                                <label class="custom-file-label" for="upload_image">选择文件</label>
                            </div>
                        </div>
                        <if condition="$model['data']['image']">
                            <figure class="figure">
                                <img src="{$model['data']['image']}" class="figure-img img-fluid rounded" alt="image">
                                <figcaption class="figure-caption text-center">{$model['data']['image']}</figcaption>
                            </figure>
                            <input type="hidden" name="delete_image" value="{$model['data']['image']}"/>
                        </if>
                    </div>
                </div>
                <div class="form-group">
                    <input type="hidden" name="wechat_id" value="{$model.wechat_id}">
                    <button type="submit" class="btn btn-primary">{$id>0?'保存':'添加'}</button>
                </div>
            </form>
        </div>
    </div>
</block>
<block name="script">
    <script type="text/javascript" src="__STATIC__/ueditor/ueditor.config.js"></script>
    <script type="text/javascript" src="__STATIC__/ueditor/ueditor.all.min.js"></script>
    <script type="text/javascript">
        jQuery(function ($) {
            var tips={
                'subscribe':'用户关注时自动发送的消息，如果没有设置则回复默认消息',
                'resubscribe':'用户取消关注后再次关注时回复的消息，没有设置则回复关注消息',
                'default':'用户关注或发送消息后没有匹配到对应的回复信息时回复的消息',
                'click':'关联在自定义菜单中的回复'
            };
            var ue = UE.getEditor('reply-content',{
                toolbars: Toolbars.simple,
                initialFrameHeight:500,
                zIndex:100
            });
            $('[name=type]').change(function (e) {
                var curtype=this.value;
                if(curtype=='keyword'){
                    $('.keyword-row').show();
                    $('[name=keyword]').prop('readonly',false);
                }else if(curtype=='click'){
                    $('.keyword-row').show();
                    $('[name=keyword]').prop('readonly',true);
                }else{
                    $('.keyword-row').hide();
                }
                var msg='';
                if(tips[curtype]){
                    msg=tips[curtype];
                }
                $('.type-tip').html(msg);
            }).filter(':checked').trigger('change');
            $('[name=reply_type]').change(function (e) {
                var rtype=this.value;
                $('.rtype-row').hide();
                $('.rtype-'+rtype).show();
            }).filter(':checked').trigger('change');
            $('.list-group-add').click(function (e) {
                e.stopPropagation();
                e.preventDefault();
                dialog.action(['从文章添加','从产品添加','从素材添加','自定义内容'],function (idx) {
                    console.log(idx);
                    switch (idx){
                        case 0:
                            selectArticle();
                            break;
                        case 1:
                            selectProduct();
                            break;
                        case 2:
                            selectMaterial();
                            break;
                        case 3:
                            createArticle();
                            break;
                        default:

                            break;
                    }
                });
            });
            function selectArticle(){
                dialog.pickArticle("{:url('admin/index/searchArticle')}",function (article) {
                    console.log(article)
                });
            }
            function selectProduct(){
                dialog.pickProduct("{:url('admin/index/searchProduct')}",function (product) {
                    console.log(product)
                });
            }
            function selectMaterial(){
                dialog.pickList("{:url('admin/index/searchMaterial')}",function (article) {
                    console.log(article)
                });
            }
            function createArticle(){

            }
            $('.news-group').bind('click','.delbtn',function (e) {

            })
        });
    </script>
</block>