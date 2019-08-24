<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2018/4/20
 * Time: 16:39
 */

namespace app\en_us\controller;

use app\common\model\Faq as FaqModel;
use app\common\model\Category as CategoryModel;
use app\common\model\Language as LanguageModel;
use app\common\helper\Category as CategoryHelp;

class Faq extends Base
{
//    protected $beforeActionList = [
//        'cate' => ['only', 'index,category,details']
//    ];

    public function _initialize()
    {
        parent::_initialize();
        $data = collection((new CategoryModel())->getDataByCode($this->code))->toArray();
        $tree = CategoryHelp::toLayer($data, $name = 'child', $parent_id = 0);
        $this->language = (new LanguageModel())->getLanguageCodeOrID($this->code);
        $this->assign('tree', $tree);
    }

    public function index()
    {
        $result = (new FaqModel())->getDataAll($this->language);
        $this->assign('data', $result);
        return $this->fetch($this->template . '/faq/index.html');
        //获取一级faq分类
        $parent = ServiceCategoryModel::getTopCategory($this->code, 'faq');
        //获取所有的faq列表
        $faq = (new FaqModel())->getFaqByCategoryID('', $this->code);
        return $this->fetch('', [
            'parent' => $parent,
            'faq' => $faq['data']
        ]);
    }

    public function category($category = '')
    {
        if (empty($category) || !isset($category)) {
            abort(404);
        }
        $categoryModel = new CategoryModel();
        $cate = $categoryModel->getAllCategory($this->code);
        $language_id = LanguageModel::getLanguageCodeOrID($this->code);
        $category_data = (new CategoryModel())->getCategoryByName($category, $language_id);
        $path = collection(CategoryHelp::getParents($cate, $category_data['id']))->toArray();
        $result = (new FaqModel())->getFaqByCategory($category);
        $this->assign("category", $category_data);
        $this->assign('path', $path);
        $this->assign('data', $result);
        return $this->fetch($this->template . '/faq/category.html');
    }

    public function details($url_title = '')
    {
        if(request()->isGet()) {
            if (empty($url_title) or !isset($url_title)) {
                abort(404);
            }
            $result = (new FaqModel())->getDetailByUrlTitle($url_title);
            if (!empty($result)) {
                $this->assign('result', $result);
                return $this->fetch($this->template . '/faq/details.html');
            } else {
                abort(404);
            }
        }
    }
}