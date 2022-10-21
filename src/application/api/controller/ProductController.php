<?php

namespace app\api\controller;

use app\common\facade\MemberFavouriteFacade;
use app\common\facade\ProductCategoryFacade;
use app\common\model\MemberLevelModel;
use app\common\model\PostageModel;
use app\common\model\ProductModel;
use app\common\model\WechatModel;
use app\common\model\ProductSkuModel;
use shirne\common\Poster;
use think\Db;
use think\facade\Log;
use think\response\Json;

/**
 * 产品操作接口
 * Class ProductController
 * @package app\api\Controller
 */
class ProductController extends BaseController
{
    /**
     * 获取全部商品分类
     * 格式
     *   0 => 顶级类列表
     *   id => 子类列表
     *   ...
     * @return Json 
     */
    public function get_all_cates(){
        return $this->response(ProductCategoryFacade::getTreedCategory());
    }

    /**
     * 获取指定id的子类，可携带指定数量和筛选条件的商品
     * @param int $pid 
     * @param int $goods_count 商品数量
     * @param int $withsku 是否携带sku信息
     * @param array $filters 携带商品列表的筛选条件
     * @return Json 
     */
    public function get_cates($pid=0, $goods_count=0, $withsku=0, $filters=[]){
        if($pid!=0 || preg_match('/^[a-zA-Z]\w+/',$pid)){
            $current=ProductCategoryFacade::findCategory($pid);
            if(empty($current)){
                return $this->response([]);
            }
            $pid=$current['id'];
        }
        $cates = ProductCategoryFacade::getSubCategory($pid);
        if($goods_count > 0){
            $product = ProductModel::getInstance();
            $filters['limit']=$goods_count;
            if(!isset($filters['recursive'])){
                $filters['recursive']=1;
            }
            if($withsku){
                $filters['withsku']=$withsku;
            }
            foreach($cates as &$cate){
                $filters['category']=$cate['id'];
                $cate['products']=$product->tagList($filters);
            }
            unset($cate);
        }
        return $this->response($cates);
    }

    /**
     * 获取商品列表，可分页
     * @param string $cate 指定所属的分类，默认包含子类
     * @param string $type 指定商品类型
     * @param string $order 指定排序
     * @param string $keyword 指定关键字
     * @param int $withsku 是否携带sku信息
     * @param int $page 指定分页
     * @param int $pagesize 指定获取数量，分页时为每页大小
     * @return Json 
     */
    public function get_list($cate='',$type='',$order='',$keyword='',$withsku=0,$page=1, $pagesize=10){
        $condition=[];
        if($cate){
            $condition['category']=$cate;
            $condition['recursive']=1;
        }
        if(!empty($order)){
            $condition['order']=$order;
        }
        if(!empty($keyword)){
            $condition['keyword']=$keyword;
        }
        if(!empty($type)){
            $condition['type']=$type;
        }
        if(!empty($withsku)){
            $condition['withsku']=$withsku;
        }
        $condition['page']=$page;
        $condition['pagesize']=$pagesize;
        
        $lists = ProductModel::getInstance()->tagList($condition, true);
        
        if(!empty($lists) && !$lists->isEmpty()) {
            $levels = getMemberLevels();
    
            $lists->each(function ($item) use ($levels) {
                if ($item['level_id']) {
                    $item['level_name'] = $levels[$item['level_id']]['level_name'] ?: '';
                }
        
                return $item;
            });
        }

        return $this->response([
            'lists'=>$lists->items(),
            'page'=>$lists->currentPage(),
            'total'=>$lists->total(),
            'total_page'=>$lists->lastPage(),
        ]);
    }

    /**
     * 获取品牌列表
     * @param int $id 
     * @return Json 
     */
    public function brands($cate = 0){
        $lists = Db::name('productBrand')->order('sort asc')->select();
        return $this->response([
            'lists'=>$lists,
        ]);
    }

