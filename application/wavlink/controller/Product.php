<?php

namespace app\wavlink\controller;

/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2017/8/23
 * Time: 10:37
 */

use app\common\model\Product as ProductModel;
use app\common\model\Category as CategoryModel;
use app\wavlink\validate\ListorderValidate;
use app\wavlink\validate\UrlTitleMustBeOnly;
use think\Request;
use app\wavlink\validate\Product as ProductValidate;

Class Product extends BaseAdmin
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->language_id=$this->current_language['id'];
    }

    //产品列表，status=1
    public function index()
    {
        $product = ProductModel::getDataByStatus(1, $this->language_id);
        $category = (new CategoryModel())->getAllCategory($this->language_id);
        $category_id = input('get.category_id');
        $name = input('get.name');
        if (!empty($category_id) || !empty($name)) {
            $data = input('get.');
            $result = (new ProductModel())->getSelectProduct($data['name'], $data['category_id'], $this->language_id);
            $this->assign('product', $result['data']);
            $this->assign('counts', $result['count']);
            $this->assign('name', $data['name']);
            $this->assign('category_id', $data['category_id']);
        } else {
            $this->assign('product', $product['data']);
            $this->assign('counts', $product['count']);
            $this->assign('name', '');
            $this->assign('category_id', '');
        }
        return $this->fetch('', [
            'category' => $category,
            'language_id' => $this->language_id,
        ]);
    }

    //回收站的产品的列表,status=-1
    public function product_recycle()
    {
        $result = ProductModel::getDataByStatus(-1);
        return $this->fetch('', [
            'product' => $result['data'],
            'counts' => $result['count'],
        ]);
    }

    public function add()
    {

        $categorys = (new CategoryModel())->getChildsCategory($this->language_id);

        return $this->fetch('', [
            'language_id' => $this->language_id,
            'categorys' => $categorys,
        ]);
    }

    public function save()
    {
        //严格判断校验
        if (request()->isAjax()) {
            $data = input('post.');
            $features_html=strip_html_tags(['script','iframe'],htmlspecialchars($data['features-html-code']),true);
            $content_html=strip_html_tags(['script','iframe'],htmlspecialchars($data['content-html-code']),true);
            $data['features_html_code']=$features_html;
            $data['content_html_code']=$content_html;
            unset($data['features-html-code']);
            unset($data['content-html-code']);
            $validate = new ProductValidate();
            if (!$validate->scene("add")->check($data)) {
                return show(0, '', '', '', '', $validate->getError());
            }
            $res = (new ProductModel())->productSave($data);
            if ($res) {
                return show(1, '', '', '', '', '添加成功');
            } else {
                return show(1, '', '', '', '', '添加失败');
            }
        }
    }

    public function product_edit($id = 0)
    {
        $id = $this->MustBePositiveInteger($id);
        $product = ProductModel::get($id);
        //获取语言
        //获取分类
        $categorys = (new CategoryModel())->getChildsCategory($this->language_id);
        $cateID = ProductModel::getProductCategory($id);
        return $this->fetch('', [
            'categorys' => $categorys,
            'language_id' => $this->language_id,
            'product' => $product,
            'cateID' => $cateID,
        ]);
    }

//    /**
//     * 排序功能开发
//     * $data = [];
//     * id,type,language_id 是必须的
//     * map是可有可无的，额外条件查询
//     */
//    public function listorder() {
//        if (request()->isAjax()) {
//            $data = input('post.'); //id ,type ,language_id,map
//            if (empty($data['map'])) {
//                self::order(array_filter($data));
//            }
//            // 对在同一个分类下的排序。总分类和子分类
//            $str = (new CategoryModel())->getChildsIDByID($data['map'],$data['language_id']);
//            $map = [
//                'category_id' => ['in',$str],
//            ];
//            unset($data['map']);
//            self::order($data, $map);
//        } else {
//            return show(0, '置顶失败，未知错误', 'error', 'error', '', '');
//        }
//    }

    //批量放回回收站
    public function allRecycle(Request $request)
    {
        $ids = $request::instance()->post();
        foreach ($ids as $k => $v) {
            if (ProductModel::get($k)) {
                //批量更新数据。
                (new ProductModel())->where('id', $k)->update(['status' => -1]);
                //批量更新排序
//                $this->obj->where('id', $k)->update(['listorder' => $k + 100]);
            } else {
                return show(0, '', '', '', '', '回收失败');
            }
        }
        return show(1, '', '', '', '', '批量回收成功');
    }

    public function sort()
    {
        if (request()->isAjax()) {
            $data = input('post.');
            (new ListorderValidate())->goCheck();
            $res = (new ProductModel())
                ->allowField(true)
                ->isUpdate(true)
                ->save(
                    ['listorder' => $data['listorder']],
                    ['id' => $data['id']]
                );
            if ($res) {
                return show(1, '', '', '', $_SERVER['HTTP_REFERER'], '操作成功');
            } else {
                return show(0, '', '', '', $_SERVER['HTTP_REFERER'], '操作失败');
            }
        }
    }

    public function mark($id)
    {
        $product = (new ProductModel())->get($id);
        $product->mark = 1;
        if ($product->save()) {
            return show(1, '', '', '', $_SERVER['HTTP_REFERER'], '操作成功');
        } else {
            return show(0, '', '', '', $_SERVER['HTTP_REFERER'], '操作失败');
        }
    }
}