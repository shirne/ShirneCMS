<extend name="public:base"/>

<block name="body">
    <div class="weui-panel weui-panel_access">
        <div class="weui-panel__hd">{$category.title|default='新闻中心'}</div>
        <div class="weui-panel__bd">
            <php>$empty='<li class="col-12 empty">暂时没有内容</li>';</php>
            <Volist name="lists" id="article" empty="$empty">
            <a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg">
                <if condition="!empty($article['cover'])">
                <div class="weui-media-box__hd">
                    <img class="weui-media-box__thumb" src="{$article.cover}" alt="{$article.title}">
                </div>
                </if>
                <div class="weui-media-box__bd">
                    <h4 class="weui-media-box__title">{$article.title}</h4>
                    <p class="weui-media-box__desc">{$article.content|cutstr=80}</p>
                    <ul class="weui-media-box__info">
                        <li class="weui-media-box__info__meta">{$article.category_title}</li>
                        <li class="weui-media-box__info__meta"><i class="ion-md-calendar"></i> {$article.create_time|showdate}</span></li>
                        <li class="weui-media-box__info__meta weui-media-box__info__meta_extra"></li>
                    </ul>
                </div>
            </a>
            </Volist>
        </div>
        <div class="weui-panel__ft">
            <a href="javascript:void(0);" class="weui-cell weui-cell_access weui-cell_link">
                <div class="weui-cell__bd">查看更多</div>
                <span class="weui-cell__ft"></span>
            </a>
        </div>
    </div>
</block>
<block name="script">
    <script type="text/javascript">

    </script>
</block>