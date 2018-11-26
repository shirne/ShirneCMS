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
                <div class="form-group rtype-row rtype-news">
                    <label for="text">回复内容</label>
                    <div style="width:480px;">
                        <div class="list-group news-group">
                            <foreach name="$model['news']" id="news" key="k">
                                <if condition="$k EQ 0">
                                    <a href="{$news.url}" class="list-group-item list-group-first" target="_blank" style="background-image:url({$news.image})">
                                        <div class="text-danger delbtn"><i class="ion-md-remove-circle"></i></div>
                                        <h3>{$news.title}</h3>
                                        <input type="hidden" name="news[{$k}][title]" value="{$news.title}"/>
                                        <input type="hidden" name="news[{$k}][url]" value="{$news.url}"/>
                                        <input type="hidden" name="news[{$k}][image]" value="{$news.image}"/>
                                        <input type="hidden" name="news[{$k}][description]" value="{$news.description}"/>
                                        <div class="text-muted">{$news.description}</div>
                                    </a>
                                    <else/>
                                    <a href="{$news.url}" class="list-group-item" target="_blank">
                                        <div class="text-danger delbtn"><i class="ion-md-remove-circle"></i></div>
                                        <input type="hidden" name="news[{$k}][title]" value="{$news.title}"/>
                                        <input type="hidden" name="news[{$k}][url]" value="{$news.url}"/>
                                        <input type="hidden" name="news[{$k}][image]" value="{$news.image}"/>
                                        <input type="hidden" name="news[{$k}][description]" value="{$news.description}"/>
                                        <h3>{$news.title}</h3>
                                        <if condition="!empty($news['image'])">
                                            <div class="imgbox" style="background-image:url({$news.image})">
                                                <img src="{$news.image}" />
                                            </div>
                                        </if>
                                    </a>
                                </if>
                            </foreach>
                            <if condition="empty($model['news']) OR count($model['news']) LT 8">
                                <a href="javascript:" class="list-group-item list-group-add">
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
                    <button type="submit" class="btn btn-primary">{$model['id']>0?'保存':'添加'}</button>
                </div>
            </form>
        </div>
    </div>
</block>
<block name="script">
    <script type="text/plain" id="firstArticleTpl">
        <a href="{@url}" class="list-group-item list-group-first" target="_blank" style="background-image:url({@image})">
            <div class="text-danger delbtn"><i class="ion-md-remove-circle"></i></div>
            <div class="hiddenbox">
                <input type="hidden" name="news[{@k}][title]" value="{@title}"/>
                <input type="hidden" name="news[{@k}][url]" value="{@url}"/>
                <input type="hidden" name="news[{@k}][image]" value="{@image}"/>
                <input type="hidden" name="news[{@k}][description]" value="{@description}"/>
            </div>
            <h3>{@title}</h3>
            <div class="text-muted">{@description}</div>
        </a>
    </script>
    <script type="text/plain" id="normalArticleTpl">
        <a href="{@url}" class="list-group-item" target="_blank">
            <div class="text-danger delbtn"><i class="ion-md-remove-circle"></i></div>
            <input type="hidden" name="news[{@k}][title]" value="{@title}"/>
            <input type="hidden" name="news[{@k}][url]" value="{@url}"/>
            <input type="hidden" name="news[{@k}][image]" value="{@image}"/>
            <input type="hidden" name="news[{@k}][description]" value="{@description}"/>
            <h3>{@title}</h3>
            {if @image}
                <div class="imgbox" style="background-image:url({@image})">
                    <img src="{@image}" />
                </div>
            {/if}
        </a>
    </script>
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
            var addhtml=$('.list-group-add').prop('outerHTML');
            $('.list-group-add').click(addbtnEvent);
            function addbtnEvent(e) {
                e.stopPropagation();
                e.preventDefault();
                dialog.action(['从文章添加','从产品添加','从素材添加','自定义内容'],function (idx) {
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
            }
            function selectArticle(){
                dialog.pickArticle(function (article) {
                    createArticle(
                        article.title,
                        article.cover,
                        article.description,
                        get_view_url('article',article.id)
                    )
                });
            }
            function selectProduct(){
                dialog.pickProduct(function (product) {
                    createArticle(
                        product.title,
                        product.image,
                        '￥'+product.min_price+(product.max_price>product.min_price?('~'+product.max_price):''),
                        get_view_url('product',product.id)
                    )
                });
            }
            function selectMaterial(){
                dialog.alert('暂不支持');
            }
            var newsCount=0;
            function createArticle(title,img,description,url){
                var boxgroup=$('.news-group');
                var sons=boxgroup.find('.list-group-item');
                if(sons.length>4){
                    sons.eq(-1).remove();
                }
                var newson='';
                var data={
                    'k'           : newsCount++,
                    'title'       : title,
                    'description' : description,
                    'url'         : url,
                    'image'       : img
                };
                if(sons.length<2){
                    newson=$('#firstArticleTpl').text().compile(data);
                }else{
                    newson=$('#normalArticleTpl').text().compile(data);
                }
                var addbtn=boxgroup.find('.list-group-add');
                if(addbtn.length>0){
                    addbtn.before(newson);
                }else{
                    boxgroup.append(newson);
                }

                setBind();
            }
            function setBind() {
                $('.news-group .delbtn').unbind('click').bind('click',function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var parent=$(this).parents('.list-group-item');
                    dialog.confirm('确定要删除该条？',function () {
                        parent.remove();
                        var boxgroup=$('.news-group');
                        var addbtn=boxgroup.find('.list-group-add');

                        if(addbtn.length<1){
                            boxgroup.append(addhtml);
                            boxgroup.find('.list-group-add').click(addbtnEvent);
                        }
                    })
                })
            }
            setBind();
        });
    </script>
</block>