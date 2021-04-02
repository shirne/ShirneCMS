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
use think\facade\Db;
use think\facade\Log;

/**
 * 产品操作接口
 * Class ProductController
 * @package app\api\Controller
 */
class ProductController extends BaseController
{
    public function get_all_cates(){
        return $this->response(ProductCategoryFacade::getTreedCategory());
    }

    public function get_cates($pid=0, $goods_count=0, $withsku=0, $filters=[]){
        if($pid!=0 && preg_match('/^[a-zA-Z]\w+/',$pid)){
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
            'lists'=>$lists->all(),
            'page'=>$lists->currentPage(),
            'total'=>$lists->total(),
            'total_page'=>$lists->lastPage(),
        ]);
    }

    public function view($id){
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

    public function flash($id, $date){
        $flash = ProductModel::getFlash($id,$date);
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
            'flashDate'=>$flash['timestamp']
        ]);
    }

    public function share($id, $type='url'){
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
        $config=config('share.');
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

    private function miniprogramQrcode($wechatid, $params, $size){
        $app = WechatModel::createApp($wechatid);
        if(!$app){
            $this->error('小程序账号错误');
        }
        $response = $app->app_code->getUnlimit(http_build_query($params['scene']), ['page'=>$params['path'],'width'=>$size]);
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            return $response->getBody()->getContents();
        }
        Log::record(var_export($response,true));
        $this->error('小程序码生成失败');
    }


    public function comments($id){
        $product = ProductModel::get($id);
        if(empty($product)){
            $this->error('参数错误');
        }
        $comments=Db::view('productComment','*')
            ->view('member',['username','realname','avatar'],'member.id=productComment.member_id','LEFT')
            ->where('productComment.status',1)
            ->where('product_id',$id)
            ->order('productComment.create_time desc')->paginate(10);

        return $this->response([
            'lists'=>$comments->all(),
            'page'=>$comments->currentPage(),
            'total'=>$comments->total(),
            'total_page'=>$comments->lastPage(),
        ]);
    }
}