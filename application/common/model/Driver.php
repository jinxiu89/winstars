<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/7/8
 * Time: 13:32
 */

namespace app\common\model;

use app\common\model\DriverDownload;
use app\common\model\Category as CategoryModel;
use think\Cache;
use think\Exception;

class Driver extends BaseModel
{
    protected $table = 'tb_driver';

    public function products()
    {
        return $this->belongsToMany('Product', 'product_driver', 'product_id', 'driver_id')->field('id,model')->order('id asc');
    }

    public function downloads()
    {
        return $this->hasMany('DriverDownload')->field('id,driver_id,requirement,size,type,url');
    }

    public function getDataByLanguageId($language_id)
    {
        try {
            $query = self::with('downloads')->where(['language_id' => $language_id]);
            $result = [
                'count' => $query->count(),
                'data' => $query->paginate()
            ];
            return ['status' => 1, 'message' => 'ok', 'data' => $result];
        } catch (Exception $exception) {
            return ['status' => 0, 'message' => $exception->getMessage(), 'data' => ''];
        }
    }

    public function getDataByUrlTitle($url_title)
    {
        return $this->get(['url_title' => $url_title]);
    }

    /***
     * @param $products
     * @return array
     *
     */
    public function getProductList($products)
    {
        foreach ($products as $item) {
            $product_ids[] = $item['id'];
        }
        return $product_ids;
    }

    /***
     * @param $data
     * @return array|false|true
     * @throws \think\exception\DbException
     * 20190709
     * 保存驱动数据，和产品相关
     * 更新：当该驱动没有选中产品的时候 不需要存储中间表数据，表示当前没有可选产品
     *
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
            } catch (Exception $exception) {
                self::rollback();
                return ['status' => 0, 'message' => $exception->getMessage()];
            }
        } else {//新增数据
            try {
                self::allowField(true)->save($data);
                $driver = self::get(['url_title' => $data['url_title']]);
                if (empty($driver)) {//数据没保存
                    return ['status' => 0, 'message' => '保存数据失败！'];
                }
                if (isset($products) || !empty($products)) {
                    if (!$driver->products()->saveAll($products)) {//关系没保存
                        $driver->delete();
                        return ['status' => 0, 'message' => '保存关系失败！'];
                    }
                }
                return ['status' => 1, 'message' => '新增成功'];
            } catch (\Exception $exception) {
                self::rollback();
                return ['status' => 0, 'message' => $exception->getMessage()];
            }
        }
    }

    /***
     * @param $data
     * @return array
     * 20190710
     *
     */
    public function saveDownload($data)
    {
        if (isset($data['id']) and !empty($data['id'])) {//更新方法
            try {
                if ((new DriverDownload())->save($data, ['id' => $data['id']])) {
                    return ['status' => 1, 'message' => '保存成功'];
                }
                return ['status' => 0, 'message' => '保存失败！未知原因'];
            } catch (Exception $exception) {
                return ['status' => 0, 'message' => $exception->getMessage()];
            }
        } else {//新增方法
            $driver_id = $data['driver_id'];
            unset($data['driver_id']);
            try {
                $driver = self::get($driver_id);
                if (!$driver) {
                    return ['status' => 0, 'message' => '你数据都不存在！怎么存关联数据？'];
                }
                if ($driver->downloads()->save($data)) {
                    return ['status' => 1, 'message' => '新增成功'];
                }
                return ['status' => 0, 'message' => '新增失败！未知原因'];
            } catch (Exception $exception) {
                return ['status' => 0, 'message' => $exception->getMessage()];
            }
        }
    }

    public function getDriverByCategory($category)
    {
        try {
            $category = (new CategoryModel())->getCategoryByUrlTitle($category);
            $result = self::where(['category_id' => $category['id']])->field('id,name,url_title,version,create_time')->cache(true)->paginate(22);
            return $result;
        } catch (Exception $exception) {
            return [];
        }
    }

    public function getDetailByUrlTitle($url_title)
    {
        try {
            return collection(self::with(['products' => 'products', 'downloads' => 'downloads'])->where(['url_title' => $url_title])->select())->toArray();
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /***
     * @return string|\think\Paginator
     * 有分页 不需要
     */
    public function getDataAll()
    {
        try {
            return self::field('id,name,url_title,version,create_time')->cache(true)->paginate(22);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}