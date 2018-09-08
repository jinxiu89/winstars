<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/9/7
 * Time: 15:34
 */

namespace app\wavlink\controller;

use \app\common\model\Partner as PartnerModel;
use \app\wavlink\validate\Partner as PartnerValidate;

class Partner extends BaseAdmin
{
    public function index()
    {
        $data = (new PartnerModel())->getDateByStatus();
        return $this->fetch('', ['result' => $data['data']]);
    }

    /**
     * @throws \Exception
     * @return mixed|void
     *
     */
    public function add()
    {
        if (request()->isAjax()) {
            $data = input('post.');
            $data['status']=1;
            $validate = new PartnerValidate;
            if (!$validate->scene('add')->check($data)) {
                return show(0, '', '', '', '', $validate->getError());
            } else {
                if ((new PartnerModel())->saveData($data)) {
                    return show(1, '', '', '', '', '添加成功');
                } else {
                    return show(0, '', '', '', '', '添加失败');
                }
            }
        }
        return $this->fetch();
    }

    public function edit($id='')
    {
        $id = $this->MustBePositiveInteger($id);
        $map=['id'=>$id];
        $model=new PartnerModel();
        $data=$model->where($map)->find();
        if(request()->isAjax()){
            $data=input('post.');
            $data['status']=1;
            $validate=new PartnerValidate();
            if(!$validate->scene('edit')->check($data)){
                return show(0,'','','','',$validate->getError());
            }else{
                if((new PartnerModel())->saveData($data)){
                    return show(1,'','','','','编辑成功');
                }else{
                    return show(0,'','','','','编辑失败');
                }
            }
        }
        return $this->fetch('',['result'=>$data]);
    }
}