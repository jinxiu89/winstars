<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2017/9/8
 * Time: 15:00
 */

namespace app\common\model;

use app\common\model\Language as LanguageModel;
use app\common\helper\Category as CategoryHelp;
use app\common\model\Product as ProductModel;
use think\Cache;
use think\Exception;
use think\exception\DbException;

Class Category extends BaseModel
{
    protected $hidden = ['create_time', 'update_time'];
    protected $table = 'category';//使用category表
    protected $model = 'category';

    //与产品多对多关联
    public function products()
    {
        return $this->belongsToMany('Product', 'product_category', 'product_id', 'category_id')->field('id,name,model');
    }

    //获取中间表数据,得到分类下的产品id
    //把foreach的数据也缓存起来
    public static function getProductCategory($id)
    {
        $product = self::get($id);
        $cates = $product->products;
        $ids = [];
        foreach ($cates as $v) {
            $ids[] = $v['id'];
        }
        return $ids;
    }

    public function getCategoryByUrlTitle($category)
    {
        //通过分类ID 拿到产品的IDS的集合
        try {
            return self::where(['url_title' => $category])->field('id,url_title,name')->find();
        } catch (Exception $exception) {
            return [];
        }
    }

    /***
     * @param $category
     * @param $language_id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 更新缓存操作
     */
    public function getCategoryByName($category, $language_id)
    {
        $map = array(
            "url_title" => $category,
            "language_id" => $language_id
        );
        if (!$this->debug) {
            if (!Cache::get('CategoryByName' . $category)) {
                Cache::set('CategoryByName' . $category, $this->where($map)->find()->toArray());
            }
        }

        return $this->debug ? $this->where($map)->find()->toArray() : Cache::get('CategoryByName' . $category);
    }

    /***
     * @param $code
     * @return false|mixed|\PDOStatement|string|\think\Collection
     * @throws DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     *
     */
    public function getDataByCode($code)
    {
        $language_id = LanguageModel::getLanguageCodeOrID($code);
        $map = ['language_id' => $language_id, 'status' => 1];
        if (!$this->debug) {
            if (!Cache::get('CategoryByCode')) {
                Cache::set('CategoryByCode', $this->where($map)->order(['listorder' => 'desc', 'id' => 'desc'])->select());
            }
        }
        return $this->debug ? $this->where($map)->order(['listorder' => 'desc', 'id' => 'desc'])->select() : Cache::get('CategoryByCode');
    }

    public static function getCategoryWithProduct($id)
    {
        $ids = static::getProductCategory($id);
        $order = ['listorder' => 'desc', 'id' => 'desc'];
        $feild = 'id,name,url_title,model,keywords,album,thumbnail';
        return (new ProductModel())->cache(true)->where('id', 'in', $ids)->where(['status' => 1])->order($order)->field($feild)->paginate(12);
    }

    //获取分类id 或者 子分类id
    public function getChildsIDByID($id, $code)
    {
        //传入一个分类id,还有语言id
        //判断该分类是否有子分类
        //如果没有 就返回这个id
        //如果有就 查出它的所有子类的id
        $language_id = LanguageModel::getLanguageCodeOrID($code);
        $cate = $this->getAllCategory($language_id);
        $HasChild = CategoryHelp::hasChild($cate, $id);
        if ($HasChild) {
            $ChildId = CategoryHelp::getChildsId($cate, $id);
            $str = implode(',', $ChildId);
        } else {
            $str = $id;
        }
        return $str;
    }


    //后台获取栏目列表
    public function getCategory($parentId = 0, $language_id)
    {
        $data = [
            'parent_id' => $parentId,
            'language_id' => $language_id
        ];
        $order = [
            'status' => 'desc',
            'listorder' => 'desc',
            'id' => 'asc',
        ];
        $model = 'Category';

        return Search($model, $data, $order);
    }

    //根据产品的 category_id，语言 获取分类数据
    public function getCategoryById($code, $id)
    {
        $language_id = LanguageModel::getLanguageCodeOrID($code);
        $data = [
            'status' => 1,
            'id' => $id,
            'language_id' => $language_id
        ];
        $category = $this->where($data)->find();
        return $category;
    }


    //根据parent_id 获取分类数据

    /***
     * @param $code
     * @param int $parent_id
     * @return false|mixed|\PDOStatement|string|\think\Collection
     * @throws DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * 升级缓存
     */
    public function getNormalCategory($code, $parent_id = 0)
    {
        $language_id = LanguageModel::getLanguageCodeOrID($code);
        $map = ['status' => 1, 'language_id' => $language_id, 'parent_id' => $parent_id,];
        $order = ['listorder' => 'desc', 'id' => 'desc'];
        $field = 'name,url_title,id,image';
        if (!$this->debug) {
            if (!Cache::get('NormalCategory')) {
                Cache::set('NormalCategory', $this->where($map)->order($order)->field($field)->select());
            }
        }
        return $this->debug ? $this->where($map)->order($order)->field($field)->select() : Cache::get('NormalCategory');
    }

    //获取所有的分类，并且递归

    /***
     * @param $value
     * @return array
     * @throws DbException
     * 把查库的操作缓存起来防止慢查询
     *
     */
    public function getAllCategory($value)
    {
        $language_id = LanguageModel::getLanguageCodeOrID($value);
        $data_language = [
            'status' => 1,
            'language_id' => $language_id
        ];
        $data = [
            'status' => 1
        ];
        if (empty($language_id)) {
            if (!$this->debug) {
                if (!Cache::get('AllCategory')) {
                    Cache::set('AllCategory', Category::all($data));
                }
                $category = $this->debug ? Cache::get() : Category::all($data);
            }
        } else {
            if (!$this->debug) {
                if (!Cache::get('AllCategory')) {
                    Cache::set('AllCategory', Category::all($data_language));
                }
            }
            $category = $this->debug ? Category::all($data_language) : Cache::get('AllCategory');
        }
        $categorys = CategoryHelp::toLevel($category, '--', 0);
        return $categorys;
    }

    //导航 直接循环出二级分类,对于二级分类
    public function getChildsCategory($code)
    {
        $language_id = LanguageModel::getLanguageCodeOrID($code);
        $map = [
            'status' => 1,
            'language_id' => $language_id
        ];
        $cate = self::all($map);
        $data = CategoryHelp::toLayer($cate, 'child');
        return $data;
    }

    /***
     * @param $language_id
     * @return array
     * @throws \think\exception\DbException
     *
     */
    public function getCategoryLevel($language_id)
    {
        $language_id = LanguageModel::getLanguageCodeOrID($language_id);
        $map = [
            'status' => 1,
            'language_id' => $language_id
        ];
        $cate = self::all($map);
        return CategoryHelp::toLevel($cate, '--', 0);
    }

    public function getDataByCategoryId($category_id)
    {
        try {
            return collection(self::with('products')->field('id,name')->where(['id' => $category_id])->select())->toArray();
        } catch (DbException $e) {
            $this->error($e->getMessage());
        }
    }

    public function sort($data)
    {

        $category = self::allowField('listorder')->save($data, ['id' => $data['id']]);
        return $category ? ['status' => 1, 'message' => '排序成功！'] : ['status' => 0, 'message' => '排序失败！'];
    }
}