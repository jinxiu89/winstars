<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2017/8/23
 * Time: 10:37
 */

namespace app\wavlink\controller;

use think\Request;
use app\common\model\Category as CategoryModel;
use app\wavlink\validate\Category as CategoryValidate;

Class Category extends BaseAdmin
{

    public function index()
    {
        $parentId = input('get.parent_id', '0', 'intval');
        $language_id = input('get.language_id', '', 'intval');
        $result = (new CategoryModel())->getCategory($parentId, $language_id);
        return $this->fetch('', [
            'category' => $result['data'],
            'counts' => $result['count'],
            'language_id' => $language_id
        ]);
    }

    public function add()
    {
        //获取语言
        $language_id = $this->MustBePositiveInteger(input('get.language_id'));
        if (input('get.parent_id')) {
            //如有存在parent_id ,就是添加子分类
            $category_id = input('get.parent_id');
            $cate = CategoryModel::all();
            $level = \app\common\helper\Category::countParent($cate, $category_id);
            if ($level > 2) {
                return "分类级别不能大于等于三级";
            }
            $this->assign('parent_id', $category_id);
        } else {
            //添加一级分类
            $this->assign('parent_id', 0);
        }
        return $this->fetch('', [
            'language_id' => $language_id,
        ]);
    }

    public function save()
    {
        if (request()->isAjax()) {
            $data = input('post.');
            $validate=new CategoryValidate();
            if(!$validate->check($data)) {
                return show(0, '', '', '', '', $validate->getError());
            }
            if (!empty($data['id'])) {
                if ($data['id'] == $data['parent_id']) {
                    return show(0, '', '不能编辑在自己名下');
                } else {
                    return $this->update($data);
                }
            }
            $res = (new CategoryModel())->add($data);
            if ($res) {
                return show(1, '', '', '', '', '添加成功');
            } else {
                return show(1, '', '', '', '', '添加失败');
            }
        }
    }

    /**
     * 编辑分类页面
     * @param int $id
     * @param $language_id
     * @return array|mixed
     * @throws \think\exception\DbException
     */
    public function edit($id, $language_id)
    {
        $id = $this->MustBePositiveInteger($id);
        $language_id = $this->MustBePositiveInteger($language_id);
        $category = CategoryModel::get($id);
        if ($category['parent_id'] > 0) {
            $cate = CategoryModel::all([
                'status' => 1,
                'language_id' => $language_id
            ]);
            $this->assign('cate', $cate);
        } else {
            $this->assign('parent_id', 0);
        }
        return $this->fetch('', [
            'category' => $category,
            'language_id' => $language_id
        ]);
    }

    /**
     * 排序功能开发
     */
    public function listorder()
    {
        if (request()->isAjax()) {
            $data = input('post.'); //id ,type ,language_id，map
            //得到它的parent_id
            $map = [
                'parent_id' => $data['map']
            ];
            self::order($data, $map);
        } else {
            return show(0, '置顶失败，未知错误', 'error', 'error', '', '');
        }
    }
//    public function del(Request $request)
//    {
//        $id = $request->param('id');
//        if (empty($id)) {
//            return show(0, 'error', 'id非法错误');
//        }
//        //从Category找是否存在子分类
//        $result = $this->obj->findChild($id);
//        //查询分类下是否有产品
//        $product = model("Product")->findProduct($id);
//        //如果查找分类下面有子分类或者产品，不为空删除失败、
//        if (!empty($result) || !empty($product)) {
//            return show(0, '', '删除失败！删除的分类有子分类或者其分类下有产品！');
//        } else {
//            try {
//                $res = CategoryModel::destroy($id);
//                if ($res) {
//                    return show(1, '', '删除成功');
//                } else {
//                    return show(0, '', '删除失败');
//                }
//            } catch (\Exception $e) {
//                return show(0, '', $e->getMessage());
//            }
//        }
//    }
//批量放回回收站
    public function allRecycle(Request $request)
    {
        try {
            $ids = $request::instance()->post();
            foreach ($ids as $k => $v) {
                if (CategoryModel::get($k)) {
                    //批量更新数据。
                    (new CategoryModel())->where('id', $k)->update(['status' => -1]);
                    //批量更新排序
//                    (new CategoryModel())->where('id', $k)->update(['listorder' => $k + 100]);
                } else {
                    return show(0, '', '回收失败');
                }
            }
            return show(1, '', '批量回收成功');
        } catch (\Exception $e) {
            return show(0, $e->getMessage(), '', '', '');
        }

    }
}