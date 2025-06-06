<?php

namespace app\admin\controller\shop;

use app\admin\controller\BaseController;
use app\admin\model\SpecificationsModel;
use app\common\model\PostageModel;
use app\common\model\ProductModel;
use app\common\model\ProductSkuModel;
use app\admin\validate\ProductSkuValidate;
use app\admin\validate\ProductValidate;
use app\admin\validate\ImagesValidate;
use app\common\facade\ProductCategoryFacade;
use app\common\model\WechatModel;
use stdClass;
use think\Db;

/**
 * 商品管理
 * Class ProductController
 * @package app\admin\controller\shop
 */
class ProductController extends BaseController
{

    public function search($key = '', $cate = 0, $brand = 0, $type = 0, $searchtype = '')
    {
        if ($searchtype == 'sku') {
            return $this->searchSku($key, $cate, $brand, $type);
        }
        return $this->searchProduct($key, $cate, $brand, $type);
    }

    private function searchSku($key = '', $cate = 0, $brand = 0, $type = 0)
    {
        $model = Db::view('productSku', 'sku_id,goods_no as sku_goods_no,price')
            ->view('product', 'id,title,image,min_price,max_price,goods_no,create_time', 'product.id=productSku.sku_id')
            ->where('product.status', 1)
            ->where('product.delete_time', 0);
        if (!empty($key)) {
            $model->where('product.id|product.title|product.vice_title|product.goods_no|productSku.goods_no', 'like', "%$key%");
        }
        if ($cate > 0) {
            $model->whereIn('product.cate_id', ProductCategoryFacade::getSubCateIds($cate));
        }
        if ($brand > 0) {
            $model->where('product.brand_id', intval($brand));
        }
        if (!empty($type)) {
            $model->where('product.type', $type);
        }

        $lists = $model->order('product.id ASC,productSku.sku_id ASC')->limit(10)->select();
        return json(['data' => $lists, 'code' => 1]);
    }

    private function searchProduct($key = '', $cate = 0, $brand = 0, $type = 0)
    {
        $model = Db::name('product')
            ->where('status', 1)
            ->where('delete_time', 0);
        if (!empty($key)) {
            $model->where('id|title', 'like', "%$key%");
        }
        if ($cate > 0) {
            $model->whereIn('cate_id', ProductCategoryFacade::getSubCateIds($cate));
        }
        if ($brand > 0) {
            $model->where('brand_id', intval($brand));
        }
        if (!empty($type)) {
            $model->where('type', $type);
        }

        $lists = $model->field('id,title,image,min_price,max_price,goods_no,create_time')
            ->order('id ASC')->limit(10)->select();
        return json(['data' => $lists, 'code' => 1]);
    }

    /**
     * 商品列表
     * @param string $key
     * @param int $cate_id
     * @return mixed|\think\response\Redirect
     * @throws \Exception
     */
    public function index($keyword = '',  $cate_id = 0)
    {
        if ($this->request->isPost()) {
            return redirect(url('', ['cate_id' => $cate_id, 'keyword' => base64url_encode($keyword)]));
        }
        $keyword = empty($keyword) ? "" : base64url_decode($keyword);
        $model = Db::view('product', '*')
            ->view('productCategory', ['name' => 'category_name', 'title' => 'category_title'], 'product.cate_id=productCategory.id', 'LEFT')
            ->where('product.delete_time', 0);

        if (!empty($keyword)) {
            $model->whereLike('product.title|productCategory.title', "%$keyword%");
        }
        if ($cate_id > 0) {
            $model->whereIn('product.cate_id', ProductCategoryFacade::getSubCateIds($cate_id));
        }

        $lists = $model->order('create_time DESC')->paginate(10);
        if (!$lists->isEmpty()) {
            $ids = array_column($lists->items(), 'id');
            $skus = Db::name('productSku')->whereIn('product_id', $ids)->select();
            $skugroups = array_index($skus, 'product_id', true);
            $lists->each(function ($item) use ($skugroups) {
                if (isset($skugroups[$item['id']])) {
                    $item['skus'] = $skugroups[$item['id']];
                } else {
                    $item['skus'] = [];
                }
                return $item;
            });
        }

        $this->assign('lists', $lists);
        $this->assign('page', $lists->render());
        $this->assign('types', getProductTypes());
        $this->assign('keyword', $keyword);
        $this->assign('cate_id', $cate_id);
        $this->assign("category", ProductCategoryFacade::getCategories());
        $this->assign("brands", ProductCategoryFacade::getBrands(0));

        return $this->fetch();
    }

