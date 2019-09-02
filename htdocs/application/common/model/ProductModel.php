<?php
namespace app\common\model;

use app\common\facade\ProductCategoryFacade;
use think\Db;


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

    public static function getForOrder($skucounts){
        if(empty($skucounts))return [];
        $sku_ids = array_keys($skucounts);
        $products=Db::view('ProductSku','*')
            ->view('Product',['title'=>'product_title','spec_data','image'=>'product_image','levels','is_discount','is_commission','commission_percent','type','level_id'],'ProductSku.product_id=Product.id','LEFT')
            ->whereIn('ProductSku.sku_id',idArr($sku_ids))
            ->select();
    
        foreach ($products as $k=>&$item){
            $item['product_price']=$item['price'];
        
            if(!empty($item['image']))$item['product_image']=$item['image'];
            if(isset($counts[$item['sku_id']])){
                $item['count']=$skucounts[$item['sku_id']];
            }else{
                $item['count']=1;
            }
            if (!empty($item['spec_data'])) {
                $item['spec_data'] = json_decode($item['spec_data'], true);
            } else {
                $item['spec_data'] = [];
            }
            if (!empty($item['specs'])) {
                $item['specs'] = json_decode($item['specs'], true);
            } else {
                $item['specs'] = [];
            }
            if(!empty($item['levels'])){
                $item['levels']=json_decode($item['levels'],true);
            }
            if(!empty($item['ext_price'])){
                $item['ext_price']=json_decode($item['ext_price'],true);
            }
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
}