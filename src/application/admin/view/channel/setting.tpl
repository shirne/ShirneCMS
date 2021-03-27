<extend name="public:base"/>

<block name="body">

    <include file="channel/_bread" title="频道设置"/>

    <div id="page-wrapper">
        <div class="page-header">频道设置</div>
        <div class="page-content">
            <form method="post" class="page-form" action="" enctype="multipart/form-data">
                <input type="hidden" name="channel_id" value="{$channel_id}" >
                <div class="form-row">
                    <div class="form-group col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">频道名称</span>
                            </div>
                            <input type="text" name="title" class="form-control" value="{$channel.title}" placeholder="输入频道名称"/>
                        </div>
                    </div>
                    <div class="form-group col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">简称</span>
                            </div>
                            <input type="text" name="short" class="form-control" value="{$channel.short}"/>
                        </div>
                    </div>
                    <div class="form-group col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">频道别名</span>
                            </div>
                        <input type="text" name="name" class="form-control" value="{$channel.name}" placeholder="输入频道别名,不能和其他频道别名重复">
                        </div>
                    </div>
                </div>
                <div class="form-row">
                <div class="form-group col">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">频道图标</span>
                        </div>
                        <div class="custom-file">
                        <input type="file" class="custom-file-input" name="upload_icon"/>
                            <label class="custom-file-label" for="upload_icon">选择文件</label>
                        </div>
                    </div>
                    <if condition="$channel['icon']">
                        <figure class="figure">
                            <img src="{$channel.icon}" class="figure-img img-fluid rounded" alt="icon">
                            <figcaption class="figure-caption text-center">{$channel.icon}</figcaption>
                        </figure>
                        <input type="hidden" name="delete_icon" value="{$channel.icon}"/>
                    </if>
                </div>
                <div class="form-group col">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">频道图片</span>
                        </div>
                        <div class="custom-file">
                        <input type="file" class="custom-file-input" name="upload_image"/>
                            <label class="custom-file-label" for="upload_image">选择文件</label>
                        </div>
                    </div>
                    <if condition="$channel['image']">
                        <figure class="figure">
                            <img src="{$channel.image}" class="figure-img img-fluid rounded" alt="image">
                            <figcaption class="figure-caption text-center">{$channel.image}</figcaption>
                        </figure>
                        <input type="hidden" name="delete_image" value="{$channel.image}"/>
                    </if>
                </div>
                </div>
                <div class="form-row">
                    <div class="form-group col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">排序</span>
                            </div>
                        <input type="text" name="sort" class="form-control" value="{$channel.sort}" placeholder="排序按从小到大">
                        </div>
                    </div>
                    <div class="form-group col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">分页</span>
                            </div>
                        <input type="text" name="pagesize" class="form-control" value="{$channel.pagesize}" placeholder="列表页分页数量">
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <label class="col-md-1">独立模板</label>
                    <div class="form-group col-md-2">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-outline-secondary{$channel['use_template']==1?' active':''}">
                                <input type="radio" name="use_template" value="1" autocomplete="off" {$channel['use_template']==1?' checked':''}> 是
                            </label>
                            <label class="btn btn-outline-secondary{$channel['use_template']==0?' active':''}">
                                <input type="radio" name="use_template" value="0" autocomplete="off"{$channel['use_template']==0?' checked':''}> 否
                            </label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-text text-muted">独立模板编写index.tpl及view.tpl放在“频道别名”目录下，参考article/index.tpl及view.tpl</div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-outline-primary{$channel['is_images']==1?' active':''}">
                                <input type="radio" name="is_images" value="1" autocomplete="off" {$channel['is_images']==1?' checked':''}> 有图集
                            </label>
                            <label class="btn btn-outline-secondary{$channel['is_images']==0?' active':''}">
                                <input type="radio" name="is_images" value="0" autocomplete="off"{$channel['is_images']==0?' checked':''}> 无图集
                            </label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-outline-primary{$channel['is_comments']==1?' active':''}">
                                <input type="radio" name="is_comments" value="1" autocomplete="off" {$channel['is_comments']==1?' checked':''}> 允许评论
                            </label>
                            <label class="btn btn-outline-secondary{$channel['is_comments']==0?' active':''}">
                                <input type="radio" name="is_comments" value="0" autocomplete="off"{$channel['is_comments']==0?' checked':''}> 无评论
                            </label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-outline-warning{$channel['is_single']==2?' active':''}">
                                <input type="radio" name="is_single" value="2" autocomplete="off" {$channel['is_single']==2?' checked':''}> 栏目
                            </label>
                            <label class="btn btn-outline-primary{$channel['is_single']==0?' active':''}">
                                <input type="radio" name="is_single" value="0" autocomplete="off"{$channel['is_single']==0?' checked':''}> 列表
                            </label>
                            <label class="btn btn-outline-secondary{$channel['is_single']==1?' active':''}">
                                <input type="radio" name="is_single" value="1" autocomplete="off" {$channel['is_single']==1?' checked':''}> 单页
                            </label>
                        </div>
                    </div>
                    <div class="col">
                        
                    </div>
                </div>
                <div class="form-group">
                    <label for="description">默认属性</label>
                    <div class="form-control">
                        <input type="text" class="taginput" value="{$channel.props|implode_cmp}" placeholder="填写多个值以,分割"  />
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">关键词</span>
                        </div>
                    <input type="text" name="keywords" class="form-control" value="{$channel.keywords}"
                           placeholder="请输入SEO关键词(选填)">
                    </div>
                </div>
                <div class="form-group">
                    <label for="description">描述信息</label>
                    <textarea name="description" cols="30" rows="10" class="form-control"
                              placeholder="请输入频道描述(选填)">{$channel.description}</textarea>
                </div>
                <div class="form-group submit-btn">
                    <input type="hidden" name="id" value="{$channel.id}">
                    <button type="submit" class="btn btn-primary">{$id>0?'保存':'添加'}</button>
                </div>
            </form>
        </div>
    </div>
</block>
<block name="script">
    <script type="text/javascript">
        jQuery(function($){
            $('.taginput').tags('props[]');
        })
    </script>
</block>