    /**
     * 获取商品详情
     * @param int $id 
     * @return Json 
     */
    public function view($id){
        $id = intval($id);
        $product = ProductModel::get($id);
        if(empty($product)){
            $this->error('商品不存在');
        }

        $skus=ProductSkuModel::where('product_id',$product['id'])->select();
        $images=Db::name('ProductImages')->where('product_id',$product['id'])->select();
        if(!empty($product['levels'])){
            $levels=MemberLevelModel::getCacheData();
            $level_names=[];
            foreach($product['levels'] as $lvid){
                $level_names[] = $levels[intval($lvid)]['level_name'];
            }
            $product['level_names']=$level_names;
        }

        $isFavourite=$this->isLogin?MemberFavouriteFacade::isFavourite($this->user['id'],'product',$id):0;

        return $this->response([
            'product'=>$product,
            'is_favourite'=>$isFavourite,
            'postage'=>PostageModel::getDesc($product['postage_id']),
            'skus'=>$skus,
            'images'=>$images
        ]);
    }

    /**
     * 获取商品快照，快照根据时间戳生成，即订单下单时间
     * @param int $id 
     * @param int $time 
     * @return void|Json 
     */
    public function flash($id, $time){
        $flash = ProductModel::getFlash($id,$time);
        if(empty($flash)){
            return $this->error('商品快照不存在');
        }
        $product = json_decode($flash['product'],true);

        $skus=json_decode($flash['skus'],true);
        $images=json_decode($flash['images'],true);

        return $this->response([
            'product'=>$product,
            'skus'=>$skus,
            'images'=>$images,
            'flash_date'=>$flash['timestamp']
        ]);
    }

