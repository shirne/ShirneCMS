<extend name="Public:Base" />

<block name="body">

<include file="Public/bread" menu="setting_index" section="系统" title="配置管理" />

<div id="page-wrapper">

    <div class="row">
        <a href="{:url('setting/advance')}" class="btn btn-success pull-right">高级模式</a>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <foreach name="groups" item="name">
                <li role="presentation" class="head-{$key}"><a href="#panel-{$key}" data-group="{$key}" role="tab" data-toggle="tab">{$name}</a></li>
            </foreach>
        </ul>

        <form class="form-horizontal" role="form" method="post">
            <input type="hidden" name="group" value="{$group}" />
            <!-- Tab panes -->
            <div class="tab-content">
                <foreach name="groups" item="name">
                <div role="tabpanel" class="tab-pane" id="panel-{$key}">

                    <foreach name="settings[$key]" item="item">
                        <div class="form-group">
                            <label for="{$key}" class="col-sm-2 control-label">{$item.title}</label>
                            <div class="col-sm-5">
                                <switch name="item.type">
                                    <case value="text">
                                        <input type="text" class="form-control" name="v-{$key}" value="{$item.value}" placeholder="{$item.description}">
                                    </case>
                                    <case value="number">
                                        <input type="number" class="form-control" name="v-{$key}" value="{$item.value}" placeholder="{$item.description}">
                                    </case>
                                    <case value="bool">
                                        <foreach name="item.data" item="value" key="k">
                                            <if condition="$item['value']==$k">
                                                <label><input type="radio" name="v-{$key}" value="{$k}" checked="checked" /> &nbsp;{$value}</label>
                                            <else />
                                                <label><input type="radio" name="v-{$key}" value="{$k}" /> &nbsp;{$value}</label>
                                            </if>
                                        </foreach>
                                    </case>
                                    <case value="radio">
                                        <foreach name="item.data" item="value" key="k">
                                            <if condition="$item['value']==$k">
                                                <label><input type="radio" name="v-{$key}" value="{$k}" checked="checked" /> &nbsp;{$value}</label>
                                            <else />
                                                <label><input type="radio" name="v-{$key}" value="{$k}" /> &nbsp;{$value}</label>
                                            </if>
                                        </foreach>
                                    </case>
                                    <case value="check">
                                        <foreach name="item.data" item="value" key="k">
                                            <if condition="in_array($k,$item['value'])">
                                                <label><input type="checkbox" name="v-{$key}[]" value="{$k}" checked="checked" /> &nbsp;{$value}</label>
                                            <else />
                                                <label><input type="checkbox" name="v-{$key}[]" value="{$k}" /> &nbsp;{$value}</label>
                                            </if>
                                        </foreach>
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
                                        <textarea name="v-{$key}" class="form-control" placeholder="{$item.description}">{$item.value}</textarea>
                                    </case>
                                    <case value="html">
                                        <textarea name="v-{$key}" class="form-control" placeholder="{$item.description}">{$item.value}</textarea>
                                    </case>
                                </switch>
                            </div>
                            <div class="col-sm-5">
                            </div>
                        </div>
                    </foreach>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
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