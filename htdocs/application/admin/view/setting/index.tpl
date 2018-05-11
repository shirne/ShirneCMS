<extend name="public:base" />

<block name="body">

<include file="public/bread" menu="setting_index" title="" />

<div id="page-wrapper">

    <div class="container-fluid tab-container" >
        <div class="btn-toolbar tab-toolbar" role="toolbar" aria-label="Toolbar with button groups">
            <div class="btn-group mr-2 btn-group-sm" role="group" aria-label="First group">
                <a href="{:url('export')}" class="btn btn-outline-secondary"><i class="ion-md-cloud-download"></i> 导出</a>
                <a href="javascript:" class="btn btn-outline-secondary import-btn"><i class="ion-md-cloud-upload"></i> 导入</a>
            </div>
            <div class="btn-group btn-group-sm" role="group" aria-label="Third group">
                <a href="{:url('setting/advance')}" class="btn btn-outline-secondary">高级模式</a>
            </div>
        </div>

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <foreach name="groups" item="name">
                <li class="nav-item head-{$key}"><a class="nav-link" href="#panel-{$key}" data-group="{$key}" role="tab" data-toggle="tab">{$name}</a></li>
            </foreach>
        </ul>

        <form class="form-horizontal" role="form" method="post">
            <input type="hidden" name="group" value="{$group}" />
            <!-- Tab panes -->
            <div class="tab-content">
                <foreach name="groups" item="name">
                <div role="tabpanel" class="tab-pane" id="panel-{$key}">

                    <foreach name="settings[$key]" item="item">
                        <div class="form-row form-group">
                            <label for="{$key}" class="col-2 text-right align-middle">{$item.title}</label>
                            <div class="col-5">
                                <switch name="item.type">
                                    <case value="text">
                                        <input type="text" class="form-control" name="v-{$key}" value="{$item.value}" placeholder="{$item.description}">
                                    </case>
                                    <case value="number">
                                        <input type="number" class="form-control" name="v-{$key}" value="{$item.value}" placeholder="{$item.description}">
                                    </case>
                                    <case value="bool">
                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <foreach name="item.data" item="value" key="k">
                                            <if condition="$item['value']==$k">
                                                <label class="btn btn-outline-secondary active">
                                                    <input type="radio" name="v-{$key}" value="{$k}" autocomplete="off" checked> {$value}
                                                </label>
                                            <else />
                                                <label class="btn btn-outline-secondary">
                                                    <input type="radio" name="v-{$key}" value="{$k}" autocomplete="off"> {$value}
                                                </label>
                                            </if>
                                        </foreach>
                                        </div>
                                    </case>
                                    <case value="radio">
                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <foreach name="item.data" item="value" key="k">
                                            <if condition="$item['value']==$k">
                                                <label class="btn btn-outline-secondary active">
                                                    <input type="radio" name="v-{$key}" value="{$k}" autocomplete="off" checked> {$value}
                                                </label>
                                            <else />
                                                <label class="btn btn-outline-secondary">
                                                    <input type="radio" name="v-{$key}" value="{$k}" autocomplete="off"> {$value}
                                                </label>
                                            </if>
                                        </foreach>
                                        </div>
                                    </case>
                                    <case value="check">
                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <foreach name="item.data" item="value" key="k">
                                            <if condition="in_array($k,$item['value'])">
                                                <label class="btn btn-outline-secondary active">
                                                    <input type="radio" name="v-{$key}[]" value="{$k}" autocomplete="off" checked> {$value}
                                                </label>
                                            <else />
                                                <label class="btn btn-outline-secondary">
                                                    <input type="radio" name="v-{$key}[]" value="{$k}" autocomplete="off"> {$value}
                                                </label>
                                            </if>
                                        </foreach>
                                        </div>
                                    </case>
                                    <case value="select">
                                        <select name="v-{$key}" class="form-control">
                                            <foreach name="item.data" item="value" key="k">
                                                <if condition="$k==$item['value']">
                                                    <option value="{$k}" selected="selected">{$value}</option>
                                                <else/>
                                                    <option value="{$k}">{$value}</option>
                                                </if>
                                            </foreach>
                                        </select>
                                    </case>
                                    <case value="textarea">
                                        <textarea name="v-{$key}" class="form-control" placeholder="{$item.description}">{$item.value|raw}</textarea>
                                    </case>
                                    <case value="html">
                                        <textarea name="v-{$key}" class="form-control" placeholder="{$item.description}">{$item.value|raw}</textarea>
                                    </case>
                                </switch>
                            </div>
                            <div class="col-5">
                            </div>
                        </div>
                    </foreach>
                    <div class="form-row form-group">
                        <div class="col-10 offset-2">
                            <button type="submit" class="btn btn-primary">保存</button>
                        </div>
                    </div>
                </div>
                </foreach>
            </div>
        </form>
    </div>
