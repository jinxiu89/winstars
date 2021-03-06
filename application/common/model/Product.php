<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2017/9/8
 * Time: 15:00
 */

namespace app\common\model;

use app\common\helper\Algorithm as AlgorithmHelp;
use app\common\model\Category as CategoryModel;
use app\common\model\Drivers as DriversModel;
use app\common\model\Language as LanguageModel;
use app\common\helper\Search as SearchHelp;
use think\Collection;
use think\Cookie;
use think\Cache;
use think\Db;
use think\Exception;

/***
 * Class Product
 * @package app\common\model
 */
Class Product extends BaseModel
{
    protected $table = 'product';

    //en_us模块下Category控制器使用到这个方法。获取该父分类下的所有产品。
    public function categorys()
    {
        return $this->belongsToMany('Category');
    }

    public function drivers()
    {
        return $this->belongsToMany('Driver', 'product_driver', 'driver_id', 'product_id')->field('id,name,url_title,version,create_time');
    }

    public function documents()
    {
        return $this->belongsToMany('Document', 'product_document', 'document_id', 'product_id')->field('id,title,url_title,version,create_time');
    }

    public function faqs()
    {
        return $this->belongsToMany('Faq', 'product_faq', 'faq_id', 'product_id')->field('id,title,url_title,create_time');
    }

    /**
     * 根据状态值，，默认排序
     * 语言查找 语言可以为空
     * 可以设置默认值 status = 1
     * $result['data']
     * $result['count']
     * @param $status
     * @param string $language_id
     * @return array|string
     * @internal param int $page
     * @internal param array $map
     * @internal param $status
     */
    public static function getDataByStatus($status, $language_id = '')
    {
        if (empty($language_id)) {
            $map = [
                'status' => $status
            ];
            $order = [
                'language_id' => 'desc',
                'id' => 'desc'
            ];
        } else {
            $map = [
                'status' => $status,
                'language_id' => $language_id
            ];
            $order = [
                'listorder' => 'desc',
                'id' => 'desc'
            ];
        }
        $model = request()->controller();
        if (Cookie::has('systemPage')) {
            $page = Cookie::get('systemPage');
        } else {
            $system = config('system.system');
            $page = $system['page'];
        }
        $result = SearchHelp::search($model, $map, $order, $page);
        foreach ($result['data'] as $v) {
            $thumbnail = substr($v['album'], 0, strpos($v['album'], ','));
            if (!$thumbnail) {
                $v['thumbnail'] = '';
            } else {
                $v['thumbnail'] = substr($v['album'], 0, strpos($v['album'], ','));
            }
        }
        return $result;
    }

    public function getBestSales($code)
    {
        $language_id = LanguageModel::getLanguageCodeOrID($code);
        $result = $this->where(["mark" => 1, 'language_id' => $language_id])->field('url_title,model,keywords,name,album')->find();
        return $result;
    }

    /***
     * 通过category来获取属于该分类的所有产品
     * @param $category
     *
     * @param $code
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getDataByCategory($category, $code)
    {
        $language_id = LanguageModel::getLanguageCodeOrID($code);
        $category_id = (new CategoryModel())->getCategoryByName($category, $language_id);
        $result = $this->where(["category_id" => $category_id])->paginate();
        return $result;

    }

    public function getDataByCategoryId($category_id)
    {
        try {
            return collection($this->where(['category_id' => $category_id])->select())->toArray();
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    /***
     * @param $id
     * 通过产品的ID来获取他所属的第一个分类
     *
     */
    public function getCategoryById($id)
    {
        try {
            $product = self::with('categorys')->field('id,url_title')->find(['id' => $id]);
            $category = collection($product->categorys)->toArray();
            return end($category)['id'];
        } catch (Exception $exception) {
            return ['status' => 0, 'message' => $exception->getMessage(), 'data' => ''];
        }
    }

    /***
     * @param $url_title
     * @return array
     * 说明：with方法预加载解决N+1性能问题
     * []里的预加载可以是一位数组传进去，可以多级关联，用“.”连接起来，例如：drivers.downloads
     *
     */
    public function getDetailByUrlTitle($url_title)
    {
        try {
            $result = $this->with(['drivers.downloads', 'documents.downloads', 'faqs'])->where(['url_title' => $url_title])->find();
            return ['status' => 1, 'message' => 'ok', 'data' => $result];
        } catch (Exception $exception) {
            return ['status' => 0, 'message' => $exception->getMessage(), 'data' => ''];
        }
    }

    //获取中间表数据,得到产品所属分类id
    public static function getProductCategory($id)
    {
        $product = self::get($id);
        $cates = $product->categorys;
        $ids = [];
        foreach ($cates as $v) {
            $ids[] = $v['id'];
        }
        return $ids;
    }

    public static function getCategoryWithProduct($id)
    {
        $category = self::get($id);

        $cates = $category->categorys;
        return $cates;
    }

    public function productSave($data)
    {
        if (!empty($data['id'])) {
            $product = self::get($data['id']);
            $this->allowField(true)->save($data, ['id' => $data['id']]);
            $roles = $product->categorys;
            $cates = [];
            foreach ($roles as $v) {
                $cates[] = $v['id'];
            }
            $product->categorys()->detach($cates);
            $res = $product->categorys()->attach($data['cates']);
        } else {
            $data['status'] = 1;
            $this->allowField(true)->save($data);
            //获取自增id
            $id = $this->id;
            $product = self::get($id);
            $this->allowField('listorder')
                ->isUpdate(true)
                ->save(
                    ['listorder' => $id + 100],
                    ['id' => $id]
                );
            $res = $product->categorys()->save($data['cates']);
        }
        return ($res !== true) ? true : false;
    }

    //后台搜索多条件模糊查询
    public function getSelectProduct($name, $category_id, $code)
    {
        $language_id = LanguageModel::getLanguageCodeOrID($code);
        //多条件模糊查询
        $map['status'] = 1;
        $map['name|model|url_title|seo_title|keywords'] = array('like', '%' . $name . '%');
        $map['language_id'] = $language_id;
        $order = [
            'listorder' => 'desc',
            'id' => 'desc'
        ];
        if (!empty($category_id)) {
            $cateID = CategoryModel::getProductCategory($category_id);
            $map['id'] = ['in', $cateID];
        }
        $result = Search('Product', $map, $order);
        foreach ($result['data'] as $v) {
            $thumbnail = substr($v['album'], 0, strpos($v['album'], ','));
            if (!$thumbnail) {
                $v['thumbnail'] = '';
            } else {
                $v['thumbnail'] = substr($v['album'], 0, strpos($v['album'], ','));
            }
        }
        return $result;
    }

    //前端搜索当前模块下的语言的数据，en_us/zh_cn模块下search控制器用到这个方法
    public function frontendGetSelectProduct($key, $code)
    {
        $language_id = LanguageModel::getLanguageCodeOrID($code);
        $model = 'product';
        $map['status'] = 1;
        $map['name|model|url_title|seo_title|keywords'] = array('like', '%' . $key . '%');
        $map['language_id'] = $language_id;
        $order = [
            'listorder' => 'desc',
        ];
        return Search($model, $map, $order);
    }

    //Ajax搜索 下拉联想列表
    public function opSelectProduct($key, $code, $limit)
    {
        $language_id = LanguageModel::getLanguageCodeOrID($code);
        $map['status'] = 1;
        $map['name|model'] = array('like', '%' . $key . '%');
        $map['language_id'] = $language_id;
        $order = [
            'listorder' => 'desc'
        ];
        return $this->where($map)
            ->order($order)
            ->limit($limit)
            ->field('name,model')
            ->select();
    }


    /**
     * @param $code
     * @param $list
     * @return false|\PDOStatement|string|\think\Collection /推荐的产品。取出排名最高的3个产品
     * 取出排序最高的产品
     * 数量为 list
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 更新缓存
     * 20190716
     *
     */
    public static function getListProduct($code, $list)
    {
        $language_id = LanguageModel::getLanguageCodeOrID($code);
        $map = [
            'status' => 1,
            'language_id' => $language_id
        ];
        $order = [
            'listorder' => 'desc'
        ];
        $field = 'url_title,name,model';
        try {
            if (!config('app_debug')) {
                if (!Cache::get('LimitListProduct')) {
                    Cache::set('LimitListProduct', self::where($map)->limit($list)->field($field)->order($order)->select());
                }
            }
            $products = config('app_debug') ? self::where($map)->limit($list)->field($field)->order($order)->select() : Cache::get('LimitListProduct');
            $data = collection($products)->toArray();
            return $data;
        } catch (Exception $exception) {

        }
    }

    /**
     * 查询产品的相关驱动地址
     * @param $result
     * $result 单独的一个产品的信息
     * @param $code
     * @return bool|mixed
     */
    public function selectProDriver($result, $code)
    {
        //设置缓存
        if (Cache::get($result['id'] . $result['model'], '')) {
            $pDirver = Cache::get($result['id'] . $result['model']);
        } else {
            $fDirver = DriversModel::getSelectDrivers($result['model'], $code);
            Cache::set($result['id'] . $result['model'], $fDirver['data'], 7200);
            $pDirver = Cache::get($result['id'] . $result['model']);
        }
        if (!empty($pDirver)) {
            return $pDirver;
        } else {
            return false;
        }
    }

    //利用二分法和缓存去查找产品
    public function binarySearchProduct($url_title, $code)
    {
        if (Cache::get('allProductByCode' . $code, '')) {
            //如果有缓存，就直接取
            $allProduct = Cache::get('allProductByCode' . $code);
        } else {
            $language_id = LanguageModel::getLanguageCodeOrID($code);
            $product = $this::all([
                'language_id' => $language_id,
                'status' => 1
            ]);
            $productArr = collection($product)->toArray();
            //对所有的产品数组进行排序
            $sortProduct = AlgorithmHelp::array_sort($productArr, 'url_title');
            //设置缓存
            Cache::set('allProductByCode' . $code, $sortProduct, 7200);
            //获取缓存的值
            $allProduct = Cache::get('allProductByCode' . $code);
        }
        //todo::还需优化的地方，把传入的$url_title换成id
        $mid = AlgorithmHelp::binarySearch($allProduct, $url_title, 'url_title');
        if ($mid === false) {
            return false;
        } else {
            return $allProduct[$mid];
        }

    }

    public function getHotSales($code, $count)
    {
        $language_id = LanguageModel::getLanguageCodeOrID($code);
        return collection(self::where(['language_id' => $language_id])->limit($count)->with('categorys')->field('id,name,url_title,keywords,model,description,album,thumbnail')->select())->toArray();
    }
}