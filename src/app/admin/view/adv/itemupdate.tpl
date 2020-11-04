{extend name="public:base" /}

{block name="body"}

{include  file="public/bread" menu="adv_index" title="广告资料"  /}

<div id="page-wrapper">
    <div class="page-header">{$id>0?'编辑':'添加'}广告</div>
    <div class="page-content">
    <form method="post" class="page-form" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">名称</label>
            <input type="text" name="title" class="form-control" value="{$model.title|default=''}" placeholder="名称">
        </div>
        {if $group['type'] == 1}
            <div class="form-row">
                <div class="form-group col">
                    <label for="image">预览图</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="upload_image"/>
                            <label class="custom-file-label" for="upload_image">选择文件</label>
                        </div>
                    </div>
                    {if !empty($model['image'])}
                        <figure class="figure">
                            <img src="{$model.image}" class="figure-img img-fluid rounded" alt="image">
                            <figcaption class="figure-caption text-center">{$model.image}</figcaption>
                        </figure>
                        <input type="hidden" name="delete_image" value="{$model.image}"/>
                    {/if}
                </div>
                <div class="form-group col">
                    <label for="video">视频</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="upload_video"/>
                            <label class="custom-file-label" for="upload_video">选择文件</label>
                        </div>
                    </div>
                    {if !empty($model['video'])}
                        <figure class="figure">
                            <video src="{$model.video}" controls class="figure-img img-fluid rounded" alt="video"></video>
                            <figcaption class="figure-caption text-center">{$model.video}</figcaption>
                        </figure>
                        <input type="hidden" name="delete_video" value="{$model.video}"/>
                    {/if}
                </div>
            </div>
            {else}
            <div class="form-group">
                <label for="image">图片</label>
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="upload_image"/>
                        <label class="custom-file-label" for="upload_image">选择文件</label>
                    </div>
                </div>
                {if !empty($model['image'])}
                    <figure class="figure">
                        <img src="{$model.image}" class="figure-img img-fluid rounded" alt="image">
                        <figcaption class="figure-caption text-center">{$model.image}</figcaption>
                    </figure>
                    <input type="hidden" name="delete_image" value="{$model.image}"/>
                {/if}
            </div>
        {/if}
        
        <div class="form-row">
            {foreach name="group['ext_set']['key']" item="ikey"}
                <div class="col-6 form-group">
                    <label for="image">{$group['ext_set']['value'][$key]}</label>
                    <input type="text" name="ext[{$ikey}]" class="form-control" value="{$model['ext'][$ikey]}" />
                </div>
            {/foreach}
        </div>
        {if $group['type'] == 0}
            <div class="form-group">
                <label for="image">元件</label>
                <div>
                    <div class="elements-box">

                    </div>
                    <a href="javascript:" class="btn btn-outline-dark btn-sm addelement"><i class="ion-md-add"></i> 添加元件</a>
                </div>
            </div>
        {/if}

        <div class="form-group">
            <label for="image">有效期</label>
            <div class="form-row date-range">
                <div class="input-group col">
                    <div class="input-group-prepend">
                    <span class="input-group-text">从</span>
                    </div>
                    <input type="text" name="start_date" class="form-control fromdate" value="{$model.start_date|default=''|showdate=''}" />
                </div>
                <div class="input-group col">
                    <div class="input-group-prepend">
                    <span class="input-group-text">至</span>
                    </div>
                    <input type="text" name="end_date" class="form-control todate" value="{$model.end_date|default=''|showdate=''}" />
                </div>
            </div>

        </div>
        <div class="form-row">
            <div class="col form-group">
                <label for="url">链接</label>
                <input type="text" name="url" class="form-control" value="{$model.url|default=''}" />
            </div>
            <div class="col form-group">
                <label for="image">排序</label>
                <input type="text" name="sort" class="form-control" value="{$model.sort|default=''}" />
            </div>
        </div>

        <div class="form-group">
            <label for="cc">状态</label>
            <label class="radio-inline">
                <input type="radio" name="status" value="1" {if $model['status'] eq 1}checked="checked"{/if} >显示
            </label>
            <label class="radio-inline">
                <input type="radio" name="status" value="0" {if $model['status'] eq 0}checked="checked"{/if}>隐藏
            </label>
        </div>
        <div class="form-group submit-btn">
            <input type="hidden" name="group_id" value="{$model.group_id}">
            <button type="submit" class="btn btn-primary">{$id>0?'保存':'添加'}</button>
        </div>
    </form>
    </div>
