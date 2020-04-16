<?php

namespace app\common\model;


use app\common\core\BaseModel;
use think\Db;

class MemberFavouriteModel extends BaseModel
{
    const TYPE_PRODUCT='product';
    const TYPE_ARTICLE='article';

    protected $autoWriteTimestamp = true;

    public function getFavourites($type)
    {
        $model = Db::view('memberFavourite','*');
        if($type == static::TYPE_PRODUCT){
            $model->view('product','title,image,min_price,max_price')
            ->where('memberFavourite.fav_type',$type);
        }elseif($type == static::TYPE_ARTICLE){
            $model->view('product','title,image,min_price,max_price')
            ->where('memberFavourite.fav_type',$type);
        }else{
            throw new \Exception('Unsupported favourite type '.$type);
        }
        return $model->paginate();
    }

    public function isFavourite($member_id,$type,$id){
        return Db::name('memberFavourite')
            ->where('member_id',$member_id)
            ->where('fav_type',$type)
            ->where('fav_id',$id)
            ->count();
    }
    public function removeFavourite($member_id,$type,$id){
        return Db::name('memberFavourite')
            ->where('member_id',$member_id)
            ->where('fav_type',$type)
            ->where('fav_id',$id)
            ->delete();
    }
    public function addFavourite($member_id,$type,$id){
        $exist=Db::name('memberFavourite')
            ->where('member_id',$member_id)
            ->where('fav_type',$type)
            ->where('fav_id',$id)
            ->find();

        $data=[
            'member_id'=>$member_id,
            'fav_type'=>$type,
            'fav_id'=>$id
        ];
        switch ($type){
            case 'product':
                $product=Db::name('product')->where('id',$id)->find();
                if(empty($product) || $product['status'] != 1){
                    $this->error='要收藏的产品不存在';
                    return false;
                }
                $data['fav_image']=$product['image'];
                $data['fav_title']=$product['title'];
                break;
            case 'article':
                $article=Db::name('article')->where('id',$id)->find();
                if(empty($article) || $article['status'] != 1){
                    $this->error='要收藏的文章不存在';
                    return false;
                }
                $data['fav_image']=$article['cover'];
                $data['fav_title']=$article['title'];
                break;
            default:
                $this->error='收藏失败';
                return false;
        }
        if(empty($exist)){
            self::create($data);
        }else{
            self::update($data,['id'=>$exist['id']]);
        }
        return true;
    }
}