<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2018/4/20
 * Time: 16:39
 */
namespace app\en_us\controller;

use app\common\model\Faq as FaqModel;
use app\common\model\ServiceCategory as ServiceCategoryModel;

class Faq extends Base
{
    /***
     * 前置方法：在Base里面定义了保护方法cate
     * 作用：获取服务分类的二级分类
     * @var array
     */
    protected $beforeActionList = [
        'cate' => ['only', 'index,category,details']
    ];



    public function index() {
        //获取一级faq分类
        $parent = ServiceCategoryModel::getTopCategory($this->code, 'faq');
        //获取所有的faq列表
        $faq = (new FaqModel())->getFaqByCategoryID('', $this->code);
        return $this->fetch('', [
            'parent' => $parent,
            'faq' => $faq['data']
        ]);
    }

    public function category($url_title = '') {
        if (empty($url_title) || !isset($url_title)) {
            abort(404);
        }
        //获取选择的faq子分类信息
        $parent = ServiceCategoryModel::getCategoryIdByName($this->code, $url_title);
        if (empty($parent)) {
            abort(404);
        } else {
            $faq = (new FaqModel())->getFaqByCategoryID($parent['id'], $this->code);
            return view('index', [
                'faq' => $faq['data'],
                'parent' => $parent,
            ]);
        }
    }

    public function details($url_title = '') {
        if (empty($url_title) || !isset($url_title)) {
            abort(404);
        }
        //该问题详情页
        $result = FaqModel::getDetailsByUrlTitle($url_title, $this->code);
        //该问题的分类
        $faqCate = ServiceCategoryModel::get(['id' => $result['category_id']]);
        if (!empty($result)) {
            return $this->fetch('', [
                'faqName'  => cut_str($result['name'],15),
                'result' => $result,
                'faqCate' => $faqCate
            ]);
        } else {
            abort(404);
        }
    }
}