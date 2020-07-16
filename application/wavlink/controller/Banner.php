<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2017/8/23
 * Time: 10:46
 */

namespace app\wavlink\controller;

use app\common\model\Banner as model;
use app\common\model\AdSpace;
use app\wavlink\validate\Banner as Validate;
use think\Exception;
use think\Request;

Class Banner extends BaseAdmin
{
    protected $model;
    protected $validate;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->language_id = $this->current_language['id'];
        $this->model = new model();
        $this->validate = new Validate();

    }

    //幻灯片图片
    public function index()
    {
        if (\request()->isGet()) {
            try {
                $result = $this->model->getDataByLanguage($this->language_id);
                $this->assign('data', $result['data']);
                $this->assign('count', $result['count']);
            } catch (Exception $exception) {
                $this->error($exception->getMessage());
            }
        }
        return $this->fetch();
    }

    //回收站图片列表,status=-1
    public function images_recycle()
    {
        $result = ImagesModel::getDataByStatus(-1);
        return $this->fetch('', [
            'image' => $result['data'],
            'counts' => $result['count'],
        ]);
    }
    public function byStatus()
    {
        $data = input('get.');

        $validate = new Validate();
        if (!$validate->scene('changeStatus')->check($data)) {
            return show(0, 'error', '', '', '', $validate->getError());

        }
        //获取控制器
        $res = $this->model->save($data, ['id' => $data['id']]);
        if ($res) {
            return show(1, "success", '', '', '', '操作成功');
        } else {
            return show(0, 'error', '', '', '', '操作失败');
        }
    }

    public function add()
    {
        //获取推荐位
        $adSpace = AdSpace::all(['status' => 1]);
        return $this->fetch('', [
            'adSpace' => $adSpace,
        ]);
    }

    public function save()
    {
        if (request()->isAjax()) {
            $data = input('post.');
            $data['language_id'] = $this->language_id;
            if (isset($data['id']) and !empty($data['id'])) {
                if ($this->validate->scene('edit')->check($data)) {
                    return $this->update($data);
                } else {
                    return show(0, '', '', '', '', $this->validate->getError());
                }
            } else {
                if ($this->validate->scene('add')->check($data)) {
                    $res = $this->model->add($data);
                    if ($res) {
                        return show(1, '', '', '', '', '添加成功');
                    } else {
                        return show(0, '', '', '', '', '添加失败');
                    }
                } else {
                    return show(0, '', '', '', '', $this->validate->getError());
                }
            }
        }
    }

    public function edit($id = 0)
    {
        try {
            //获取正常的推荐位分类
            $adSpace = AdSpace::all(['status' => 1]);
            //获取语言
            $data = $this->model->get($id);

        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
        return $this->fetch('', [
            'result' => $data,
            'adSpace' => $adSpace,
        ]);
    }

    //批量修改
    public function allChange(Request $request)
    {
        try {
            $ids = $request::instance()->post();
            foreach ($ids as $k => $v) {
                if (ImagesModel::get($k)) {
                    //批量更新数据。
//                    (new CategoryModel())->where('id', $k)->update(['status' => -1]);
                    //批量更新排序
                    (new ImagesModel())->where('id', $k)->update(['listorder' => $k + 100]);
                } else {
                    return show(0, '', '回收失败');
                }
            }
            return show(1, '', '批量回收成功');
        } catch (\Exception $e) {
            return show(0, $e->getMessage(), '', '', '');
        }
    }

    //排序操作
    public function listorder()
    {
        if (request()->isAjax()) {
            $data = input('post.'); //id ,type ,language_id
            $map = [
                'featured_id' => $data['map'],
            ];
            unset($data['map']);
            self::order($data, $map);
        } else {
            return show(0, '置顶失败，未知错误', 'error', 'error', '', '');
        }
    }
}