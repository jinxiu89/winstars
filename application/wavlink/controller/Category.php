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
            $validate = new CategoryValidate();
            if (isset($data['id']) or !empty($data['id'])) {
                if ($validate->scene('edit')->check($data)) {
                    if ($data['id'] == $data['parent_id']) {
                        return show(0, '', '不能编辑在自己名下');
                    } else {
                        return $this->update($data);
                    }
                } else {
                    return show(0, '', '', '', '', $validate->getError());
                }
            } else {
                if ($validate->scene('add')->check($data)) {
                    $res = (new CategoryModel())->add($data);
                    if ($res) {
                        return show(1, '', '', '', '', '添加成功');
                    } else {
                        return show(1, '', '', '', '', '添加失败，未知原因');
                    }
                } else {
                    return show(0, '', '', '', '', $validate->getError());
                }
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

    public function sort()
    {
        if (\request()->isAjax()) {
            $data = input('post.');
            $validate = new CategoryValidate();
            if ($validate->scene('sort')->check($data)) {
                $result = (new CategoryModel())->sort($data);
                if ($result) {
                    return show($result['status'], $result['message'], '', '', '', $result['message']);
                } else {
                    return show($result['status'], '未知原因的排序失败', '', '', '', '未知原因的排序失败');
                }
            } else {
                return show(false, 'failed', '', '', '', $validate->getError());
            }
        }
    }

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