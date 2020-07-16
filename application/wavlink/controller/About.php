<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2017/8/23
 * Time: 10:37
 */

namespace app\wavlink\controller;

use app\common\model\About as AboutModel;
use app\wavlink\validate\About as AboutValidate;
use think\Request;

Class About extends BaseAdmin
{
    /***
     * @return mixed
     * 20190808 修改了语言模块的 运行机制 改为$this->current_language来处理，存储的是一个当前语言的数组，包含了ID code name等信息 要用自取
     */
    public function index()
    {
        $order = [
            'status' => 'desc',
            'listorder' => 'desc'
        ];
        $about = AboutModel::getDataByOrder(['language_id' => $this->current_language['id']], $order);
        return $this->fetch('', [
            'about' => $about['data'],
            'count' => $about['count'],
        ]);
    }

    public function add()
    {
        return $this->fetch('', [
            'language_id' => $this->current_language['id']
        ]);
    }

    public function save(Request $request)
    {
        if (request()->isAjax()) {
            $data = $request::instance()->post();
            $validate = new AboutValidate();
            if ($validate->check($data)) {
                if (!empty($data['id'])) {
                    return $this->update($data);
                }
                $res = (new AboutModel())->add($data);
                if ($res) {
                    return show(1, '', '', '', '', '添加成功');
                } else {
                    return show(1, '', '', '', '', '添加失败');
                }
            } else {
                return show(0, '', '', '', '', $validate->getError());
            }
        }
    }

    public function edit($id)
    {
        $id = $this->MustBePositiveInteger($id);
        $about = AboutModel::get($id);
        return $this->fetch('', [
            'about' => $about,
            'language_id' => $this->current_language['id'],
        ]);
    }
}