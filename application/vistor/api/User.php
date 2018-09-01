<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2017/12/9
 * Time: 14:48
 */
namespace app\vistor\api;
use app\vistor\common\model\VistorModel;
use think\Controller;
Class User extends Controller{
    /**
     * 构造方法 实例化模型
     */
    protected $model;
    public function _initialize(){
        $this->model = new VistorModel();
    }

    /**
     * 用户登录认证
     * @param $data
     * @return string
     */
    public function login($data){
        $res = new VistorModel();
        return $res->login($data);
    }
}