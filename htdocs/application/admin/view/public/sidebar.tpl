
<div class="side-nav" id="accordion" role="tablist" aria-multiselectable="true">
    <foreach name="menus[0]" item="menu">
        <div class="card text-white bg-dark">

            <if condition="!empty($menus[$menu['id']])">
                <div class="card-header" role="tab" id="heading{$menu['key']}">
                    <h4 >
                        <a data-key="{$menu['key']}" class="menu_top collapsed" data-toggle="collapse" href="#collapse{$menu['key']}" aria-expanded="false" aria-controls="collapse{$menu['key']}">
                            <i class="{$menu['icon']}"></i>&nbsp;{$menu['name']}
                        </a>
                    </h4>
                </div>
                <div id="collapse{$menu['key']}" class="collapse" data-parent="#accordion"  role="tabpanel" aria-labelledby="heading{$menu['key']}">
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <foreach name="menus[$menu['id']]" item="m">
                                <li><a data-key="{$m['key']}" href="{:url($m['url'])}"><i class="{$m['icon']}"></i> {$m['name']}</a></li>
                            </foreach>
                        </ul>
                    </div>
                </div>
                <else/>
                <div class="card-header" role="tab" id="heading{$menu['key']}">
                    <h4 >
                        <a class="menu_top" data-key="{$menu['key']}" data-parent="#accordion" href="{:url($menu['url'])}"  aria-expanded="false">
                            <i class="{$menu['icon']}"></i>&nbsp;{$menu['name']}
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
                    <i class="ion-md-power-off"></i>&nbsp;退出
                </a>
            </h4>
        </div>
    </div>
</div>