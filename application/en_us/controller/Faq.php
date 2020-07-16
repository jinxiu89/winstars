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
use app\common\model\ServiceCategory;
use app\common\model\Language as LanguageModel;
use app\common\helper\Category as CategoryHelp;
use app\common\model\AdSpace;
use think\Request;

class Faq extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $result = (new AdSpace())->getDataByCode('About');
        if ($result['status'] == true) {
            $this->assign('banner', $result['data']);
        }
    }

    public function _initialize()
    {
        parent::_initialize();
        $data = collection((new CategoryModel())->getDataByCode($this->code))->toArray();
        $tree = CategoryHelp::toLayer($data, $name = 'child', $parent_id = 0);
        $this->language = (new LanguageModel())->getLanguageCodeOrID($this->code);
        $this->assign('tree', $tree);
    }

    /**
     * @return mixed
     */
    public function index()
    {
        //获取一级faq分类
//        $parent = ServiceCategory::getTopCategory($this->code, 'faq');//这条是指faq这个服务分类的一些信息，如ID 图片等，后续要用可以开发出来使用
        //获取所有的faq列表
        $faq = (new FaqModel())->getFaqByCategoryID('', $this->code);
        return $this->fetch($this->template . '/faq/index.html',['data'=>$faq['data'],'count'=>$faq['count']]);
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