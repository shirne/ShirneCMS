<?php
namespace app\common\model;

use app\common\core\ContentModel;
use app\common\facade\ProductCategoryFacade;
use think\Db;
use think\Paginator;


/**
 * Class ProductModel
 * @package app\common\model
 */
class ProductModel extends ContentModel
{
    protected $autoWriteTimestamp = true;
    protected $type = ['levels'=>'array','spec_data'=>'array','prop_data'=>'array','commission_percent'=>'array'];

    function __construct($data = [])
    {
        parent::__construct($data);
        $this->cateFacade=ProductCategoryFacade::getFacadeInstance();
        $this->searchFields = 'title|vice_title|goods_no';
    }

    private static $specifications;
    public static function getSpecList(){
        if (empty(self::$specifications)) {
            self::$specifications = cache('specifications');
            if (empty(self::$specifications)) {
                $data =  Db::name('specifications')->field(['id','title'])->order('id ASC')->select();
                self::$specifications=array_index($data,'id,title');
                cache('specifications', self::$specifications);
            }
        }
        return self::$specifications;
    }
    
    protected function tagBaseView($model){
        return $model->view('productBrand',['title'=>'brand_title','logo'=>'brand_logo'],$this->model.'.brand_id=productBrand.id','LEFT');
    }

    protected function onFilter($model, $attrs){
        if(!empty($attrs['brand'])){
            if(strpos($attrs['brand'],',')>0){
                $model->whereIn($this->model . ".brand_id", idArr($attrs['brand']));
            }else {
                $model->where($this->model . ".brand_id", intval($attrs['brand']));
            }
        }
        if(!empty($attrs['shop'])){
            if(strpos($attrs['shop'],',')>0){
                $model->whereIn($this->model . ".shop_id", idArr($attrs['shop']));
            }else {
                $model->where($this->model . ".shop_id", intval($attrs['shop']));
            }
        }
        if(!empty($attrs['member'])){
            if(strpos($attrs['member'],',')>0){
                $model->whereIn($this->model . ".member_id", idArr($attrs['member']));
            }else {
                $model->where($this->model . ".member_id", intval($attrs['member']));
            }
        }
        if(!empty($attrs['province'])){
            $model->where(function($query)use($attrs){
                $query->where($this->model . ".province", $attrs['province'])->whereOr($this->model . ".province",'');
            });
        }
        if(!empty($attrs['city'])){
            $model->where(function($query)use($attrs){
                $query->where($this->model . ".city", $attrs['city'])->whereOr($this->model . ".city",'');
            });
        }
    }
    
    /**
     * @param array|Paginator $lists
     * @param array $attrs
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function afterTagList($lists,$attrs){
        if(!empty($lists)){
            $pids = array_column(is_array($lists)?$lists:$lists->items(),'id');
            $append=[];
            if(!empty($attrs['withsku'])){
                $skus=ProductSkuModel::whereIn('product_id',$pids)->select();
                $skus = array_index($skus,'product_id',true);
                $append['skus']=$skus;
            }
            if(!empty($attrs['withimgs'])){
                $imgs=Db::name('productImages')->whereIn('product_id',$pids)->select();
                $imgs = array_index($imgs,'product_id',true);
                $append['imgs']=$imgs;
            }
            $lists = $this->appendTagData($lists, $append);
        }
        return $lists;
    }
    protected function afterTagItem($item,$attrs=[]){
        $item['sale']=$item['sale']+intval($item['v_sale']);
        return $item;
    }

    public static function getForOrder($skucounts){
        if(empty($skucounts))return [];
        $sku_ids = array_keys($skucounts);
        $products=Db::view('ProductSku','*')
            ->view('Product',['title'=>'product_title','spec_data','image'=>'product_image','store_id','cate_id','status','levels','is_discount','postage_id','is_commission','commission_percent','type','level_id','max_buy','max_buy_cycle'],'ProductSku.product_id=Product.id','LEFT')
            ->whereIn('ProductSku.sku_id',idArr($sku_ids))
            ->select();
    
        foreach ($products as $k=>&$item){
            $item['product_price']=$item['price'];
            $item['product_cost_price']=$item['cost_price'];
        
            if(!empty($item['image']))$item['product_image']=$item['image'];
            if(isset($skucounts[$item['sku_id']])){
                $item['count']=$skucounts[$item['sku_id']];
            }else{
                $item['count']=1;
            }
            $item['commission_percent']=force_json_decode($item['commission_percent']);
            $item['spec_data'] = force_json_decode($item['spec_data']);
            $item['specs'] = force_json_decode($item['specs']);
            $item['levels']=force_json_decode($item['levels']);
            $item['ext_price']=force_json_decode($item['ext_price']);
            
        }
        unset($item);
        return $products;
    }
    
    public static function transSpec($sku_specs){
        $transed=[];
        if(!empty($sku_specs)) {
            $specs = self::getSpecList();
            $unindex = 0;
            foreach ($sku_specs as $id => $value) {
                if (isset($specs[$id])) {
                    $transed[$specs[$id]] = $value;
                } else {
                    $transed['unknown-' . $unindex++] = $value;
                }
            }
        }
        return $transed;
    }

    public static function setFlash($id, $date){
        $product = Db::name('product')->where('id',$id)->find();
        if(empty($product)){
            return false;
        }
        $hasFlash = static::getFlash($id, $date);
        if(!empty($hasFlash)){
            return true;
        }
        $flashData=[
            'product_id'=>$id,
            'timestamp'=>$date,
            'title'=>$product['title'],
            'product'=>json_encode($product,JSON_UNESCAPED_UNICODE),
            'brand'=>json_encode(Db::name('productBrand')->where('id',$product['brand_id'])->find(),JSON_UNESCAPED_UNICODE),
            'skus'=>json_encode(Db::name('productSku')->where('product_id',$id)->select(),JSON_UNESCAPED_UNICODE),
            'images'=>json_encode(Db::name('productImages')->where('product_id',$id)->select(),JSON_UNESCAPED_UNICODE),
        ];
        Db::name('productFlash')->insert($flashData);
        return true;
    }

    public static function getFlash($id, $date){
        return Db::name('productFlash')->where('product_id',$id)
            ->where('timestamp',$date)->find();
    }
}