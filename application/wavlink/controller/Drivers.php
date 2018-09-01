<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2017/8/23
 * Time: 10:37
 * 下载中心
 */
namespace app\wavlink\controller;

use app\common\model\Drivers as DriversModel;
use app\common\model\ServiceCategory as ServiceCategoryModel;
use app\wavlink\validate\UrlTitleMustBeOnly;
use app\wavlink\validate\Drivers as DriversValidate;
use think\Request;
Class Drivers extends BaseAdmin
{
    public function index() {
        $language_id = $this->MustBePositiveInteger(input('get.language_id'));
        if (request()->isPost()) {
            $data = input('post.name');
            $res = DriversModel::getSelectDrivers($data, $language_id);
            return view('', [
                'drivers' => $res['data'],
                'counts' => $res['count'],
                'language_id' => $language_id
            ]);
        }
        $result = DriversModel::getDataByStatus(1, $language_id);
        return $this->fetch('', [
            'drivers' => $result['data'],
            'counts' => $result['count'],
            'language_id' => $language_id
        ]);
    }

    /**
     * 添加下载页面
     */
    public function add() {
        //获取语言
        $language_id = $this->MustBePositiveInteger(input('get.language_id'));
        //获取驱动的分类
        $category = ServiceCategoryModel::getSecondCategory($language_id);
        return $this->fetch('', [
            'category' => $category,
            'language_id' => $language_id,
        ]);
    }

    /**
     * 提交保存数据
     * @return array
     */
    public function save() {
        if (request()->isAjax()){
            (new DriversValidate())->goCheck();
            (new UrlTitleMustBeOnly())->goCheck();
            $data = input('post.');
            if(!empty($data['id'])){
                return $this->update($data);
            }
            $res = (new DriversModel())->add($data);
            if ($res) {
                return show(1,'','','','', '添加成功');
            } else {
                return show(1,'','','','', '添加失败');
            }
        }
    }

    /**
     * 编辑操作开发
     * @param int $id
     * @param $language_id
     * @return mixed
     */
    public function edit($id = 0,&$language_id) {
       $id = $this->MustBePositiveInteger($id);
       $language_id = $this->MustBePositiveInteger($language_id);
        //获取驱动的二级分类
        $category = ServiceCategoryModel::getSecondCategory($language_id);
        $drivers = DriversModel::get($id);
        return $this->fetch('', [
            'language_id' => $language_id,
            'drivers' => $drivers,
            'category' => $category,

        ]);
    }

    /**
     * 批量回收
     * @param Request $request
     * @return array
     */
    public function allRecycle(Request $request) {
        $ids = $request::instance()->post();
        if (!is_array($ids)) {
            return show(0, 'error','','','', '数据错误');
        }
        try {
            foreach ($ids as $k => $v) {
                if (DriversModel::get($k)) {
                    model('Drivers')->where('id', $k)->update(['status' => -1]);
//                    model('Drivers')->where('id', $k)->update(['listorder' => $k+50]);
                } else {
                    return show(0, 'error','','','', '回收失败');
                }
            }
            return show(1, 'success','','','','批量回收成功');
        } catch (\Exception $e) {
            return show(0, 'error', '','','',$e->getMessage());
        }
    }

    /**
     * 回收下载列表开发
     */
    public function recycle() {
        $result = DriversModel::getDataByStatus(-1);
        return $this->fetch('', [
            'drivers' => $result['data'],
            'counts' => $result['count'],
        ]);
    }
    /**
     * 排序功能开发
     * 默认 必须数据 id,type,language_id
     **type == 1 时 置底
     * type == 4 时 置顶
     * type == 3 时 上移
     * type == 2 时 下移
     */
    public function listorder() {
        if (request()->isAjax()) {
            $data = input('post.'); //id ,type ,language_id
            self::order(array_filter($data));
        } else {
            return show(0, '置顶失败，未知错误', 'error', 'error', '', '');
        }
    }
//    /**
//     * 回收站列表彻底删除
//     * @param Request $request
//     * @internal param int $id
//     * @return array
//     */
//    public function del(Request $request)
//    {
//        $id = $request::instance()->param();
//        if (!is_array($id)) {
//            return show(0, 'error', '数据错误');
//        }
//        $res = \app\common\model\Drivers::destroy($id);
//        if ($res) {
//            return show(1, 'success', '删除成功');
//        } else {
//            return show(0, 'error', '删除失败');
//        }
//    }

//    /**
//     * 回收站批量彻底删除
//     * @param Request $request
//     * @return array
//     */
//    public function alldel(Request $request)
//    {
//        $ids = $request::instance()->post();
//        if (!is_array($ids)) {
//            return show(0, 'error', '删除失败');
//        }
//        try {
//            foreach ($ids as $k => $v) {
//                if (\app\common\model\Drivers::get($k)) {
//                    \app\common\model\Drivers::destroy($k);
//                } else {
//                    return show(0, 'error', '删除失败');
//                }
//            }
//            return show(1, 'success', '删除成功');
//        } catch (\Exception $e) {
//            return show(0, 'error', $e->getMessage());
//        }
//    }
}