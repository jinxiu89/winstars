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

Class Document extends BaseModel
{
    protected $table = "tb_documents";

    public function products()
    {
        return $this->belongsToMany('Product', 'product_document', 'product_id', 'document_id')->field('id,model')->order('id asc');
    }

    public function downloads()
    {
        return $this->hasMany('DocumentDownload')->field('id,document_id,size,language,url');
    }

    /***
     * @param $url_title
     * @return array
     * 根据url_title来取数据，使用在后台的编辑功能上
     */
    public function getDataByUrlTitle($url_title)
    {
        try {
            $data = $this->get(['url_title' => $url_title]);
            return ['status' => true, "message" => "ok", "data" => $data];
        } catch (DbException $e) {
            return ['status' => false, "message" => $e->getMessage()];
        } catch (\Exception $exception) {
            return ['status' => false, "message" => $exception->getMessage()];
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

    /***
     * @param $data
     * @return array
     */
    public function sort($data)
    {
        $driver = self::allowField('sorting')->save($data, ['id' => $data['id']]);
        return $driver ? ['status' => 1, 'message' => '排序成功！'] : ['status' => 0, 'message' => '排序失败！'];
    }

    public function getDocument($category_id = '', $language_id)
    {
        $language_id = LanguageModel::getLanguageCodeOrID($language_id);
        if (empty($id)) {
            $data = [
                'status' => 1,
                'language_id' => $language_id
            ];
        } else {
            $data = [
                'status' => 1,
                'language_id' => $language_id,
                'category_id' => $category_id
            ];
        }
        $order = [
            'listorder' => 'desc',
            'id' => 'desc'
        ];
        return Search('Document', $data, $order);
    }

    public function getSelectDoc($name)
    {
        //多条件模糊查询
        $model = 'Document';
        $map['status'] = '1';
        $map['name|title|url_title|seo_title|keywords'] = array('like', '%' . $name . '%');
        $map['language_id'] = '2';
        $order = [
            'id' => 'desc',
        ];
        return Search($model, $map, $order);
    }

    //前台获取数据开始，调用5篇排序最高的文档，
    public function getDocumentList($code, $limit)
    {
        $language_id = LanguageModel::getLanguageCodeOrID($code);
        $map = [
            'status' => 1,
            'language_id' => $language_id,
        ];
        $order = [
            'sorting' => 'desc',
            'id' => 'desc'
        ];
        return $this->where($map)
            ->order($order)
            ->limit($limit)
            ->field('title,url_title')
            ->order('id desc')
            ->select();
    }

    public function getDataByLanguageId($language_id)
    {
        try {
            $result = [
                'count' =>self::where('language_id','=',$language_id)->where('status','=',1)->count(),
                'data' => self::where('language_id','=',$language_id)->where('status','=',1)
                    ->with('downloads')->with('products')
                    ->field('id,url_title,title,language_id,sorting,version,status,create_time')
                    ->order(['sorting' => 'desc', 'id' => 'asc'])->paginate()
            ];
            return ['status' => 1, 'message' => 'ok', 'data' => $result];
        } catch (Exception $exception) {
            return ['status' => 0, 'message' => $exception->getMessage(), 'data' => ''];
        }
    }

    /***
     * @param $data
     * @return array
     * @throws \think\exception\DbException
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
                $document = self::get(['url_title' => $data['url_title']]);
                if (empty($document)) {//数据没保存
                    return ['status' => 0, 'message' => '保存数据失败！'];
                }
                if (isset($products) || !empty($products)) {
                    if (!$document->products()->saveAll($products)) {//关系没保存
                        $document->delete();
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
     * @param $data
     * @return array
     *
     */
    public function saveDownload($data)
    {
        if (isset($data['id']) and !empty($data['id'])) {//更新方法
            try {
                if ((new DocumentDownload())->save($data, ['id' => $data['id']])) {
                    return ['status' => 1, 'message' => '保存成功'];
                }
                return ['status' => 0, 'message' => '保存失败！未知原因'];
            } catch (\Exception $exception) {
                return ['status' => 0, 'message' => $exception->getMessage()];
            }
        } else {//新增方法
            $document_id = $data['document_id'];
            unset($data['document_id']);
            try {
                $document = self::get($document_id);
                if (!$document) {
                    return ['status' => 0, 'message' => '你数据都不存在！怎么存关联数据？'];
                }
                if ($document->downloads()->save($data)) {
                    return ['status' => 1, 'message' => '新增成功'];
                }
                return ['status' => 0, 'message' => '新增失败！未知原因'];
            } catch (Exception $exception) {
                return ['status' => 0, 'message' => $exception->getMessage()];
            }
        }
    }

    public function getDocumentByCategory($category)
    {
        try {
            $category = (new CategoryModel())->getCategoryByUrlTitle($category);
            $result = self::where(['category_id' => $category['id']])->field('id,title,url_title,version,create_time')->cache(true)->paginate(22);
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
}