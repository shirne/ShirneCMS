<extend name="public:base" />
<block name="header">
    <style type="text/css">
        span.form-control{
            height:auto;white-space: normal;word-break: break-all;
        }
        .page-content .img-fluid{
            max-width: 400px;
        }
    </style>
</block>
<block name="body">

    <include file="public/bread" menu="wechat_index" title="公众号素材" />
    <div id="page-wrapper">
        <div class="page-header"><a href="javascript:history.back()" class="btn btn-outline-primary btn-sm"><i class="ion-md-arrow-back"></i> 返回列表</a> 预览素材</div>
        <div class="page-content">
            <if condition="$media['type'] == 'image' || $media['type'] == 'thumb'">
            <img src="{$media.url}" class="img-fluid" alt="Responsive image">
            <div class="form-row">
                <div class="col lform-group">
                    <label for="title">素材id</label>
                    <span class="form-control" >{$media.media_id}</span>
                </div>
                <div class="col form-group">
                    <label for="image">素材标题</label>
                    <span class="form-control" >{$media.title}</span>
                </div>
                <div class="col form-group">
                    <label for="image">素材链接</label>
                    <span class="form-control" >{$media.url}</span>
                </div>
            </div>
            <elseif condition="$media['type'] == 'voice'"/>
            <div class="form-row">
                <div class="col lform-group">
                    <label for="title">素材id</label>
                    <span class="form-control" >{$media.media_id}</span>
                </div>
                <div class="col form-group">
                    <label for="image">素材标题</label>
                    <span class="form-control" >{$media.title}</span>
                </div>
                <div class="col form-group">
                    <label for="image">素材链接</label>
                    <span class="form-control" >{$media.url}</span>
                </div>
            </div>
            <elseif condition="$media['type'] == 'video'"/>
            <div class="form-row">
                <div class="col lform-group">
                    <label for="title">素材id</label>
                    <span class="form-control" >{$media.media_id}</span>
                </div>
                <div class="col form-group">
                    <label for="image">素材标题</label>
                    <span class="form-control" >{$media.title}</span>
                </div>
                <div class="col form-group">
                    <label for="image">素材链接</label>
                    <span class="form-control" >{$media.url}</span>
                </div>
            </div>
            <elseif condition="$media['type'] == 'news'"/>
            <div class="col lform-group">
                <label for="title">素材id</label>
                <span class="form-control" >{$media.media_id}</span>
            </div>
            <div class="col form-group">
                <label for="image">素材标题</label>
                <span class="form-control" >{$media.title}</span>
            </div>
            <div class="accordion" id="accordionArticle">
                <volist name="articles" id="v">
                <div class="card">
                  <div class="card-header" id="headingOne">
                    <h2 class="mb-0">
                      <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        {$v.title} {$v.author} 
                      </button>
                    </h2>
                  </div>
              
                  <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionArticle">
                    <div class="card-body">
                        <div class="col form-group">
                            <label for="image">素材链接</label>
                            <span class="form-control" >{$v.url}</span>
                        </div>
                        <img src="{:url('thumb',['media_id'=>$v['thumb_media_id']])}" class="img-fluid" alt="Responsive image">
                        <div>{$v.content|raw}</div>
                    </div>
                  </div>
                </div>
                </volist>
            </div>
            <else/>
            <div class="form-row">
                <div class="col lform-group">
                    <label for="title">素材id</label>
                    <span class="form-control" >{$media.media_id}</span>
                </div>
                <div class="col form-group">
                    <label for="image">素材标题</label>
                    <span class="form-control" >{$media.title}</span>
                </div>
                <div class="col form-group">
                    <label for="image">素材链接</label>
                    <span class="form-control" >{$media.url}</span>
                </div>
            </div>
            </if>
        </div>
    </div>
</block>