    public function set_increment($incre)
    {
        $this->setAutoIncrement('product', $incre);
    }

    public function qrcode($id, $qrtype = 'url', $size = 430, $miniprogram = 0)
    {
        $product = ProductModel::get($id);
        if ($qrtype == 'url') {
            $url = url('index/product/view', ['id' => $id], true, true);
            $content = gener_qrcode($url, $size);
        } else {
            if ($size > 1280) $size = 1280;
            $content = $this->miniprogramQrcode($miniprogram, ['path' => 'pages/product/detail?id=' . $id], $qrtype, $size);
        }
        return download($content, $product['title'] . '-qrcode.png', true);
    }

    private function miniprogramQrcode($wechatid, $params, $qrtype, $size)
    {
        $app = WechatModel::createApp($wechatid);
        if (!$app) {
            $this->error('小程序账号错误');
        }
        if ($qrtype == 'miniqr') {
            $response = $app->app_code->getQrCode($params['path'], $size);
        } else {
            $response = $app->app_code->get($params['path'], ['width' => $size]);
        }
        return $response->getBody()->getContents();
    }

    private function processData($data)
    {
        $skus = $data['skus'];
        if (empty($data['levels'])) $data['levels'] = [];
        $data['max_price'] = array_max($skus, 'price');
        $data['min_price'] = array_min($skus, 'price');
        $data['market_price'] = array_max($skus, 'market_price');
        $data['storage'] = array_sum(array_column($skus, 'storage'));
        if (empty($data['prop_data'])) {
            $data['prop_data'] = [];
        }
        if (!isset($data['spec_data'])) $data['spec_data'] =  [];
        if ($data['is_commission'] == 3) {
            $data['commission_percent'] = $data['commission_amount'];
        } elseif ($data['is_commission'] == 4) {
            $data['commission_percent'] = $data['commission_levels'];
        }
        unset($data['commission_amount']);
        unset($data['commission_levels']);
        if ($data['is_commission'] < 2) {
            unset($data['commission_percent']);
        }
        if (!empty($data['province']) && strpos($data['province'], '全部') !== false) {
            $data['province'] = '';
        }
        if (!empty($data['city']) && strpos($data['city'], '全部') !== false) {
            $data['city'] = '';
        }
        if (!empty($data['county']) && strpos($data['county'], '全部') !== false) {
            $data['county'] = '';
        }
        return $data;
    }

    private function checkArea($data)
    {
        // $topcate=ProductCategoryFacade::getTopCategory($data['cate_id']);
        // if(!empty($topcate) && $topcate['id']==15 || $data['cate_id']==15){
        //     if(empty($data['province'])){
        //         $this->error('请选择商品发布省份');
        //     }
        // }
    }

