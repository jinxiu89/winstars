<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2017/9/18
 * Time: 16:14
 * 推荐位管理
 */

namespace app\wavlink\controller;

use app\common\model\AdSpace as model;
use think\Exception;
use think\Request;
use app\wavlink\validate\AdSpace as Validate;

class AdSpace extends BaseAdmin
{
    protected $model;
    protected $validate;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->model = new model();
        $this->validate = new Validate();
    }

    public function index()
    {
        if(\request()->isGet()){
            try{
                $data=$this->model->all();
                $count=count($data);
                $this->assign('data',$data);
                $this->assign('count',$count);
            }catch (Exception $exception){
                $this->error($exception->getMessage());
            }
        }
        return $this->fetch('');
    }

    public function select()
    {
        return view();
    }

    public function add()
    {
        return $this->fetch();
    }

    public function save()
    {
        if (request()->isAjax()) {
            $data = input('post.');

            if (isset($data['id']) and !empty($data['id'])) {
                if (!empty($data['id'])) {
                    return $this->update($data);
                }
            } else {
                //否则视为新增
                if ($this->validate->scene('add')->check($data)) {
                    $res = $this->model->add($data);
                    if ($res) {
                        return show(1, '', '', '', '', '添加成功');
                    } else {
                        return show(1, '', '', '', '', '添加失败');
                    }
                } else {
                    return show(0, '', '', '', '', $this->validate->getError());
                }
            }
        }
    }

    public function edit($id)
    {
        if (intval($id) < 0) {
            return show(0, 'error', 'ID非法');
        }
        try {
            $adSpace = $this->model->get($id);
        } catch (Exception $exception) {
            return show(0, 'error', $exception->getMessage());
        }
        return $this->fetch('', [
            'result' => $adSpace,
        ]);
    }
//    //单个删除
//    public function del(Request $request){
//        $id = $request::instance()->param();
//        if (empty($id)){
//            return show(0,'','数据不存在');
//        }
//        $res = \app\common\model\Featured::destroy($id);
//        if ($res){
//            return show(1,'','删除成功');
//        }else{
//            return show(0,'','删除失败');
//        }
//    }
//    //批量删除
//    public function delAll(Request $request){
//        $ids = $request::instance()->post();
//        if (!is_array($ids)){
//            return show(0,'','数据异常');
//        }
//        try{
//            foreach ($ids as $k => $v){
//                if (\app\common\model\Featured::get($k)){
//                    \app\common\model\Featured::destroy($k);
//                }else{
//                    return show(0,'','删除错误');
//                }
//            }
//            return show(1,'','批量删除成功');
//        }catch (\Exception $e){
//            return show(0,'',$e->getMessage());
//        }
//    }
}
