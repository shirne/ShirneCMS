{extend name="public:base"/}

{block name="body"}
    {include file="public/bread" menu="page_index" title="单页详情"/}

    <div id="page-wrapper">
        <div class="page-header">{$id>0?'编辑':'添加'}页面</div>
        <div id="page-content">
            <form method="post" class="page-form" action="" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="col form-group">
                        <label for="page-title">页面标题</label>
                        <input type="text" name="title" class="form-control" value="{$page.title}" id="page-title"
                               placeholder="输入单页标题">
                    </div>
                    <div class="col form-group">
                        <label for="vice_title">副标题</label>
                        <input type="text" name="vice_title" class="form-control" value="{$page.vice_title}"  >
                    </div>
                </div>
                <div class="form-row">
                    <div class="col form-group">
                        <label for="page-name">页面标识</label>
                        <input type="text" name="name" class="form-control" value="{$page.name}" id="page-name"
                               placeholder="输入标识,不能和其他页面标识重复">
                    </div>
                    <div class="col form-group">
                        <label for="sort">排序</label>
                        <input type="text" name="sort" class="form-control" value="{$page.sort}" >
                    </div>
                    <div class="col form-group">
                        <label for="group">分组</label>
                        <div class="input-group">
                        <input type="text" name="group" class="form-control" value="{$page.group}" placeholder="从右侧选择或填写一个新的分组" >
                            <select class="form-control" onchange="var val=$(this).val();if(val)this.form.group.value=val;">
                                <option value="">选择分组</option>
                                {volist name="groups" id="group"}
                                    <option value="{$group.group}">{$group.group_name}</option>
                                {/volist}
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col form-group">
                        <label for="upload_icon">图标</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="upload_icon"/>
                                <label class="custom-file-label" for="upload_icon">选择文件</label>
                            </div>
                        </div>
                        {if !empty($page['icon'])}
                            <figure class="figure">
                                <img src="{$page.icon}" class="figure-img img-fluid rounded" alt="image">
                                <figcaption class="figure-caption text-center">{$page.icon}</figcaption>
                            </figure>
                            <input type="hidden" name="delete_icon" value="{$page.icon}"/>
                        {/if}
                    </div>
                    <div class="col form-group">
                        <label for="upload_image">封面图</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="upload_image"/>
                                <label class="custom-file-label" for="upload_image">选择文件</label>
                            </div>
                        </div>
                        {if !empty($page['image'])}
                            <figure class="figure">
                                <img src="{$page.image}" class="figure-img img-fluid rounded" alt="image">
                                <figcaption class="figure-caption text-center">{$page.image}</figcaption>
                            </figure>
                            <input type="hidden" name="delete_image" value="{$page.image}"/>
                        {/if}
                    </div>
                </div>
                <div class="form-row">
                    <label class="col-md-1">独立模板</label>
                    <div class="form-group col-md-2">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-outline-secondary{$page['use_template']==1?' active':''}">
                                <input type="radio" name="use_template" value="1" autocomplete="off" {$page['use_template']==1?' checked':''}> 是
                            </label>
                            <label class="btn btn-outline-secondary{$page['use_template']==0?' active':''}">
                                <input type="radio" name="use_template" value="0" autocomplete="off"{$page['use_template']==0?' checked':''}> 否
                            </label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-text text-muted">独立模板编写[别名].tpl放在“分组标识”(如果分组有开启独立模板)或page目录下，参考page/index.tpl</div>
                    </div>
                </div>
                <div class="form-row">
                    <label class="col-md-1">页面状态</label>
                    <div class="form-group col-md-2">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-outline-secondary{$page['status']==1?' active':''}">
                                <input type="radio" name="status" value="1" autocomplete="off" {$page['status']==1?' checked':''}> 显示
                            </label>
                            <label class="btn btn-outline-secondary{$page['status']==2?' active':''}">
                                <input type="radio" name="status" value="0" autocomplete="off"{$page['status']==2?' checked':''}> 隐藏
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="p-content">单页内容</label>
                    <script id="p-content" name="content" type="text/plain">{$page.content|raw}</script>
                </div>
                <div class="form-group submit-btn">
                    <input type="hidden" name="id" value="{$page.id}">
                    <button type="submit" class="btn btn-primary">{$id>0?'保存':'添加'}</button>
                </div>
            </form>
        </div>
    </div>
{/block}
{block name="script"}
    <!-- 配置文件 -->
    <script type="text/javascript" src="__STATIC__/ueditor/ueditor.config.js"></script>
    <!-- 编辑器源码文件 -->
    <script type="text/javascript" src="__STATIC__/ueditor/ueditor.all.min.js"></script>
    <!-- 实例化编辑器 -->
    <script type="text/javascript">
        var ue = UE.getEditor('p-content', {
            toolbars: Toolbars.normal,
            initialFrameHeight: 500,
            zIndex: 100
        });
    </script>
{/block}