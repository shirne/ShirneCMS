
<div class="panel-group side-nav" id="accordion" role="tablist" aria-multiselectable="true">
    <foreach name="menus[0]" item="menu">
        <div class="panel panel-default">

            <if condition="!empty($menus[$menu['id']])">
                <div class="panel-heading" role="tab" id="heading{$menu['key']}">
                    <h4 class="panel-title">
                        <a data-key="{$menu['key']}" class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse{$menu['key']}" aria-expanded="false" aria-controls="collapse{$menu['key']}">
                            <i class="fa {$menu['icon']}"></i>&nbsp;{$menu['name']}
                        </a>
                    </h4>
                </div>
                <div id="collapse{$menu['key']}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading{$menu['key']}">
                    <div class="panel-body">
                        <ul class="list-unstyled">
                            <foreach name="menus[$menu['id']]" item="m">
                                <li><a data-key="{$m['key']}" href="{:url($m['url'])}"><i class="fa {$m['icon']}"></i> {$m['name']}</a></li>
                            </foreach>
                        </ul>
                    </div>
                </div>
                <else/>
                <div class="panel-heading" role="tab" id="heading{$menu['key']}">
                    <h4 class="panel-title">
                        <a data-key="{$menu['key']}" data-parent="#accordion" href="{:url($menu['url'])}"  aria-expanded="false">
                            <i class="fa {$menu['icon']}"></i>&nbsp;{$menu['name']}
                        </a>
                    </h4>
                </div>
            </if>

        </div>
    </foreach>
    <div class="panel panel-default" id="loginBar">
        <div class="panel-heading" role="tab" id="headinglog">
            <h4 class="panel-title">
                <a data-parent="#accordion" href="{:url('login/logout')}"  aria-expanded="false">
                    <i class="fa fa-power-off"></i>&nbsp;退出
                </a>
            </h4>
        </div>
    </div>
</div>