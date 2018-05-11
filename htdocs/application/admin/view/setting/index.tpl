<extend name="public:base" />

<block name="body">

<include file="public/bread" menu="setting_index" title="" />

<div id="page-wrapper">

    <div class="container-fluid tab-container" >
        <div class="btn-toolbar tab-toolbar" role="toolbar" aria-label="Toolbar with button groups">
            <div class="btn-group mr-2 btn-group-sm" role="group" aria-label="First group">
                <a href="javascript:" class="btn btn-outline-secondary"><i class="ion-md-ios-cloud-download"></i> 导出</a>
                <a href="javascript:" class="btn btn-outline-secondary"><i class="ion-md-ios-cloud-upload"></i> 导入</a>
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
    </block>
<block name="script">
<script type="text/javascript">
    jQuery(function() {
        var agroup='{$group}';
        if(agroup && $('.head-'+agroup).length>0){
            $('.nav-tabs li.head-'+agroup+' a').trigger('click');
            //$('#panel-'+agroup).addClass('active');
        }else{
            $('.nav-tabs li').eq(0).find('a').trigger('click');
            //$('.tab-content .tab-pane').eq(0).addClass('active');
        }
        $('.nav-tabs a').click(function() {
            $('[name=group]').val($(this).data('group'));
        });
    });
</script>

</block>