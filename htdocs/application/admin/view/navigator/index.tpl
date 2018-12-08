<extend name="public:base" />

<block name="body">

    <include file="public/bread" menu="navigator_index" title="" />

    <div id="page-wrapper">

        <div class="container-fluid tab-container" >
            <form class="form-horizontal" role="form" method="post">
                <table class="table table-hover table-striped">
                    <thead>
                    <tr>
                        <th width="40">&nbsp;</th>
                        <th width="150">名称</th>
                        <th width="70">打开方式</th>
                        <th width="50">底部显示</th>
                        <th>链接</th>
                        <th width="120">子菜单</th>
                        <th width="240">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <foreach name="navigator" item="item">
                        <tr class="navrow row-{$key}" data-key="{$key}">
                            <td><i class="ion-md-apps"></i> </td>
                            <td>
                                <input type="text" class="form-control" name="navs[{$key}][title]" value="{$item.title}"/>
                            </td>
                            <td>
                                <select class="form-control" name="navs[{$key}][target]">
                                    <option value="">无</option>
                                    <option value="_blank" {$item['target']=='_blank'?'selected':''}>新窗口</option>
                                    <option value="_self" {$item['target']=='_self'?'selected':''}>本窗口</option>
                                </select>
                            </td>
                            <td>
                                <label><input type="checkbox" name="navs[{$key}][footer]" value="1" {$item['footer']?'checked':''}/>&nbsp;是</label>
                            </td>
                            <td>
                                <div class="input-group">

                                    <if condition="is_array($item['url'])">
                                        <select class="form-control typepicker" name="navs[{$key}][urltype]" style="width: 80px;max-width: 80px;">
                                            <option value="url" >网址</option>
                                            <option value="module" selected>模块</option>
                                        </select>
                                        <select class="form-control modulepicker modulefield" name="navs[{$key}][module]">
                                            <foreach name="modules" item="v" key="k">
                                                <option value="{$k}" rwq="{$item['url'][2]}" {$k==$item['url'][2]?'selected':''}>{$v}</option>
                                            </foreach>
                                        </select>
                                        <select class="form-control modulecate modulefield" data-value="{$item['url']|json_encode}" name="navs[{$key}][cate_name]">
                                        </select>
                                        <input type="text" class="form-control urlfield" name="navs[{$key}][url]" value=""/>
                                        <else/>
                                        <select class="form-control typepicker" name="navs[{$key}][urltype]" style="width: 80px;max-width: 80px;">
                                            <option value="url" selected>网址</option>
                                            <option value="module">模块</option>
                                        </select>
                                        <select class="form-control modulepicker modulefield" name="navs[{$key}][module]">
                                            <foreach name="modules" item="v" key="k">
                                                <option value="{$k}">{$v}</option>
                                            </foreach>
                                        </select>
                                        <select class="form-control modulecate modulefield" name="navs[{$key}][cate_name]">
                                        </select>
                                        <input type="text" class="form-control urlfield" name="navs[{$key}][url]" value="{$item.url}"/>
                                    </if>
                                </div>
                            </td>
                            <td>
                                <select class="form-control subpicker" name="navs[{$key}][subnavtype]">
                                    <option value="customer" >手动设置</option>
                                    <option value="module" {$item['subnavtype']=='module'?'selected':''}>自动调用</option>
                                </select>
                            </td>
                            <td>
                                <a href="javascript:" class="btn btn-outline-primary rowadd">添加</a>
                                <a href="javascript:" class="btn btn-outline-primary rowup">上移</a>
                                <a href="javascript:" class="btn btn-outline-primary rowdown">下移</a>
                                <a href="javascript:" class="btn btn-outline-danger rowdelete">删除</a>
                            </td>
                        </tr>
                        <if condition="$item['subnavtype'] EQ 'customer' && !empty($item['subnav'])">
                            <foreach name="item['subnav']" item="subnav" key="index">
                                <tr class="subrow row-{$key}-{$index}" data-key="{$key}" data-index="{$index}">
                                    <td><i class="ion-md-apps"></i> </td>
                                    <td>
                                        <input type="text" class="form-control" name="navs[{$key}][subnav][{$index}][title]" value="{$subnav.title}" />
                                    </td>
                                    <td>
                                        <select class="form-control" name="navs[{$key}][subnav][{$index}][target]">
                                            <option value="">无</option>
                                            <option value="_blank" {$subnav['target']=='_blank'?'selected':''}>新窗口</option>
                                            <option value="_self" {$subnav['target']=='_self'?'selected':''}>本窗口</option>
                                        </select>
                                    </td>
                                    <td>
                                        &nbsp;
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="text" class="form-control urlfield" name="navs[{$key}][subnav][{$index}][url]" value="{$subnav.url}"/>
                                        </div>
                                    </td>
                                    <td>
                                        &nbsp;
                                    </td>
                                    <td>
                                        <a href="javascript:" class="btn btn-outline-primary rowup">上移</a>
                                        <a href="javascript:" class="btn btn-outline-primary rowdown">下移</a>
                                        <a href="javascript:" class="btn btn-outline-danger rowdelete">删除</a>
                                    </td>
                                </tr>
                            </foreach>
                        </if>
                    </foreach>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td>&nbsp;</td>
                        <td colspan="6"><a href="javascript:" class="btn btn-outline-primary addrow">添加行</a> </td>
                    </tr>
                    </tfoot>
                </table>
                <div class="form-row form-group">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">保存</button>
                        <div class="text-muted mt-3">本页面所有操作完成后，请点击<span class="text-danger"> 保存 </span>按钮。<br />然后在本页面右上角弹出菜单点击 <span class="text-danger">清除缓存</span>。<br />如有操作失误，不要保存，直接<span class="text-danger">刷新</span>页面即可还原修改前的状态。</div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</block>