</div>
{/block}
{block name="script"}
    <script type="text/html" id="element-image">
        <div class="slide-element mb-2">
            <input type="hidden" name="elements[{@i}][type]" value="image" />
            <div class="input-group mb-1">
                <span class="input-group-prepend"><span class="input-group-text">图片</span></span>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" name="upload_elements_{@i}_image"/>
                    <label class="custom-file-label" for="elements_{@i}_image">选择文件</label>
                </div>
                <span class="input-group-append"><a class="btn btn-outline-danger delete-btn" href="javascript:">移除</a></span>
            </div>
            <figure class="figure">
                <img src="{@image}" class="figure-img img-fluid rounded" alt="image">
                <figcaption class="figure-caption text-center">{@image}</figcaption>
            </figure>
            <input type="hidden" name="delete_elements_{@i}_image" value="{@image}" />
            <input type="hidden" name="elements[{@i}][image]" value="{@image}" />
            <div class="input-group mb-1">
                <span class="input-group-prepend"><span class="input-group-text">初始样式</span></span>
                <input type="text" name="elements[{@i}][style]" class="form-control" />
            </div>
            <div class="input-group">
            <span class="input-group-prepend"><span class="input-group-text">动画效果</span></span>
            <select name="elements[{@i}][effect]" class="input input--dropdown js--animations">
                <optgroup label="Attention Seekers">
                  <option value="bounce">bounce</option>
                  <option value="flash">flash</option>
                  <option value="pulse">pulse</option>
                  <option value="rubberBand">rubberBand</option>
                  <option value="shake">shake</option>
                  <option value="swing">swing</option>
                  <option value="tada">tada</option>
                  <option value="wobble">wobble</option>
                </optgroup>
        
                <optgroup label="Bouncing Entrances">
                  <option value="bounceIn">bounceIn</option>
                  <option value="bounceInDown">bounceInDown</option>
                  <option value="bounceInLeft">bounceInLeft</option>
                  <option value="bounceInRight">bounceInRight</option>
                  <option value="bounceInUp">bounceInUp</option>
                </optgroup>
        
                <optgroup label="Bouncing Exits">
                  <option value="bounceOut">bounceOut</option>
                  <option value="bounceOutDown">bounceOutDown</option>
                  <option value="bounceOutLeft">bounceOutLeft</option>
                  <option value="bounceOutRight">bounceOutRight</option>
                  <option value="bounceOutUp">bounceOutUp</option>
                </optgroup>
        
                <optgroup label="Fading Entrances">
                  <option value="fadeIn">fadeIn</option>
                  <option value="fadeInDown">fadeInDown</option>
                  <option value="fadeInDownBig">fadeInDownBig</option>
                  <option value="fadeInLeft">fadeInLeft</option>
                  <option value="fadeInLeftBig">fadeInLeftBig</option>
                  <option value="fadeInRight">fadeInRight</option>
                  <option value="fadeInRightBig">fadeInRightBig</option>
                  <option value="fadeInUp">fadeInUp</option>
                  <option value="fadeInUpBig">fadeInUpBig</option>
                </optgroup>
        
                <optgroup label="Fading Exits">
                  <option value="fadeOut">fadeOut</option>
                  <option value="fadeOutDown">fadeOutDown</option>
                  <option value="fadeOutDownBig">fadeOutDownBig</option>
                  <option value="fadeOutLeft">fadeOutLeft</option>
                  <option value="fadeOutLeftBig">fadeOutLeftBig</option>
                  <option value="fadeOutRight">fadeOutRight</option>
                  <option value="fadeOutRightBig">fadeOutRightBig</option>
                  <option value="fadeOutUp">fadeOutUp</option>
                  <option value="fadeOutUpBig">fadeOutUpBig</option>
                </optgroup>
        
                <optgroup label="Flippers">
                  <option value="flip">flip</option>
                  <option value="flipInX">flipInX</option>
                  <option value="flipInY">flipInY</option>
                  <option value="flipOutX">flipOutX</option>
                  <option value="flipOutY">flipOutY</option>
                </optgroup>
        
                <optgroup label="Lightspeed">
                  <option value="lightSpeedIn">lightSpeedIn</option>
                  <option value="lightSpeedOut">lightSpeedOut</option>
                </optgroup>
        
                <optgroup label="Rotating Entrances">
                  <option value="rotateIn">rotateIn</option>
                  <option value="rotateInDownLeft">rotateInDownLeft</option>
                  <option value="rotateInDownRight">rotateInDownRight</option>
                  <option value="rotateInUpLeft">rotateInUpLeft</option>
                  <option value="rotateInUpRight">rotateInUpRight</option>
                </optgroup>
        
                <optgroup label="Rotating Exits">
                  <option value="rotateOut">rotateOut</option>
                  <option value="rotateOutDownLeft">rotateOutDownLeft</option>
                  <option value="rotateOutDownRight">rotateOutDownRight</option>
                  <option value="rotateOutUpLeft">rotateOutUpLeft</option>
                  <option value="rotateOutUpRight">rotateOutUpRight</option>
                </optgroup>
        
                <optgroup label="Sliding Entrances">
                  <option value="slideInUp">slideInUp</option>
                  <option value="slideInDown">slideInDown</option>
                  <option value="slideInLeft">slideInLeft</option>
                  <option value="slideInRight">slideInRight</option>
        
                </optgroup>
                <optgroup label="Sliding Exits">
                  <option value="slideOutUp">slideOutUp</option>
                  <option value="slideOutDown">slideOutDown</option>
                  <option value="slideOutLeft">slideOutLeft</option>
                  <option value="slideOutRight">slideOutRight</option>
                  
                </optgroup>
                
                <optgroup label="Zoom Entrances">
                  <option value="zoomIn">zoomIn</option>
                  <option value="zoomInDown">zoomInDown</option>
                  <option value="zoomInLeft">zoomInLeft</option>
                  <option value="zoomInRight">zoomInRight</option>
                  <option value="zoomInUp">zoomInUp</option>
                </optgroup>
                
                <optgroup label="Zoom Exits">
                  <option value="zoomOut">zoomOut</option>
                  <option value="zoomOutDown">zoomOutDown</option>
                  <option value="zoomOutLeft">zoomOutLeft</option>
                  <option value="zoomOutRight">zoomOutRight</option>
                  <option value="zoomOutUp">zoomOutUp</option>
                </optgroup>
        
                <optgroup label="Specials">
                  <option value="hinge">hinge</option>
                  <option value="rollIn">rollIn</option>
                  <option value="rollOut">rollOut</option>
                </optgroup>
              </select>
              <span class="input-group-middle"><span class="input-group-text">动画时长</span></span>
              <input type="text" name="elements[{@i}][duration]" class="form-control" />
              <span class="input-group-middle"><span class="input-group-text">动画延时</span></span>
              <input type="text" name="elements[{@i}][delay]" class="form-control" />
            </div>
        </div>
    </script>
    <script type="text/html" id="element-text">
        <div class="slide-element mb-2">
            <input type="hidden" name="elements[{@i}][type]" value="text" />
            <div class="input-group mb-1">
                <span class="input-group-prepend"><span class="input-group-text">文本</span></span>
                <input type="text" name="elements[{@i}][text]" class="form-control" />
                <span class="input-group-middle"><span class="input-group-text">字号</span></span>
                <input type="text" name="elements[{@i}][fontsize]" class="form-control" />
                <span class="input-group-middle"><span class="input-group-text">颜色</span></span>
                <input type="text" name="elements[{@i}][color]" class="form-control" />
                <span class="input-group-append"><a href="javascript:" class="btn btn-outline-danger delete-btn">移除</a></span>
            </div>
            <div class="input-group mb-1">
                <span class="input-group-prepend"><span class="input-group-text">初始样式</span></span>
                <input type="text" name="elements[{@i}][style]" class="form-control" />
            </div>
            <div class="input-group">
            <span class="input-group-prepend"><span class="input-group-text">动画效果</span></span>
            <select name="elements[{@i}][effect]" class="input input--dropdown js--animations">
                <optgroup label="Attention Seekers">
                  <option value="bounce">bounce</option>
                  <option value="flash">flash</option>
                  <option value="pulse">pulse</option>
                  <option value="rubberBand">rubberBand</option>
                  <option value="shake">shake</option>
                  <option value="swing">swing</option>
                  <option value="tada">tada</option>
                  <option value="wobble">wobble</option>
                </optgroup>
        
                <optgroup label="Bouncing Entrances">
                  <option value="bounceIn">bounceIn</option>
                  <option value="bounceInDown">bounceInDown</option>
                  <option value="bounceInLeft">bounceInLeft</option>
                  <option value="bounceInRight">bounceInRight</option>
                  <option value="bounceInUp">bounceInUp</option>
                </optgroup>
        
                <optgroup label="Bouncing Exits">
                  <option value="bounceOut">bounceOut</option>
                  <option value="bounceOutDown">bounceOutDown</option>
                  <option value="bounceOutLeft">bounceOutLeft</option>
                  <option value="bounceOutRight">bounceOutRight</option>
                  <option value="bounceOutUp">bounceOutUp</option>
                </optgroup>
        
                <optgroup label="Fading Entrances">
                  <option value="fadeIn">fadeIn</option>
                  <option value="fadeInDown">fadeInDown</option>
                  <option value="fadeInDownBig">fadeInDownBig</option>
                  <option value="fadeInLeft">fadeInLeft</option>
                  <option value="fadeInLeftBig">fadeInLeftBig</option>
                  <option value="fadeInRight">fadeInRight</option>
                  <option value="fadeInRightBig">fadeInRightBig</option>
                  <option value="fadeInUp">fadeInUp</option>
                  <option value="fadeInUpBig">fadeInUpBig</option>
                </optgroup>
        
                <optgroup label="Fading Exits">
                  <option value="fadeOut">fadeOut</option>
                  <option value="fadeOutDown">fadeOutDown</option>
                  <option value="fadeOutDownBig">fadeOutDownBig</option>
                  <option value="fadeOutLeft">fadeOutLeft</option>
                  <option value="fadeOutLeftBig">fadeOutLeftBig</option>
                  <option value="fadeOutRight">fadeOutRight</option>
                  <option value="fadeOutRightBig">fadeOutRightBig</option>
                  <option value="fadeOutUp">fadeOutUp</option>
                  <option value="fadeOutUpBig">fadeOutUpBig</option>
                </optgroup>
        
                <optgroup label="Flippers">
                  <option value="flip">flip</option>
                  <option value="flipInX">flipInX</option>
                  <option value="flipInY">flipInY</option>
                  <option value="flipOutX">flipOutX</option>
                  <option value="flipOutY">flipOutY</option>
                </optgroup>
        
                <optgroup label="Lightspeed">
                  <option value="lightSpeedIn">lightSpeedIn</option>
                  <option value="lightSpeedOut">lightSpeedOut</option>
                </optgroup>
        
                <optgroup label="Rotating Entrances">
                  <option value="rotateIn">rotateIn</option>
                  <option value="rotateInDownLeft">rotateInDownLeft</option>
                  <option value="rotateInDownRight">rotateInDownRight</option>
                  <option value="rotateInUpLeft">rotateInUpLeft</option>
                  <option value="rotateInUpRight">rotateInUpRight</option>
                </optgroup>
        
                <optgroup label="Rotating Exits">
                  <option value="rotateOut">rotateOut</option>
                  <option value="rotateOutDownLeft">rotateOutDownLeft</option>
                  <option value="rotateOutDownRight">rotateOutDownRight</option>
                  <option value="rotateOutUpLeft">rotateOutUpLeft</option>
                  <option value="rotateOutUpRight">rotateOutUpRight</option>
                </optgroup>
        
                <optgroup label="Sliding Entrances">
                  <option value="slideInUp">slideInUp</option>
                  <option value="slideInDown">slideInDown</option>
                  <option value="slideInLeft">slideInLeft</option>
                  <option value="slideInRight">slideInRight</option>
        
                </optgroup>
                <optgroup label="Sliding Exits">
                  <option value="slideOutUp">slideOutUp</option>
                  <option value="slideOutDown">slideOutDown</option>
                  <option value="slideOutLeft">slideOutLeft</option>
                  <option value="slideOutRight">slideOutRight</option>
                  
                </optgroup>
                
                <optgroup label="Zoom Entrances">
                  <option value="zoomIn">zoomIn</option>
                  <option value="zoomInDown">zoomInDown</option>
                  <option value="zoomInLeft">zoomInLeft</option>
                  <option value="zoomInRight">zoomInRight</option>
                  <option value="zoomInUp">zoomInUp</option>
                </optgroup>
                
                <optgroup label="Zoom Exits">
                  <option value="zoomOut">zoomOut</option>
                  <option value="zoomOutDown">zoomOutDown</option>
                  <option value="zoomOutLeft">zoomOutLeft</option>
                  <option value="zoomOutRight">zoomOutRight</option>
                  <option value="zoomOutUp">zoomOutUp</option>
                </optgroup>
        
                <optgroup label="Specials">
                  <option value="hinge">hinge</option>
                  <option value="rollIn">rollIn</option>
                  <option value="rollOut">rollOut</option>
                </optgroup>
              </select>
              <span class="input-group-middle"><span class="input-group-text">动画时长</span></span>
              <input type="text" name="elements[{@i}][duration]" class="form-control" />
              <span class="input-group-middle"><span class="input-group-text">动画延时</span></span>
              <input type="text" name="elements[{@i}][delay]" class="form-control" />
            </div>
        </div>
    </script>
    <script type="text/javascript">
        function addElement(row, key){
            var tmpl = '';
            if(row.type == 'text'){
                tmpl = $('#element-text').html();
            }else if(row.type == 'image'){
                tmpl = $('#element-image').html();
            }
            if(tmpl){
                row.i = key;
                $('.elements-box').append(tmpl.compile(row));
                var current = $('.elements-box .slide-element').eq(-1);
                current.find('input,select').each(function(idx, item){
                    if($(item).attr('type')!=='hidden'){
                        var name=$(item).attr('name');
                        nameparts=name.split('][');
                        if(nameparts.length>1){
                            var k = nameparts[1].replace(']','');
                            $(item).val(row[k]?row[k]:'');
                        }
                    }
                })

            }
        }
        jQuery(function ($) {
            var elementCount = 0;
            var elements = JSON.parse('{$model.elements|json_encode|raw}')
            $('.addelement').click(function (e) {
                dialog.action(['文本','图片'],function(index){
                    addElement({
                        image:'/static/images/nopic.png',
                        type:index?'image':'text'
                    },'add'+(elementCount++))
                })
            });
            $('.elements-box').on('click','.delete-btn',function (e) {
                var self=$(this);
                dialog.confirm('确定删除该元件？',function () {
                    self.parents('.slide-element').remove();
                })
            });
            if(elements && elements.length>0){
                for(var i=0;i<elements.length;i++){
                    addElement(elements[i], i)
                }
            }
        });
    </script>
{/block}
