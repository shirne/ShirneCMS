<extend name="public:base" />

<block name="body">
    <include file="public/bread" menu="wechat_index" title="公众号菜单" />

    <div id="page-wrapper">
        <div class="page-header">编辑菜单</div>
        <div id="page-content">
            <form method="post" action="">
                <div class="form-row mb-3">
                    <div class="col menu-view-box">
                        <div class="menu-view">
                            <div class="menu-inner">
                                <div class="menubox">
                                    <div class="row"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col menu-info-box">
                        <div class="card">
                            <div class="card-header">
                                <a class="float-right delete" href="javascript:" onclick="deleteMenu()"></a>
                                <h4></h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="menu-name" class="col-sm-2 col-form-label">菜单名称</label>
                                    <div class="col-sm-10"><input type="text" name="menu-name" class="form-control" onchange="updateMenu()" value="" /><div class="text-muted nametip"></div> </div>
                                </div>
                                <div class="form-group row menu-content">
                                    <label for="content-view" class="col-sm-2 col-form-label">菜单内容</label>
                                    <div class="col-sm-10">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="click">
                                            <label class="form-check-label" for="inlineRadio1">发送消息</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="view">
                                            <label class="form-check-label" for="inlineRadio2">跳转网页</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="miniprogram">
                                            <label class="form-check-label" for="inlineRadio3">跳转小程序</label>
                                        </div>
                                    </div>

                                    <div class="col content-view">
                                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Home</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Profile</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Contact</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content" id="myTabContent">
                                            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">...</div>
                                            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">...</div>
                                            <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">...</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="menu-tip text-muted">已添加子菜单，仅可设置菜单名称。</div>
                            </div>
                            <div class="card-tip">请从左侧选择菜单编辑</div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <input type="hidden" name="id" value="{$model.id}">
                    <button type="submit" class="btn btn-primary">提交</button>
                </div>
            </form>
        </div>
    </div>
</block>
<block name="script">
    <script type="text/javascript">
        var menuData=JSON.parse('{$menuData|json_encode|raw}');
        var curIdx=-1;
        var curSubIdx=-1;
        var curMenu=null;
        function showMenu(data) {
            var html = [];
            for (var i = 0; i < data.length; i++) {
                var menu={
                    'idx':i,
                    'name':data[i].name,
                    'submenu':createSubMenu(data[i].sub_button||[],i),
                    'prefix':data[i].sub_button&& data[i].sub_button.length>0?'<i class="ion-md-menu"></i>':''
                    
                };
                html.push('<div class="col"><span class="menutext" onclick="editMenu({@idx})">{@prefix}{@name}</span><div class="submenu">{@submenu}</div></div>'.compile(menu));
            }
            if(data.length<3){
                html.push('<div class="col" onclick="addMenu(-1)"><i class="ion-md-add"></i></div>');
            }
            $('.menubox .row').html(html.join('\n'));
        }
        function createSubMenu(list,idx){
            var html=[];
            for (var i = 0; i < list.length; i++) {
                var menu={
                    'idx':idx,
                    'subidx':i,
                    'name':list[i].name
                };
                html.push('<div class="subitem" onclick="editMenu({@idx},{@subidx})"><span>{@name}</span></div>'.compile(menu));
            }
            if(list.length<5){
                html.push('<div class="subitem" onclick="addMenu('+idx+')"><i class="ion-md-add"></i></div>');
            }
            return html.join('\n');
        }
        function editMenu(idx, subidx) {
            curIdx=idx;
            curSubIdx=subidx;
            var menus=$('.menubox .col');
            var subMenus=$('.menubox .col .subitem');
            menus.removeClass('active').removeClass('focus').eq(curIdx).addClass('active');
            subMenus.removeClass('active');
            if(curSubIdx>-1){
                menus.eq(curIdx).find('.subitem').eq(curSubIdx).addClass('active');
                curMenu=menuData[curIdx].sub_button[curSubIdx]
            }else{
                menus.eq(curIdx).addClass('focus');
                curMenu=menuData[curIdx]
            }

            var infobox=$('.menu-info-box');
            infobox.find('.card-tip').hide();
            infobox.find('.card-body').show();
            infobox.find('.card-header').show();

            infobox.find('.card-header h4').text(curMenu.name);
            infobox.find('.card-header a').text(curSubIdx>-1?'删除子菜单':'删除菜单');
            infobox.find('[name=menu-name]').val(curMenu.name);
            if(curSubIdx>-1){
                infobox.find('.nametip').text('字数不超过8个汉字或16个字母');
            }else{
                infobox.find('.nametip').text('字数不超过4个汉字或8个字母');
                if(curMenu.sub_button&&curMenu.sub_button.length>0){
                    infobox.find('.menu-content').hide();
                    infobox.find('.menu-tip').show();
                }else{
                    infobox.find('.menu-content').show();
                    infobox.find('.menu-tip').hide();
                }
            }

        }
        function deleteMenu(){
            dialog.confirm('确定删除该菜单？',function () {
                var infobox=$('.menu-info-box');
                infobox.find('.card-tip').show();
                infobox.find('.card-body').hide();
                infobox.find('.card-header').hide();
            })
        }
        function updateMenu(){
            var infobox=$('.menu-info-box');
            var menuname=infobox.find('[name=menu-name]').val();
            if(curSubIdx>-1){
                curMenu=menuData[curIdx].sub_button[curSubIdx]
            }else{
                curMenu=menuData[curIdx]
            }
            curMenu.name=menuname;
            infobox.find('.card-header h4').text(menuname);
            var menus=$('.menubox .col');
            if(curSubIdx>-1){
                menus.eq(curIdx).find('.subitem').eq(curSubIdx).find('span').text(menuname);
            }else{
                menus.eq(curIdx).find('.menutext').html((curMenu.sub_button&&curMenu.sub_button.length>0?'<i class="ion-md-menu"></i>':'')+menuname);
            }

        }
        function addMenu(idx){
            if(idx>-1){
                if(!menuData[idx].sub_button){
                    menuData[idx].sub_button=[];
                }
                if(menuData[idx].sub_button.length>4){
                    return toastr.warning('子菜单数量最多5个');
                }
                menuData[idx].sub_button.push({
                    name:'新建菜单'
                })
            }else{
                if(menuData.length>2){
                    return toastr.warning('主菜单数量最多3个');
                }
                menuData.push({
                    name:'新建菜单'
                });
            }
            showMenu(menuData);
            if(idx>-1){
                editMenu(idx,menuData[idx].sub_button.length-1);
            }else{
                editMenu(menuData.length-1);
            }
        }
        showMenu(menuData);
    </script>
</block>