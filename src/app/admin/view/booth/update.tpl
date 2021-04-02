{extend name="public:base" /}
{block name="header"}
    <style type="text/css">
        .card .close{
            position: absolute;
            right: 10px;
            top: 5px;
        }
        .card .swap{
            position: absolute;
            left: 10px;
            right: auto;
            top: 5px;
        }
    </style>
{/block}
{block name="body"}

    {include file="public/bread" menu="booth_index" title="展位设置" /}
    
    <div id="page-wrapper">
        <div class="page-header">{$id>0?'编辑':'添加'}展位</div>
        <div class="page-content">
        <form method="post" class="page-form" action="">
            <div class="form-row">
                <div class="form-group col">
                    <label for="title">位置名称</label>
                    <input type="text" name="title" class="form-control" v-model="model.title" placeholder="位置名称">
                </div>
                <div class="form-group col">
                    <label for="flag">调用标识</label>
                    <input type="text" name="flag" class="form-control" :readonly="model.locked==1" v-model="model.flag" placeholder="调用标识">
                </div>
            </div>
            <div class="form-row">
                <div class="col form-row align-items-center">
                    <label class="pl-2 mr-2" for="status">展位类型</label>
                    <div class="form-group col">
                        <div class="btn-group btn-group-toggle" >
                            {foreach name="booth_types" id="item"}
                                <label :class="'btn btn-outline-secondary'+(model.type=='{$key}'?' active':'')">
                                    <input type="radio" name="type" value="{$key}" autocomplete="off" v-model="model.type">{$item}
                                </label>
                            {/foreach}
                        </div>
                    </div>
                </div>
                <div class="col form-row align-items-center">
                    <label class="pl-2 mr-2" for="status">状态</label>
                    <div class="form-group col">
                        <div class="btn-group btn-group-toggle" >
                            <label :class="'btn btn-outline-secondary'+(model.status==1?' active':'')">
                                <input type="radio" name="status" :value="1" autocomplete="off" v-model="model.status">显示
                            </label>
                            <label :class="'btn btn-outline-secondary'+(model.status==0?' active':'')">
                                <input type="radio" name="status" :value="0" autocomplete="off" v-model="model.status">隐藏
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="model.type=='category'" class="card">
                <div class="card-header"><h4 class="card-title">文章分类</h4></div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col">
                            <div class="btn-group btn-group-toggle" >
                                <label :class="'btn btn-outline-secondary'+(model.data.type==0?' active':'')">
                                    <input type="radio" name="data[type]" :value="0" autocomplete="off" v-model="model.data.type">自动获取
                                </label>
                                <label :class="'btn btn-outline-secondary'+(model.data.type==1?' active':'')">
                                    <input type="radio" name="data[type]" :value="1" autocomplete="off" v-model="model.data.type">手动选择
                                </label>
                            </div>
                        </div>
                        <div v-show="model.data.type==0" class="form-group col">
                            <div class="input-group">
                                <span class="input-group-prepend"><span class="input-group-text">显示数量</span> </span>
                                <input type="text" class="form-control" name="data[count]" v-model="model.data.count"/>
                                <span class="input-group-middle"><span class="input-group-text">携带文章数量</span> </span>
                                <input type="text" class="form-control" name="data[article_count]" v-model="model.data.article_count"/>
                                <span class="input-group-middle"><span class="input-group-text">排序</span> </span>
                                <select class="form-control" v-model="model.data.article_sort" name="data[article_sort]">
                                    <option value="">默认排序</option>
                                    <option value="update_time desc" >更新时间</option>
                                    <option value="views desc">浏览量</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div v-show="model.data.type==0" class="form-group">
                        <div class="input-group">
                            <span class="input-group-prepend"><span class="input-group-text">上级分类</span> </span>
                            <select name="data[parent_id]" class="form-control" v-model="model.data.parent_id">
                                <option :value="0">顶级分类</option>
                                <option v-for="cate in category" :value="cate.id">{{cate.html}}{{cate.title}}</option>
                            </select>
                        </div>
                    </div>
                    <div v-show="model.data.type==1" class="form-row">
                        <div v-for="(item, index) in list_category"  class="col-6 col-md-4 col-lg-3">
                            <input type="hidden" name="data[category_ids][]" :value="item.id" />
                            <div class="card mb-2">
                                <button type="button" class="close" aria-label="移除" @click.stop="removeThis(index)">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <button type="button" class="close swap" aria-label="交换" @click.stop="swapThis(index)">
                                    <i aria-hidden="true" class="ion-md-swap"></i>
                                </button>
                                <img v-if="item.icon" :src="item.icon" class="card-img-top" :alt="item.title">
                                <div class="card-body">
                                    <h5 class="card-title">{{item.title}}</h5>
                                    <p class="card-text"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="media" @click="pickCate">
                                <i class="ion-md-add border" style="font-size: 60px;line-height:1em;width:60px;text-align: center"></i>
                                <div class="media-body">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div v-else-if="model.type=='article'" class="card">
                <div class="card-header"><h4 class="card-title">文章</h4></div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col">
                            <div class="btn-group btn-group-toggle" >
                                <label :class="'btn btn-outline-secondary'+(model.data.type==0?' active':'')">
                                    <input type="radio" name="data[type]" :value="0" autocomplete="off" v-model="model.data.type">自动获取
                                </label>
                                <label :class="'btn btn-outline-secondary'+(model.data.type==1?' active':'')">
                                    <input type="radio" name="data[type]" :value="1" autocomplete="off" v-model="model.data.type">手动选择
                                </label>
                            </div>
                        </div>
                        <div v-show="model.data.type==0" class="form-group col">
                            <div class="input-group">
                                <span class="input-group-prepend"><span class="input-group-text">显示数量</span> </span>
                                <input type="text" class="form-control" name="data[count]" v-model="model.data.count"/>
                            </div>
                        </div>
                    </div>
                    <div v-show="model.data.type==0" class="form-row">
                        <div class="form-group col">
                            <div class="input-group">
                                <span class="input-group-prepend"><span class="input-group-text">文章分类</span> </span>
                                <select name="data[category_id]" class="form-control" v-model="model.data.category_id">
                                    <option :value="0">全部分类</option>
                                    <option v-for="cate in category" :value="cate.id">{{cate.html}}{{cate.title}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col">
                            <div class="btn-group btn-group-toggle" >
                                <label :class="'btn btn-outline-secondary'+(model.data.filter_type?'':' active')">
                                    <input type="radio" name="data[filter_type]" value="" v-model="model.data.filter_type" autocomplete="off" >不限
                                </label>
                                {volist name="article_types" id="type" key="k"}
                                    <label :class="'btn btn-outline-secondary'+(model.data.filter_type=={$key}?' active':'')">
                                        <input type="radio" name="data[filter_type]" :value="{$key}" v-model="model.data.filter_type" autocomplete="off" >{$type}
                                    </label>
                                {/volist}
                            </div>
                        </div>
                    </div>
                    <div v-show="model.data.type==1" class="form-row">
                        <div v-for="(item, index) in list_article"  class="col-6 col-md-4 col-lg-3">
                            <input type="hidden" name="data[article_ids][]" :value="item.id" />
                            <div class="card mb-2">
                                <button type="button" class="close" aria-label="移除" @click.stop="removeThis(index)">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <button type="button" class="close swap" aria-label="交换" @click.stop="swapThis(index)">
                                    <i aria-hidden="true" class="ion-md-swap"></i>
                                </button>
                                <img v-if="item.cover" :src="item.cover" class="card-img-top" :alt="item.title">
                                <div class="card-body">
                                    <h5 class="card-title">{{item.title}}</h5>
                                    <p class="card-text" v-html="item.description"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="card border-0" @click="pickArticle">
                                <i class="ion-md-add border" style="font-size: 60px;line-height:1em;width:60px;text-align: center"></i>
                                <div class="card-body">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {if in_array('shop',$modules) !== false}
            <div v-else-if="model.type=='product_category'" class="card">
                <div class="card-header"><h4 class="card-title">商品分类</h4></div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col">
                            <div class="btn-group btn-group-toggle" >
                                <label :class="'btn btn-outline-secondary'+(model.data.type==0?' active':'')">
                                    <input type="radio" name="data[type]" :value="0" autocomplete="off" v-model="model.data.type">自动获取
                                </label>
                                <label :class="'btn btn-outline-secondary'+(model.data.type==1?' active':'')">
                                    <input type="radio" name="data[type]" :value="1" autocomplete="off" v-model="model.data.type">手动选择
                                </label>
                            </div>
                        </div>
                        <div v-show="model.data.type==0" class="form-group col">
                            <div class="input-group">
                                <span class="input-group-prepend"><span class="input-group-text">显示数量</span> </span>
                                <input type="text" class="form-control" name="data[count]" v-model="model.data.count"/>
                            </div>
                        </div>
                    </div>
                    <div v-show="model.data.type==0" class="form-group">
                        <div class="input-group">
                            <span class="input-group-prepend"><span class="input-group-text">上级分类</span> </span>
                            <select name="data[parent_id]" class="form-control" v-model="model.data.parent_id">
                                <option :value="0">顶级分类</option>
                                <option v-for="cate in product_category" :value="cate.id">{{cate.html}}{{cate.title}}</option>
                            </select>
                        </div>
                    </div>
                    <div v-show="model.data.type==1" class="form-row">
                        <div v-for="(item, index) in list_product_category"  class="col-6 col-md-4 col-lg-3">
                            <input type="hidden" name="data[product_category_ids][]" :value="item.id" />
                            <div class="card  mb-2">
                                <button type="button" class="close" aria-label="移除" @click.stop="removeThis(index)">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <button type="button" class="close swap" aria-label="交换" @click.stop="swapThis(index)">
                                    <i aria-hidden="true" class="ion-md-swap"></i>
                                </button>
                                <img v-if="item.icon" :src="item.icon" class="card-img-top" :alt="item.title">
                                <div class="card-body">
                                    <h5 class="card-title">{{item.title}}</h5>
                                    <p class="card-text"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="card border-0" @click="pickProductCate">
                                <i class="ion-md-add border" style="font-size: 60px;line-height:1em;width:60px;text-align: center"></i>
                                <div class="card-body">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div v-else-if="model.type=='product'" class="card">
                <div class="card-header"><h4 class="card-title">商品</h4></div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col">
                            <div class="btn-group btn-group-toggle" >
                                <label :class="'btn btn-outline-secondary'+(model.data.type==0?' active':'')">
                                    <input type="radio" name="data[type]" :value="0" autocomplete="off" v-model="model.data.type">自动获取
                                </label>
                                <label :class="'btn btn-outline-secondary'+(model.data.type==1?' active':'')">
                                    <input type="radio" name="data[type]" :value="1" autocomplete="off" v-model="model.data.type">手动选择
                                </label>
                            </div>
                        </div>
                        <div v-show="model.data.type==0" class="form-group col">
                            <div class="input-group">
                                <span class="input-group-prepend"><span class="input-group-text">显示数量</span> </span>
                                <input type="text" class="form-control" name="data[count]" v-model="model.data.count"/>
                                <span class="input-group-middle"><span class="input-group-text">携带产品数量</span> </span>
                                <input type="text" class="form-control" name="data[product_count]" v-model="model.data.product_count"/>
                                <span class="input-group-middle"><span class="input-group-text">排序</span> </span>
                                <select class="form-control" v-model="model.data.product_sort" name="data[product_sort]">
                                    <option value="">默认排序</option>
                                    <option value="update_time desc" >更新时间</option>
                                    <option value="min_price asc">价格升序</option>
                                    <option value="min_price desc">价格降序</option>
                                    <option value="sales desc">销售量</option>
                                    <option value="goods_no asc">货号</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div v-show="model.data.type==0" class="form-group">
                        <div class="input-group">
                            <span class="input-group-prepend"><span class="input-group-text">商品分类</span> </span>
                            <select name="data[category_id]" class="form-control" v-model="model.data.category_id">
                                <option :value="0">全部分类</option>
                                <option v-for="cate in product_category" :value="cate.id">{{cate.html}}{{cate.title}}</option>
                            </select>
                        </div>
                    </div>
                    <div v-show="model.data.type==1" class="form-row">
                        <div v-for="(item, index) in list_product"  class="col-6 col-md-4 col-lg-3">
                            <input type="hidden" name="data[product_ids][]" :value="item.id" />
                            <div class="card mb-2">
                                <button type="button" class="close" aria-label="移除" @click.stop="removeThis(index)">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <button type="button" class="close swap" aria-label="交换" @click.stop="swapThis(index)">
                                    <i aria-hidden="true" class="ion-md-swap"></i>
                                </button>
                                <img v-if="item.image" :src="item.image" class="card-img-top" :alt="item.title">
                                <div class="card-body">
                                    <h5 class="card-title">{{item.title}}</h5>
                                    <p class="card-text">{{item.min_price}} ~ {{item.max_price}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="card border-0" @click="pickProduct">
                                <i class="ion-md-add border" style="font-size: 60px;line-height:1em;width:60px;text-align: center"></i>
                                <div class="card-body">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else-if="model.type=='brand'" class="card">
                <div class="card-header"><h4 class="card-title">品牌</h4></div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col">
                            <div class="btn-group btn-group-toggle" >
                                <label :class="'btn btn-outline-secondary'+(model.data.type==0?' active':'')">
                                    <input type="radio" name="data[type]" :value="0" autocomplete="off" v-model="model.data.type">自动获取
                                </label>
                                <label :class="'btn btn-outline-secondary'+(model.data.type==1?' active':'')">
                                    <input type="radio" name="data[type]" :value="1" autocomplete="off" v-model="model.data.type">手动选择
                                </label>
                            </div>
                        </div>
                        <div v-show="model.data.type==0" class="form-group col">
                            <div class="input-group">
                                <span class="input-group-prepend"><span class="input-group-text">显示数量</span> </span>
                                <input type="text" class="form-control" name="data[count]" v-model="model.data.count"/>
                            </div>
                        </div>
                    </div>
                    <div v-show="model.data.type==1" class="form-row">
                        <div v-for="(item, index) in list_brand"  class="col-4 col-md-3 col-lg-2">
                            <input type="hidden" name="data[brand_ids][]" :value="item.id" />
                            <div class="card mb-2">
                                <button type="button" class="close" aria-label="移除" @click.stop="removeThis(index)">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <button type="button" class="close swap" aria-label="交换" @click.stop="swapThis(index)">
                                    <i aria-hidden="true" class="ion-md-swap"></i>
                                </button>
                                <img v-if="item.logo" :src="item.logo" class="card-img-top" :alt="item.title">
                                <div class="card-body">
                                    <h5 class="card-title">{{item.title}}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="card border-0" @click="pickBrand">
                                <i class="ion-md-add border" style="font-size: 60px;line-height:1em;width:60px;text-align: center"></i>
                                <div class="card-body">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {/if}
            <div v-else-if="model.type=='ad'" class="card">
                <div class="card-header"><h4 class="card-title">广告位</h4></div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col">
                        <div class="input-group">
                            <span class="input-group-prepend"><span class="input-group-text">广告位</span> </span>
                            <input type="text" class="form-control" name="data[ad_flag]" v-model="model.data.ad_flag"/>
                            <span class="input-group-append"><button class="btn btn-outline-primary" @click="pickAd"><i class="ion-md-search"></i> </button> </span>
                        </div>
                        </div>
                        <div class="form-group col">
                        <div class="input-group">
                            <span class="input-group-prepend"><span class="input-group-text">显示数量</span> </span>
                            <input type="text" class="form-control" name="data[count]" v-model="model.data.count"/>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="card">
                <div class="card-body">
                    <div class="empty">请选择展位类型</div>
                </div>
            </div>
            <div class="form-group submit-btn">
                <input type="hidden" name="id" value="{$model.id}">
                <button type="submit" class="btn btn-primary">{$id>0?'保存':'添加'}</button>
            </div>
        </form>
        </div>
    </div>
    {/block}

{block name="script"}
    <script type="text/plain" id="category_json">{:json_encode(\\app\\common\\facade\\CategoryFacade::getCategories())}</script>
    <script type="text/plain" id="product_category_json">{:json_encode(\\app\\common\\facade\\ProductCategoryFacade::getCategories())}</script>
    <script type="text/plain" id="cur_list">{:json_encode($model['id']>0?$model->fetchData():'')}</script>
    <script type="text/javascript" src="__STATIC__/vue/2.6/vue.min.js"></script>
    <script type="text/javascript">
        var app = new Vue({
            el: '#page-wrapper',
            data: {
                model:{
                    status:1,
                    type:"",
                    data:{
                        type:0,
                        parent_id:0,
                        category_id:0
                    }
                },
                list_category:[],
                list_article:[],
                list_product_category:[],
                list_product:[],
                category:[],
                product_category:[]
            },
            mounted:function() {
                this.loadData();
            },
            methods: {
                loadData:function () {
                    var self=this;
                    self.category = JSON.parse($('#category_json').text())
                    self.product_category = JSON.parse($('#product_category_json').text())

                    jQuery.ajax({
                        url:'',
                        type:'GET',
                        dataType:'JSON',
                        success:function (json) {
                            if(json.code==1) {
                                self.model=json.data.model
                                var curjson=$('#cur_list').text()
                                if(curjson){
                                    var list = JSON.parse(curjson)
                                    if(list && list.length>0) {
                                        if (self.model.type == 'article') {
                                            self.list_article = list
                                        }else if (self.model.type == 'category') {
                                            self.list_category = list
                                        }else if (self.model.type == 'product') {
                                            self.list_product = list
                                        }else if (self.model.type == 'product_category') {
                                            self.list_product_category = list
                                        }
                                    }
                                }
                            }
                        }
                    })
                },
                pickCate:function (e) {
                    dialog.pickList({
                        isajax:false,
                        list:this.category,
                        rowTemplate:'<a href="javascript:" data-id="{@id}" class="list-group-item list-group-item-action pt-0 pb-0" style="line-height:30px;">{@html}&nbsp;[{@id}]&nbsp;{@title}</a>'
                    },function (category) {
                        app.list_category.push(category)
                    })
                },
                pickArticle:function (e) {
                    dialog.pickArticle(function (article) {
                        app.list_article.push(article)
                    })
                },
                pickProductCate:function (e) {
                    dialog.pickList({
                        isajax:false,
                        list:this.product_category,
                        rowTemplate:'<a href="javascript:" data-id="{@id}" class="list-group-item list-group-item-action pt-0 pb-0" style="line-height:30px;">{@html}&nbsp;[{@id}]&nbsp;{@title}</a>'
                    },function (category) {
                        app.list_product_category.push(category)
                    })
                },
                pickProduct:function (e) {
                    dialog.pickProduct(function (product) {
                        app.list_product.push(product)
                    })
                },
                pickAd:function (e) {
                    e.stopPropagation()
                    e.preventDefault()
                    var self=this
                    dialog.pickList("{:url('adv/search')}",function (adv) {
                        Vue.set(self.model.data,'ad_flag',adv.flag)
                    })
                },
                removeThis:function(idx){
                    var self = this;
                    dialog.confirm('移除这块数据？',function(){
                        if (self.model.type == 'article') {
                            self.list_article.splice(idx, 1)
                        }else if (self.model.type == 'category') {
                            self.list_category.splice(idx, 1)
                        }else if (self.model.type == 'product') {
                            self.list_product.splice(idx, 1)
                        }else if (self.model.type == 'product_category') {
                            self.list_product_category.splice(idx, 1)
                        }else if (self.model.type == 'brand') {
                            self.list_brand.splice(idx, 1)
                        }
                    })
                },
                swapThis:function(idx){
                    var self = this;
                    if(idx == 0)idx += 1;
                    if(idx > 0 && this['list_'+this.model.type].length>1){
                        var tempList = this['list_'+this.model.type];
                        // console.log(tempList)
                        var temp = tempList[idx];
                        tempList[idx] = tempList[idx-1];
                        tempList[idx-1] = temp;
                        Vue.set(this, 'list_'+this.model.type, tempList)
                        this.$forceUpdate()
                    }
                }
            }
        });
    </script>
{/block}