    /**
     * 添加
     * @param int $cid
     * @return mixed
     */
    public function add($cid = 0)
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $this->checkArea($data);
            $validate = new ProductValidate();
            $validate->setId();
            $skuValidate = new ProductSkuValidate();
            $skuValidate->setId(0);
            $validate->rule([
                'skus' => function ($value) use ($skuValidate) {
                    if (!is_array($value) || count($value) < 1) {
                        return '请填写商品规格信息';
                    }
                    foreach ($value as $sku) {
                        if (!$skuValidate->check($sku)) {
                            return $skuValidate->getError();
                        }
                    }
                    return true;
                }
            ]);
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $delete_images = [];
                $uploaded = $this->_upload('product', 'upload_image');
                if (!empty($uploaded)) {
                    $data['image'] = $uploaded['url'];
                    if (!empty($data['delete_image'])) $delete_images[] = $data['delete_image'];
                } elseif ($this->uploadErrorCode > 102) {
                    $this->error($this->uploadErrorCode . ':' . $this->uploadError);
                }
                if (isset($data['delete_image'])) unset($data['delete_image']);
                $data['user_id'] = $this->mid;
                $data = $this->processData($data);
                $skus = $data['skus'];
                unset($data['skus']);
                $model = ProductModel::create($data);
                if ($model['id']) {
                    delete_image($delete_images);
                    foreach ($skus as $sku) {
                        $sku['product_id'] = $model['id'];
                        ProductSkuModel::create($sku);
                    }
                    user_log($this->mid, 'addproduct', 1, '添加商品 ' . $model->id, 'manager');
                    $this->success(lang('Add success!'), url('shop.product/index'));
                } else {
                    delete_image($data['image']);
                    $this->error(lang('Add failed!'));
                }
            }
        }

        $presets = getProductPresets();

        $model = $presets['0'];
        $model['sale'] = 0;
        $model['status'] = 1;
        $model['cate_id'] = $cid;
        $model['spec_data'] = [];
        $model['brand_id'] = 0;

        if ($cid > 0 && isset($presets[$cid])) {
            $model = array_merge($model, $presets[$cid]);
        }

        $levels = getMemberLevels();
        $this->assign("category", ProductCategoryFacade::getCategories());
        $this->assign("brands", ProductCategoryFacade::getBrands(0));
        $this->assign('product', $model);
        $this->assign('presets', $presets);
        $this->assign('skus', []);
        $this->assign('levels', $levels);
        $this->assign('needarea', true);
        $this->assign('types', getProductTypes());
        $this->assign('postages', PostageModel::getCacheData());
        $this->assign('id', 0);
        return $this->fetch('edit');
    }

    /**
     * 修改
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function edit($id)
    {
        $id = intval($id);

        if ($this->request->isPost()) {
            $data = $this->request->post();
            $this->checkArea($data);
            $validate = new ProductValidate();
            $validate->setId($id);

            $oldSkus = Db::name('productSku')->where('product_id', $id)->select();

            // 匹配已存在的sku_id和goods_no
            if (!empty($data['skus']) && !empty($oldSkus)) {
                $oldNoIds = array_column($oldSkus, 'sku_id', 'goods_no');
                foreach ($data['skus'] as &$sku) {
                    if (empty($sku['sku_id']) && isset($oldNoIds[$sku['goods_no']])) {
                        $sku['sku_id'] = $oldNoIds[$sku['goods_no']];
                    }
                }
                unset($sku);
            }

            $skuValidate = new ProductSkuValidate();
            $validate->rule([
                'skus' => function ($value) use ($skuValidate) {
                    if (!is_array($value) || count($value) < 1) {
                        return '请填写商品规格信息';
                    }
                    foreach ($value as $sku) {
                        $skuValidate->setId($sku['sku_id']);
                        if (!$skuValidate->check($sku)) {
                            return $skuValidate->getError();
                        }
                    }
                    return true;
                }
            ]);
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $delete_images = [];
                $uploaded = $this->_upload('product', 'upload_image');
                if (!empty($uploaded)) {
                    $data['image'] = $uploaded['url'];
                    if (!empty($data['delete_image'])) $delete_images[] = $data['delete_image'];
                } elseif ($this->uploadErrorCode > 102) {
                    $this->error($this->uploadErrorCode . ':' . $this->uploadError);
                }
                $model = ProductModel::get($id);
                $skus = $data['skus'];

                $data = $this->processData($data);
                try {
                    $model->allowField(true)->save($data);
                    //不删除图片，可能会导致订单数据图片不显示
                    //delete_image($delete_images);
                    $existsIds = [];
                    foreach ($skus as $sku) {
                        if (!isset($sku['specs'])) $sku['specs'] = new stdClass;
                        if (!empty($sku['sku_id'])) {
                            ProductSkuModel::update($sku);
                            $existsIds[] = $sku['sku_id'];
                        } else {
                            $sku['product_id'] = $id;
                            $skuModel = ProductSkuModel::create($sku);
                            $existsIds[] = $skuModel['sku_id'];
                        }
                    }
                    Db::name('productSku')->where('product_id', $id)->whereNotIn('sku_id', $existsIds)->delete();
                    user_log($this->mid, 'updateproduct', 1, '修改商品 ' . $id, 'manager');
                } catch (\Exception $err) {
                    delete_image($data['image']);
                    $this->error(lang('Update failed: %', [$err->getMessage()]));
                }
                $this->success(lang('Update success!'), url('shop.product/index'));
            }
        }

        $model = ProductModel::get($id);
        if (empty($model)) {
            $this->error('商品不存在');
        }
        $skuModel = new ProductSkuModel();
        $skus = $skuModel->where('product_id', $id)->select();
        $levels = getMemberLevels();
        $this->assign("category", ProductCategoryFacade::getCategories());
        $this->assign("brands", ProductCategoryFacade::getBrands(0));
        $this->assign('levels', $levels);
        $this->assign('price_levels', array_filter($levels, function ($item) {
            return $item['diy_price'] == 1;
        }));
        $this->assign('product', $model);
        $this->assign('presets', getProductPresets($model));
        $this->assign('skus', $skus->isEmpty() ? [[]] : $skus);
        $this->assign('types', getProductTypes());
        $this->assign('needarea', true);
        $this->assign('postages', PostageModel::getCacheData());
        $this->assign('id', $id);
        return $this->fetch();
    }

    public function set_cate($cate_id, $ids)
    {
        $model = Db::name('product');
        $result = $model->where('id', 'in', idArr($ids))->update(['cate_id' => $cate_id]);
        if ($result) {
            user_log($this->mid, 'set-product-cate', 1, '设置商品分类 ' . $ids . '=>' . $cate_id, 'manager');
            $this->success(lang('Update success!'), url('shop.product/index'));
        }
        $this->error(lang('Update failed!'));
    }

    public function set_area($areas, $ids)
    {
        $model = Db::name('product');
        if (count($areas) < 3) {
            $areas[] = '';
            $areas[] = '';
        }
        for ($i = 0; $i < count($areas); $i++) {
            if (!empty($areas) && strpos($areas[$i], '全部') !== false) {
                $areas[$i] = '';
            }
        }
        $result = $model->where('id', 'in', idArr($ids))->update(['province' => $areas[0], 'city' => $areas[1], 'county' => $areas[2]]);
        if ($result) {
            user_log($this->mid, 'setarea-product', 1, '设置商品区域 ' . $ids . '=>' . implode(',', $areas), 'manager');
            $this->success(lang('Update success!'), url('shop.product/index'));
        }
        $this->error(lang('Update failed!'));
    }

    /**
     * 获取规格
     * @param $key
     * @param $ids
     * @return \think\response\Json
     * @throws Exception
     */
    public function get_specs($key = '', $ids = '')
    {
        $model = SpecificationsModel::where(1);
        $limit = 10;
        if (!empty($key)) {
            $model->whereLike('title', "%$key%");
        }
        if (!empty($ids)) {
            $ids = idArr($ids);
            $model->whereIn('key', $ids);
            $limit = count($ids);
        }
        $lists = $model->order('ID ASC')->limit($limit)->select();
        return json(['data' => $lists, 'code' => 1]);
    }

    /**
     * 删除
     * @param $id
     * @throws Exception
     */
    public function delete($id)
    {
        $model = Db::name('product');
        $result = $model->where('id', 'in', idArr($id))->update(['delete_time' => time(), 'status' => 0]);
        if ($result) {
            user_log($this->mid, 'mark-delete-product', 1, '商品移到回收站 ' . $id, 'manager');
            $this->success(lang('Delete success!'), url('shop.product/index'));
        } else {
            $this->error(lang('Delete failed!'));
        }
    }

    public function trash($keyword = '', $cate_id = 0)
    {
        if ($this->request->isPost()) {
            return redirect(url('', ['cate_id' => $cate_id, 'key' => base64url_encode($keyword)]));
        }
        $keyword = empty($keyword) ? "" : base64url_decode($keyword);
        $model = Db::view('product', '*')
            ->view('productCategory', ['name' => 'category_name', 'title' => 'category_title'], 'product.cate_id=productCategory.id', 'LEFT')
            ->where('product.delete_time', '>', 0);

        if (!empty($keyword)) {
            $model->whereLike('product.title|productCategory.title', "%$keyword%");
        }
        if ($cate_id > 0) {
            $model->whereIn('product.cate_id', ProductCategoryFacade::getSubCateIds($cate_id));
        }

        $lists = $model->order('create_time DESC')->paginate(10);
        if (!$lists->isEmpty()) {
            $ids = array_column($lists->items(), 'id');
            $skus = Db::name('productSku')->whereIn('product_id', $ids)->select();
            $skugroups = array_index($skus, 'product_id', true);
            $lists->each(function ($item) use ($skugroups) {
                if (isset($skugroups[$item['id']])) {
                    $item['skus'] = $skugroups[$item['id']];
                } else {
                    $item['skus'] = [];
                }
                return $item;
            });
        }

        $this->assign('lists', $lists);
        $this->assign('page', $lists->render());
        $this->assign('types', getProductTypes());
        $this->assign('keyword', $keyword);
        $this->assign('cate_id', $cate_id);
        $this->assign("category", ProductCategoryFacade::getCategories());
        $this->assign("brands", ProductCategoryFacade::getBrands(0));
        return $this->fetch();
    }

    public function trash_del($id)
    {
        $model = Db::name('product');
        $result = $model->where('delete_time', '>', 0)->where('id', 'in', idArr($id))->delete();
        if ($result) {
            Db::name('productSku')->where('product_id', 'in', idArr($id))->delete();
            Db::name('productImages')->where('product_id', 'in', idArr($id))->delete();
            Db::name('productComment')->where('product_id', 'in', idArr($id))->delete();
            user_log($this->mid, 'delete-product', 1, '删除商品 ' . $id, 'manager');
            $this->success(lang('Delete success!'), url('shop.product/trash'));
        } else {
            $this->error(lang('Delete failed!'));
        }
    }

    public function trash_restore($id)
    {
        $model = Db::name('product');
        $result = $model->where('id', 'in', idArr($id))->update(['delete_time' => 0]);
        if ($result) {
            user_log($this->mid, 'mark-restore-product', 1, '商品移出回收站 ' . $id, 'manager');
            $this->success(lang('Restore success!'), url('shop.product/trash'));
        } else {
            $this->error(lang('Restore failed!'));
        }
    }

    /**
     * 上下架
     * @param $id
     * @param int $status
     * @throws Exception
     */
    public function push($id, $status = 0)
    {
        $data['status'] = $status == 1 ? 1 : 0;

        $result = Db::name('product')->where('id', 'in', idArr($id))->update($data);
        if ($result && $data['status'] === 1) {
            user_log($this->mid, 'pushproduct', 1, '上架商品 ' . $id, 'manager');
            $this->success("上架成功", url('shop.product/index'));
        } elseif ($result && $data['status'] === 0) {
            user_log($this->mid, 'cancelproduct', 1, '下架商品 ' . $id, 'manager');
            $this->success("下架成功", url('shop.product/index'));
        } else {
            $this->error("操作失败");
        }
    }

    /**
     * 更新价格表
     */
    public function editsku($sku_id)
    {
        $sku = Db::name('ProductSku')->where('sku_id', $sku_id)->find();
        if (empty($sku)) {
            $this->error('价格表不存在');
        }
        $price = $this->request->param('price');
        $storage = $this->request->param('storage');

        Db::name('ProductSku')->where('sku_id', $sku_id)->update([
            'price' => $price,
            'storage' => $storage
        ]);
        $product = Db::name('Product')->where('id', $sku['product_id'])->find();
        if (!empty($product)) {
            $skutotal = Db::name('ProductSku')->where('product_id', $sku['product_id'])->field('min(price) as min_price,max(price) as max_price,sum(storage) as storage')->find();
            Db::name('Product')->where('id', $sku['product_id'])->update($skutotal);
        }
        $this->success("更新成功");
    }

    /**
     * 图集
     * @param $aid
     * @param $key
     * @return mixed
     * @throws Exception
     */
    public function imagelist($aid, $key = '')
    {
        $model = Db::name('ProductImages');
        $product = Db::name('Product')->find($aid);
        if (empty($product)) {
            $this->error('产品不存在');
        }
        $model->where('product_id', $aid);
        if (!empty($key)) {
            $model->whereLike('title', "%$key%");
        }
        $lists = $model->order('sort ASC,id DESC')->paginate(15);
        $this->assign('product', $product);
        $this->assign('lists', $lists);
        $this->assign('page', $lists->render());
        $this->assign('aid', $aid);
        return $this->fetch();
    }

    /**
     * 添加图片
     * @param $aid
     * @return mixed
     */
    public function imageadd($aid)
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new ImagesValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $uploaded = $this->_upload('product', 'upload_image');
                if (!empty($uploaded)) {
                    $data['image'] = $uploaded['url'];
                }
                $model = Db::name("ProductImages");
                $url = url('shop.product/imagelist', array('aid' => $aid));
                if ($model->insert($data)) {
                    $this->success(lang('Add success!'), $url);
                } else {
                    delete_image($data['image']);
                    $this->error(lang('Add failed!'));
                }
            }
        }
        $model = array('status' => 1, 'product_id' => $aid);
        $this->assign('model', $model);
        $this->assign('aid', $aid);
        $this->assign('id', 0);
        return $this->fetch('imageupdate');
    }

    /**
     * 修改图片
     * @param $id
     * @return mixed
     */
    public function imageupdate($id)
    {
        $id = intval($id);

        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new ImagesValidate();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            } else {
                $model = Db::name("ProductImages");
                $url = url('shop.product/imagelist', array('aid' => $data['product_id']));
                $delete_images = [];
                $uploaded = $this->_upload('product', 'upload_image');
                if (!empty($uploaded)) {
                    $data['image'] = $uploaded['url'];
                    if (!empty($data['delete_image'])) $delete_images[] = $data['delete_image'];
                }
                if (isset($data['delete_image']))  unset($data['delete_image']);
                $data['id'] = $id;
                if ($model->update($data)) {
                    delete_image($delete_images);
                    $this->success(lang('Update success!'), $url);
                } else {
                    delete_image($data['image']);
                    $this->error(lang('Update failed!'));
                }
            }
        } else {
            $model = Db::name('ProductImages')->where('id', $id)->find();
            if (empty($model)) {
                $this->error('图片不存在');
            }

            $this->assign('model', $model);
            $this->assign('aid', $model['product_id']);
            $this->assign('id', $id);
            return $this->fetch();
        }
    }

    /**
     * 删除图片
     * @param $aid
     * @param $id
     */
    public function imagedelete($aid, $id)
    {
        $id = intval($id);
        $model = Db::name('ProductImages');
        $result = $model->delete($id);
        if ($result) {
            $this->success(lang('Delete success!'), url('shop.product/imagelist', array('aid' => $aid)));
        } else {
            $this->error(lang('Delete failed!'));
        }
    }

    /**
     * 商品评论
     * @param int $id
     * @return \think\Response
     */
    public function comments($id = 0, $key = '')
    {
        $model = Db::view('productComment', '*')
            ->view('member', ['username', 'level_id', 'avatar'], 'member.id=productComment.member_id', 'LEFT')
            ->view('product', ['title' => 'product_title', 'cate_id', 'image'], 'product.id=productComment.product_id', 'LEFT')
            ->view('productCategory', ['name' => 'category_name', 'title' => 'category_title'], 'product.cate_id=productCategory.id', 'LEFT');
        $where = array();
        if ($id > 0) {
            $where[] = ['product_id', $id];
        }
        if (!empty($key)) {
            $where[] = ['product.title|productCategory.title', 'like', "%$key%"];
        }

        $lists = $model->where($where)->paginate(10);

        $this->assign('lists', $lists);
        $this->assign('page', $lists->render());
        $this->assign('keyword', $key);
        $this->assign('product_id', $id);
        $this->assign("category", ProductCategoryFacade::getCategories());

        return $this->fetch();
    }

    /**
     * 评论查看回复
     * @param $id
     * @return mixed
     */
    public function commentview($id)
    {
        $model = Db::name('productComment')->find($id);
        if (empty($model)) {
            $this->error('评论不存在');
        }
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['reply_time'] = $id;
            $data['reply_user_id'] = $this->mid;
            Db::name('productComment')->where('id', $id)->update($data);
            $this->success('回复成功');
        }
        $product = Db::name('product')->find($model['product_id']);
        $category = Db::name('productCategory')->find($product['cate_id']);
        $member = Db::name('member')->find($model['member_id']);

        $this->assign('model', $model);
        $this->assign('product', $product);
        $this->assign('category', $category);
        $this->assign('member', $member);
        return $this->fetch();
    }

    /**
     * 评论状态
     * @param $id
     * @param int $type
     */
    public function commentstatus($id, $type = 1)
    {
        $data['status'] = $type == 1 ? 1 : 2;

        $result = Db::name('productComment')->where('id', 'in', idArr($id))->update($data);
        if ($result && $data['status'] === 1) {
            user_log($this->mid, 'auditproductcomment', 1, '审核评论 ' . $id, 'manager');
            $this->success("审核成功", url('shop.product/comments'));
        } elseif ($result && $data['status'] === 2) {
            user_log($this->mid, 'hideproductcomment', 1, '隐藏评论 ' . $id, 'manager');
            $this->success("评论已隐藏", url('shop.product/comments'));
        } else {
            $this->error("操作失败");
        }
    }

    /**
     * 删除评论
     * @param $id
     */
    public function commentdelete($id)
    {
        $model = Db::name('productComment');
        $result = $model->where('id', 'in', idArr($id))->delete();
        if ($result) {
            user_log($this->mid, 'deleteproductcomment', 1, '删除评论 ' . $id, 'manager');
            $this->success(lang('Delete success!'), url('shop.product/comments'));
        } else {
            $this->error(lang('Delete failed!'));
        }
    }
}