</div>
    <script type="text/plain" id="importTpl">
        <div class="container-fluid">
        <form method="post" action="{:url('import')}" enctype="multipart/form-data">
        <div class="form-row">
            <div class="col-3">导入方式</div>
            <div class="col-9">
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                  <label class="btn btn-secondary active">
                    <input type="radio" name="type" value="content" autocomplete="off" checked> 文本
                  </label>
                  <label class="btn btn-secondary">
                    <input type="radio" name="type" value="file" autocomplete="off"> 文件
                  </label>
                </div>
            </div>
        </div>
        <div class="form-row typerow contentrow" style="display:none;margin-top:1rem;">
            <div class="col-3">导入内容</div>
            <div class="col-9">
                <textarea name="content" class="form-control"></textarea>
            </div>
        </div>
        <div class="form-row typerow filerow" style="display:none;margin-top:1rem;">
            <div class="col-3">导入文件</div>
            <div class="col-9">
                <div class="custom-file">
                    <input type="file" name="contentFile" class="custom-file-input" id="validatedCustomFile" />
                    <label class="custom-file-label" for="validatedCustomFile">选择文件</label>
                 </div>
            </div>
        </div>
        </form>
        </div>
    </script>
    </block>
<block name="script">
<script type="text/javascript">
    jQuery(function() {
        var agroup='{$group}';
        if(agroup && $('.head-'+agroup).length>0){
            $('.nav-tabs li.head-'+agroup+' a').trigger('click');
        }else{
            $('.nav-tabs li').eq(0).find('a').trigger('click');
        }
        $('.nav-tabs a').click(function() {
            $('[name=group]').val($(this).data('group'));
        });

        var importTpl=$('#importTpl').html();
        $('.import-btn').click(function(e) {
            var isposting=false;
            var dlg=new Dialog({
                'onshow':function(body) {
                    body.find('[name=type]').change(function (e) {
                        var val=$('[name=type]:checked').val();
                        body.find('.typerow').hide();
                        body.find('.'+val+'row').show();
                    }).eq(0).trigger('change');
                    body.find('[name=contentFile]').change(function() {
                        var label=$(this).parents('.custom-file').find('.custom-file-label');
                        label.text($(this).val());
                    })
                },
                'onsure':function(body){
                    if(!isposting) {
                        isposting = true;
                        var form=body.find('form');
                        if(!FormData){
                            form.submit();
                        }else{
                            $.ajax({
                                url:form.attr('action'),
                                type:'POST',
                                dataType:'JSON',
                                data:new FormData(form[0]),
                                cache:false,
                                processData:false,
                                contentType:false,
                                success:function (json) {
                                    if(json.code==1){
                                        dlg.hide();
                                        dialog.alert(json.msg,function(){
                                            if(json.url){
                                                location.href=json.url;
                                            }else{
                                                location.reload();
                                            }
                                        });
                                    }else{
                                        toastr.warning(json.msg);
                                        isposting=false;
                                    }
                                }
                            });
                        }
                    }
                    return false;
                }
            }).show(importTpl,'导入配置');
        })
    });
</script>

</block>