<?php
/**
 * Created by PhpStorm.
 * User: wavlink
 * Date: 2017/11/25
 * Time: 14:24
 */

namespace app\en_us\controller;

use app\common\model\Category as CategoryModel;
use app\common\helper\Category as CategoryHelp;
use app\common\model\Language as LanguageModel;
use app\common\model\Driver as DriverModel;
use think\Request;

class Driver extends Base
{
    public function _initialize()
    {
        parent::_initialize();
        $data = collection((new CategoryModel())->getDataByCode($this->code))->toArray();
        $tree = CategoryHelp::toLayer($data, $name = 'child', $parent_id = 0);
        $this->language=(new LanguageModel())->getLanguageCodeOrID($this->code);
        $this->assign('tree', $tree);
    }

    //驱动下载首页
    public function index()
    {
        $result=(new DriverModel())->getDataAll($this->language);
        $this->assign('data',$result);
        return $this->fetch($this->template . '/driver/index.html');
    }

    public function getProductList()
    {
        $category = input('post.');
        $result = (new CategoryModel())->getDataByCategoryId($category['category_id']);
        return toJSON(['status' => true, 'message' => 'ok', 'data' => $result[0]['products']]);
    }

    public function category($category)
    {
        $categoryModel = new CategoryModel();
        $cate = $categoryModel->getAllCategory($this->code);
        $language_id = LanguageModel::getLanguageCodeOrID($this->code);
        $category_data = (new CategoryModel())->getCategoryByName($category, $language_id);
        $path = collection(CategoryHelp::getParents($cate, $category_data['id']))->toArray();
        $result = (new DriverModel())->getDriverByCategory($category);
        $this->assign("category", $category_data);
        $this->assign('path', $path);
        $this->assign('data', $result);
        return $this->fetch($this->template . '/driver/category.html');
    }

    public function details($url_title)
    {
//        print_r(config('app_debug'));
        $result=(new DriverModel())->getDetailByUrlTitle($url_title);
        $this->assign('data',$result[0]);
        return $this->fetch($this->template . '/driver/details.html');
    }
}