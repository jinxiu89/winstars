<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2017/9/8
 * Time: 15:00
 */
namespace app\common\model;

use app\common\model\Language as LanguageModel;
Class ServiceCategory extends BaseModel
{
    protected $table = 'service_category';

    /**
     * 获取状态status=1的数据,并按照id倒序的方式排序出来在index下
     * 根据parent_id得到
     * 获取一级栏目
     * @param int $parentId
     * @param $language_id
     * @return \think\Paginator
     */
    public function getServiceCategory($parentId = 0, $language_id = '') {
        $model = 'ServiceCategory';
        $data = [
            'parent_id' => $parentId,
            'language_id' => $language_id
        ];
        $order = [
            'status' => 'desc',
            'listorder'=>'desc',
            'id' => 'desc',
        ];
        return Search($model, $data, $order);
    }

    /**
     * @param $value
     * @param $url_title
     * @return array|false|\PDOStatement|string|\think\Model\
     * 根据分类的url_title,语言来获取分类
     */
    public static function getCategoryIdByName($value, $url_title) {
        $language_id = LanguageModel::getLanguageCodeOrID($value);
        $data = [
            'status' => 1,
            'url_title' => $url_title,
            'language_id' => $language_id,
        ];
        $result = self::where($data)->find();
        return $result;
    }

    /**
     * @param $value
     * @param $name
     * @return array|false|\PDOStatement|string|\think\Model \获取服务管理 服务分类
     * \获取服务管理 服务分类 一级分类
     * @internal param int $parentId
     * @internal param $code
     * 传过来的$value 可以是语言code 也可以是语言id，最终都会以语言id去查询
     *  $name 是一级分类的 url_title
     */
    public static function getTopCategory($value, $name) {
        $language_id = LanguageModel::getLanguageCodeOrID($value);
        $data = [
            'status'      => 1,
            'language_id' => $language_id,
            'url_title'   => $name,
            'parent_id'   => 0
        ];
        $result = self::where($data)->find();
        return $result;
    }

    //获取对应栏目的二级分类

    /***
     * 如果$name 一级分类变量为空，则调用这个方法的控制器名
     * if (empty($name)){
     * $cate = request()->controller();
     * value 为当前的语言code，如en_us 是当前语言code ,
     * 而我们的模块名是按照code来起名的，所以在初始化_initialize时获取当前模块名
     * @param $value
     * 一级分类名，单指 url_title 字段的值
     * @param string $name
     * @return false|\PDOStatement|string|\think\Collection
     * 获取到某个分类下面的二级分类
     *
     * self:: 指类本身
     */
    public static function getSecondCategory($value,$name='') {
        if (empty($name)){
            $cate = request()->controller();
        }else{
            $cate =$name;
        }
        $parent = self::getTopCategory($value,$cate);
        $data = [
            'status' => 1,
            'parent_id' => $parent['id'],
        ];
        $order = [
            'listorder' => 'desc',
            'id' => 'desc'
        ];
        return self::where($data)
            ->order($order)
            ->field('id,name,image,description,keywords,language_id,url_title,seo_title')
            ->select();
    }
    //
}