<block name="script">
    <script type="text/plain" id="rowTemplate">
        <tr class="navrow row-{@key}" data-key="{@key}">
            <td><i class="ion-md-apps"></i> </td>
            <td>
                <input type="text" class="form-control" name="navs[{@key}][title]" />
            </td>
            <td>
                <select class="form-control" name="navs[{$key}][target]">
                    <option value="">无</option>
                    <option value="_blank">新窗口</option>
                    <option value="_self">本窗口</option>
                </select>
            </td>
            <td>
                <label><input type="checkbox" name="navs[{@key}][footer]" value="1" />&nbsp;是</label>
            </td>
            <td>
                <div class="input-group">
                    <select class="form-control typepicker" name="navs[{@key}][urltype]" style="width: 80px;max-width: 80px;">
                        <option value="url" >网址</option>
                        <option value="module" selected>模块</option>
                    </select>
                    <select class="form-control modulepicker modulefield" name="navs[{@key}][module]">
                        <foreach name="modules" item="v" key="k">
                            <option value="{$k}">{$v}</option>
                        </foreach>
                    </select>
                    <select class="form-control modulecate modulefield" name="navs[{@key}][cate_name]">
                    </select>
                    <input type="text" class="form-control urlfield" name="navs[{@key}][url]" value=""/>
                </div>
            </td>
            <td>
                <select class="form-control subpicker" name="navs[{@key}][subnavtype]" >
                    <option value="customer" selected>手动设置</option>
                    <option value="module">自动调用</option>
                </select>
            </td>
            <td>
                <a href="javascript:" class="btn btn-outline-primary rowadd">添加</a>
                <a href="javascript:" class="btn btn-outline-primary rowup">上移</a>
                <a href="javascript:" class="btn btn-outline-primary rowdown">下移</a>
                <a href="javascript:" class="btn btn-outline-danger rowdelete">删除</a>
            </td>
        </tr>
    </script>
    <script type="text/plain" id="subRowTemplate">
        <tr class="subrow row-{@key}-{@index}" data-key="{@key}" data-index="{@index}">
            <td><i class="ion-md-apps"></i> </td>
            <td>
                <input type="text" class="form-control" name="navs[{@key}][subnav][{@index}][title]" />
            </td>
            <td>
                <select class="form-control" name="navs[{@key}][subnav][{@index}][target]">
                    <option value="">无</option>
                    <option value="_blank">新窗口</option>
                    <option value="_self">本窗口</option>
                </select>
            </td>
            <td>
                &nbsp;
            </td>
            <td>
                <div class="input-group">
                    <input type="text" class="form-control urlfield" name="navs[{@key}][subnav][{@index}][url]" value=""/>
                </div>
            </td>
            <td>
                &nbsp;
            </td>
            <td>
                <a href="javascript:" class="btn btn-outline-primary rowup">上移</a>
                <a href="javascript:" class="btn btn-outline-primary rowdown">下移</a>
                <a href="javascript:" class="btn btn-outline-danger rowdelete">删除</a>
            </td>
        </tr>
    </script>
    <script type="text/javascript">
        jQuery(function($) {
            var idx=parseInt('{:count($navigator)}');

            var cates={};

            function categoryOption(cates,pid,pre){
                var options=[];
                if(!pid){
                    pid=0;
                    options.push('<option value="">模块主页</option>');
                }
                if(!pre)pre='';
                for(var i=0;i<cates.length;i++){
                    if(cates[i].pid==pid) {
                        options.push('<option value="' + cates[i].name + '">' +pre+ cates[i].title + '</option>');
                        options.push(categoryOption(cates,cates[i].id,pre?(' '+pre):'|-'));
                    }
                }
                return options.join('\n');
            }

            function typePickerChange(e){
                var parent=$(this).parents('.input-group');
                var row=$(this).parents('tr');
                if($(this).val()=='module') {
                    parent.find('.modulefield').show();
                    parent.find('.urlfield').hide();
                    row.find('.subpicker option').each(function() {
                        if($(this).val()!='customer'){
                            $(this).prop('disabled',false);
                        }
                    });
                    row.find('.modulepicker').trigger('change');
                }else{
                    parent.find('.modulefield').hide();
                    parent.find('.urlfield').show();
                    row.find('.subpicker option').each(function() {
                        if($(this).val()!='customer'){
                            $(this).prop('disabled',true);
                        }

                    });
                    row.find('.subpicker').val('customer');
                }
                row.find('.subpicker').trigger('change');
            }
            function initCategory(select,cates){
                if (cates.length) {
                    select.show();
                    select.html(categoryOption(cates));
                    var value=select.data('value');
                    var cname='';
                    if(value[1]) {
                        if (value[2] == 'Index') {

                        } else if (value[2] == 'Page') {
                            cname = value[1] ? value[1]['group'] : '';
                        } else {
                            cname = value[1] ? value[1]['name']:'';
                        }
                    }

                    if(cname)select.val(cname);
                } else {
                    select.hide();
                }
            }
            function modulePickerChange(e) {
                var parent=$(this).parents('.input-group');
                var module=$(this).val();
                var self=this;
                if(cates[module]){
                    if(cates[module]===1){
                        setTimeout(function() {
                            modulePickerChange.call(self,e);
                        },1000);
                    }else {
                        initCategory(parent.find('.modulecate'), cates[module]);
                    }
                }else {
                    $.ajax({
                        url: "{:url('navigator/getCategories',['module'=>'__MODULE__'])}".replace('__MODULE__', module),
                        dataType: 'JSON',
                        success: function (json) {
                            //console.log(json);
                            cates[module]=json.data;
                            initCategory(parent.find('.modulecate'),cates[module]);
                        }
                    })
                }
            }
            function subPickerChange(e){
                var row=$(this).parents('tr');
                if($(this).val()=='customer'){
                    row.find('.rowadd').removeClass('disabled');
                }else{
                    row.find('.rowadd').addClass('disabled');
                }
            }

            $('.subpicker').change(subPickerChange);
            $('.modulepicker').change(modulePickerChange);
            $('.typepicker').change(typePickerChange).trigger('change');

            $('.table').on('click','.rowdelete',function (e) {
                var self=$(this);
                dialog.confirm('您确定要删除此导航菜单？',function () {
                    var parent=self.parents('tr');
                    if(!parent.is('.subrow')) {
                        var subnavs = parent.nextAll('tr.subrow');
                        subnavs.remove();
                    }
                    parent.remove();
                })
            });
            $('.table').on('click','.rowup',function (e) {
                var row=$(this).parents('tr');
                var prev=row.prev();
                if(row.is('.subrow')){
                    if(prev.is('.subrow')){
                        row.insertBefore(prev);
                        var key=row.data('key');
                        var curIndex=row.data('index');
                        var prevIndex=prev.data('index');
                        var curNavPre='navs['+key+'][subnav]['+curIndex+']';
                        var prevNavPre='navs['+key+'][subnav]['+prevIndex+']';
                        row.data('index',prevIndex)
                            .attr('class','subrow row-'+key+'-'+prevIndex)
                            .find('[name^=navs]').each(function () {
                            $(this).prop('name',$(this).prop('name').replace(curNavPre,prevNavPre));
                        });
                        prev.data('index',curIndex)
                            .attr('class','subrow row-'+key+'-'+curIndex)
                            .find('[name^=navs]').each(function () {
                            $(this).prop('name',$(this).prop('name').replace(prevNavPre, curNavPre));
                        });
                    }
                }else{
                    var prevSons=[];
                    while(prev.is('.subrow')){
                        prevSons.push(prev[0]);
                        prev=prev.prev();
                        if(!prev.length) {
                            return false;
                        }
                    }
                    var curSons=[];
                    var son=row.next();
                    while(son.is('.subrow')){
                        curSons.push(son[0]);
                        son=son.next();
                        if(!son.length) {
                            break;
                        }
                    }
                    row.insertBefore(prev);
                    var curKey=row.data('key');
                    var prevKey=prev.data('key');
                    var curPre='navs['+curKey+']';
                    var prevPre='navs['+prevKey+']';
                    row.data('key',prevKey)
                        .attr('class','navrow row-'+prevKey)
                        .find('[name^=navs]').each(function () {
                        $(this).attr('name',$(this).attr('name').replace(curPre,prevPre));
                    });
                    prev.data('key',curKey)
                        .attr('class','navrow row-'+curKey)
                        .find('[name^=navs]').each(function () {
                        $(this).attr('name',$(this).attr('name').replace(prevPre, curPre));
                    });

                    if(prevSons.length>0){
                        prevSons=prevSons.reverse();
                        $(prevSons).each(function () {
                            $(this).find('[name^=navs]').each(function () {
                                $(this).attr('name',$(this).attr('name').replace(prevPre, curPre));
                            });
                            $(this).insertAfter(prev);
                        })
                    }
                    if(curSons.length>0){
                        $(curSons).each(function () {
                            $(this).find('[name^=navs]').each(function () {
                                $(this).attr('name',$(this).attr('name').replace(curPre, prevPre));
                            });
                            $(this).insertAfter(row);
                        })
                    }
                }

            });

            $('.table').on('click','.rowdown',function (e) {
                var row=$(this).parents('tr');
                var next=row.next();
                if(row.is('.subrow')){
                    if(next.is('.subrow')){
                        row.insertAfter(next);
                        var key=row.data('key');
                        var curIndex=row.data('index');
                        var nextIndex=prev.data('index');
                        var curNavPre='navs['+key+'][subnav]['+curIndex+']';
                        var nextNavPre='navs['+key+'][subnav]['+nextIndex+']';
                        row.data('index',nextIndex)
                            .attr('class','subrow row-'+key+'-'+nextIndex)
                            .find('[name^=navs]').each(function () {
                            $(this).prop('name',$(this).prop('name').replace(curNavPre,nextNavPre));
                        });
                        next.data('index',curIndex)
                            .attr('class','subrow row-'+key+'-'+curIndex)
                            .find('[name^=navs]').each(function () {
                            $(this).prop('name',$(this).prop('name').replace(nextNavPre, curNavPre));
                        });
                    }
                }else{
                    var curSons=[];
                    while(next.is('.subrow')){
                        curSons.push(next[0]);
                        next=next.next();
                        if(!next.length) {
                            return false;
                        }
                    }
                    var nextSons=[];
                    var son=next.next();
                    while(son.is('.subrow')){
                        nextSons.push(son[0]);
                        son=son.next();
                        if(!son.length) {
                            break;
                        }
                    }
                    row.insertAfter(next);
                    var curKey=row.data('key');
                    var nextKey=prev.data('key');
                    var curPre='navs['+curKey+']';
                    var nextPre='navs['+nextKey+']';
                    row.data('key',nextKey)
                        .attr('class','navrow row-'+nextKey)
                        .find('[name^=navs]').each(function () {
                        $(this).attr('name',$(this).attr('name').replace(curPre,nextPre));
                    });
                    next.data('key',curKey)
                        .attr('class','navrow row-'+curKey)
                        .find('[name^=navs]').each(function () {
                        $(this).attr('name',$(this).attr('name').replace(nextPre, curPre));
                    });

                    if(nextSons.length>0){
                        $(nextSons).each(function () {
                            $(this).find('[name^=navs]').each(function () {
                                $(this).attr('name',$(this).attr('name').replace(nextPre, curPre));
                            });
                            $(this).insertAfter(row);
                        })
                    }
                    if(curSons.length>0){
                        $(curSons).each(function () {
                            $(this).find('[name^=navs]').each(function () {
                                $(this).attr('name',$(this).attr('name').replace(curPre, nextPre));
                            });
                            $(this).insertAfter(next);
                        })
                    }
                }

            });

            var template=$('#rowTemplate').text();
            var subTemplate=$('#subRowTemplate').text();
            $('.addrow').click(function (e) {
                var newrow=$(template.compile({
                    'key':idx++
                }));
                $(this).parents('table').find('tbody').append(newrow);

                newrow.find('.subpicker').change(subPickerChange).trigger('change');
                newrow.find('.modulepicker').change(modulePickerChange).trigger('change');
                newrow.find('.typepicker').change(typePickerChange).trigger('change');
            });
            $('.table').on('click','.rowadd',function (e) {
                var row=$(this).parents('tr');
                var lastrow=row.next('tr');
                var newrow=subTemplate.compile({
                    'key':idx,
                    'index':0
                });
                row.after(newrow);
            });
        });
    </script>

</block>