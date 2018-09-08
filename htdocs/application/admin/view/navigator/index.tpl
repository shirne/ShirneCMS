<extend name="public:base" />

<block name="body">

    <include file="public/bread" menu="setting_index" title="" />

    <div id="page-wrapper">

        <div class="container-fluid tab-container" >
            <form class="form-horizontal" role="form" method="post">
                <table class="table table-hover table-striped">
                    <thead>
                    <tr>
                        <th width="40">&nbsp;</th>
                        <th width="150">名称</th>
                        <th width="50">底部显示</th>
                        <th>链接</th>
                        <th width="120">子菜单</th>
                        <th width="240">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <foreach name="navigator" item="item">
                        <tr>
                            <td><i class="ion-md-apps"></i> </td>
                            <td>
                                <input type="text" class="form-control" name="navs[{$key}][title]" value="{$item.title}"/>
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
                    </foreach>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td>&nbsp;</td>
                        <td colspan="5"><a href="javascript:" class="btn btn-outline-primary addrow">添加行</a> </td>
                    </tr>
                    </tfoot>
                </table>
                <div class="form-row form-group">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">保存</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</block>
<block name="script">
    <script type="text/plain" id="rowTemplate">
        <tr class="row-{@key}">
            <td><i class="ion-md-apps"></i> </td>
            <td>
                <input type="text" class="form-control" name="navs[{@key}][title]" />
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
        <tr class="subrow row-{@key}-{@index}">
            <td><i class="ion-md-apps"></i> </td>
            <td>
                <input type="text" class="form-control" name="navs[{@key}][subnav][{@index}][title]" />
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
                    })
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
                row.find('.modulepicker').trigger('change');
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
                if(cates[module]){
                    initCategory(parent.find('.modulecate'),cates[module]);
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
                if(row.is('.subrow')){

                }else{

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