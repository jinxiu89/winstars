<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2017/9/8
 * Time: 15:00
 */

namespace app\common\model;

use app\common\model\Language as LanguageModel;
use think\Exception;
use think\exception\DbException;
use app\common\model\Category as CategoryModel;

Class Faq extends BaseModel
{
    protected $table = 'tb_faq';

    public function products()
    {
        return $this->belongsToMany('Product', 'product_faq', 'product_id', 'faq_id')->field('id,model')->order('id asc');
    }

    public function getDataByUrlTitle($url_title)
    {
        try {
            return $this->get(['url_title' => $url_title]);
        } catch (DbException $e) {
            //todo::异常修正稍后处理
        }
    }

    /***
     * @param $products
     * @return array
     * 把产品的列表 组合成一堆一维数组；
     * 改函数在后台编辑文档时使用，用于处理文档与产品的多对多关联的数据
     */
    public function getProductList($products)
    {
        foreach ($products as $item) {
            $product_ids[] = $item['id'];
        }
        return $product_ids;
    }

    public function getFaqByCategoryID($id = '', $code)
    {
        $language_id = LanguageModel::getLanguageCodeOrID($code);
        if (empty($id)) {
            $data = [
                'status' => 1,
                'language_id' => $language_id
            ];
        } else {
            $data = [
                'status' => 1,
                'language_id' => $language_id,
            ];
        }
        $order = [
            'sorting' => 'desc',
            'id' => 'desc'
        ];
        return Search('faq', $data, $order);
    }

    public function getSelectFaq($code, $key)
    {
        $language_id = LanguageModel::getLanguageCodeOrID($code);
        $model = 'Faq';
        $map['status'] = 1;
        $map['name|url_title|problem|answer|relevantproblem'] = array('like', '%' . $key . '%');
        $map['language_id'] = $language_id;
        $order = [
            'id' => 'desc',
        ];
        return Search($model, $map, $order);
    }

    public function getDataByLanguageId($language_id)
    {
        try {
            $query = self::with('products')->where(['language_id' => $language_id]);
            try {
                $result = [
                    'count' => $query->count(),
                    'data' => $query->paginate()
                ];
            } catch (DbException $e) {
            }
            return ['status' => 1, 'message' => 'ok', 'data' => $result];
        } catch (Exception $exception) {
            return ['status' => 0, 'message' => $exception->getMessage(), 'data' => ''];
        }
    }

    /***
     * @param $data
     * @return array|false|true
     * @throws DbException
     * @throws \think\exception\PDOException
     */
    public function saveData($data)
    {
        if (isset($data['products']) || !empty($data['products'])) {
            $products = $data['products'];
            unset($data['products']);
        }
        if (isset($data['id']) and !empty($data['id'])) {
            //更新
            //1、查出原有的数据
            $tempData = self::get($data['id']);
            if (isset($tempData->products) and !empty($tempData->products)) {
                $ids = self::getProductList($tempData->products);
            }
            try {
                $tempData->allowField(true)->save($data);
                if (!empty($ids) || isset($ids)) {//能有就删除
                    $tempData->products()->detach($ids);
                }
                if (isset($products) || !empty($products)) {
                    if ($tempData->products()->saveAll($products)) {
                        return ['status' => 1, 'message' => '保存成功！'];
                    };
                    return ['status' => 0, 'message' => '保存失败，未知原因！'];
                }
                return ['status' => 1, 'message' => '保存成功！'];
            } catch (\Exception $exception) {
                self::rollback();
                return ['status' => 0, 'message' => $exception->getMessage()];
            }
        } else {//新增数据
            try {
                self::allowField(true)->save($data);
                $faq = self::get(['url_title' => $data['url_title']]);
                if (empty($faq)) {//数据没保存
                    return ['status' => 0, 'message' => '保存数据失败！'];
                }
                if (isset($products) || !empty($products)) {
                    if (!$faq->products()->saveAll($products)) {//关系没保存
                        $faq->delete();
                        return ['status' => 0, 'message' => '保存关系失败！'];
                    }
                }
                return ['status' => 1, 'message' => '新增成功'];
            } catch (DbException $exception) {
                self::rollback();
                return ['status' => 0, 'message' => $exception->getMessage()];
            } catch (\Exception $exception) {
                self::rollback();
                return ['status' => 0, 'message' => $exception->getMessage()];
            }
        }

    }

    /***
     * @param $language_id
     * @return string|\think\Paginator
     */
    public function getDataAll($language_id)
    {
        try {
            return self::field('id,title,url_title,category_id,create_time')->cache(true)->order(['sorting' => 'desc', 'id' => 'asc'])->where(['language_id' => $language_id])->paginate(22);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function getDetailByUrlTitle($url_title)
    {
        try {
            return self::get(['url_title' => $url_title]);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function getFaqByCategory($category){
        try {
            $category = (new CategoryModel())->getCategoryByUrlTitle($category);
            $result = self::where(['category_id' => $category['id']])->field('id,title,url_title,create_time')->cache(true)->paginate(22);
            return $result;
        } catch (Exception $exception) {
            return [];
        }
    }
}