    private function get_share_config(){
        $config = [];
        $sysconfig=getSettings(false,true);
        $shareConfig=$sysconfig['share'];
        if(empty($shareConfig) || empty($shareConfig['share_background'])){
            return false;
        }

        $config['background']='.'.$shareConfig['share_background'];
        $config['data']['avatar']=$shareConfig['share_avatar'];
        $config['data']['avatar']['type']='image';
        $config['data']['qrcode']=$shareConfig['share_qrcode'];
        $config['data']['qrcode']['type']='image';
        
        $config['data']['image']=$shareConfig['share_image'];
        $config['data']['image']['type']='image';

        if($shareConfig['share_bgset'] == 1){
            $config['data']['bg']=['type'=>'background'];
        }
        $config['data']['title']=$shareConfig['share_title'];
        $config['data']['vice_title']=$shareConfig['vice_title'];
        $config['data']['price']=$shareConfig['share_price'];
        $config['data']['nickname']=$shareConfig['share_nickname'];
        if(!empty($shareConfig['share_qrlogo'])){
            $config['data']['qrlogo']=$shareConfig['share_qrcode'];
            $config['data']['qrlogo']['type']='image';
            $config['data']['qrlogo']['value']='.'.$shareConfig['share_qrlogo'];
        }
        return $config;
    }
    /**
     * 获取商品分享海报，支持web，公众号，小程序
     * @param mixed $id 
     * @param string $type 
     * @return Json 
     */
    public function share($id, $type='url'){
        $id = intval($id);
        $product = ProductModel::get($id);
        if(empty($product)){
            $this->error('商品不存在');
        }
        
        $data=[
            'avatar'=>'',
            'nickname'=>'',
            'image'=>'.'.$product['image'],
            'title'=>$product['title'],
            'vice_title'=>$product['vice_title'],
            'price'=>$product['min_price'],
            'qrcode'=>''
        ];
        if(strpos($data['title'],'【')==0 && strpos($data['title'],'】')>0){
            $data['title'] = mb_substr($data['title'],mb_strpos($data['title'],'】')+1);
        }

        if(!in_array($type,['url','miniqr'])){
            $this->error('分享图类型错误');
        }
        $params=['id'=>$id];
        if($this->isLogin && $this->user['is_agent'] > 0){
            $data['avatar']=$this->user['avatar'];
            $data['nickname']=$this->user['nickname'];
            $params['agent']=$this->user['agentcode'];
            $qrurl = './uploads/pshare/'.$id.'/'.($this->user['id']%100).'/'.$this->user['agentcode'].'-'.$type.'-p'.$id.'-qrcode.jpg';
            $sharepath = './uploads/pshare/'.$id.'/'.($this->user['id']%100).'/'.$this->user['agentcode'].'-'.$type.'-p'.$id.'.jpg';
        }else{
            $data['avatar']='.'.$this->config['site-weblogo'];
            $data['nickname']=$this->config['site-name'];
            $qrurl = './uploads/pshare/'.$id.'/share-qrcode-'.$type.'.png';
            $sharepath = './uploads/pshare/'.$id.'/share-'.$type.'.png';
        }
        $imgurl = media(ltrim($sharepath,'.'));
        $config=$this->get_share_config();
        if(empty($config) || empty($config['background'])){
            $this->error('请配置产品海报生成样式(config/share.php)');
        }
        if(!file_exists($sharepath) || 
            filemtime($sharepath) < $product['update_time'] || 
            filemtime($sharepath) < $this->user['update_time'] ||
            filemtime($sharepath) < filemtime($config['background'] )){

            if($type == 'url'){
                $url = url('index/product/view',$params, true, true);
                $content=gener_qrcode($url, 430);
            }else{
                $appid=$this->request->tokenData['appid'];
                $wechat=WechatModel::where('appid',$appid)->find();
                if(empty($wechat)){
                    $this->error('分享图生成失败(wechat)');
                }
                $content = $this->miniprogramQrcode($wechat, ['path'=>'pages/product/detail', 'scene'=> $params], 430);
            }
            $dir = dirname($qrurl);
            if(!is_dir($dir)){
                mkdir($dir,0777,true);
            }
            file_put_contents($qrurl,$content);
            $data['qrcode']=$qrurl;

            $dir = dirname($sharepath);
            if(!is_dir($dir)){
                mkdir($dir,0777,true);
            }

            
            $poster = new Poster($config);
            if($poster->generate($data)){
                $poster->save($sharepath);
                $imgurl .= '?_t='.time();
            }else{
                $this->error('分享图生成失败');
            }
        }else{
            $imgurl .= '?_t='.filemtime($sharepath);
        }
        
        return $this->response(['share_url'=>$imgurl]);
    }

    /**
     * 生成小程序码
     * @param mixed $wechatid 
     * @param mixed $params 
     * @param mixed $size 
     * @return string|void 
     */
    private function miniprogramQrcode($wechatid, $params, $size){
        $app = WechatModel::createApp($wechatid);
        if(!$app){
            $this->error('小程序账号错误');
        }
        $response = $app->app_code->getUnlimit(http_build_query($params['scene']), ['page'=>$params['path'],'width'=>$size]);
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            return $response->getBody()->getContents();
        }
        Log::warning(var_export($response,true));
        $this->error('小程序码生成失败');
    }

    /**
     * 获取评论列表
     * @param int $id 商品id
     * @param int $pagesize 默认10
     * @param int $page 
     * @return Json 
     */
    public function comments($id, $pagesize = 10){
        $id = intval($id);
        $product = ProductModel::get($id);
        if(empty($product)){
            $this->error('参数错误');
        }
        $comments=Db::view('productComment','*')
            ->view('member',['username','realname','avatar'],'member.id=productComment.member_id','LEFT')
            ->where('productComment.status',1)
            ->where('product_id',$id)
            ->order('productComment.create_time desc')->paginate($pagesize);

        return $this->response([
            'lists'=>$comments->items(),
            'page'=>$comments->currentPage(),
            'total'=>$comments->total(),
            'total_page'=>$comments->lastPage(),
        ]);
    }
}