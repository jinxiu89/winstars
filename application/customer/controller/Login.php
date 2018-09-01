<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2018/3/14
 * Time: 11:18
 */
namespace app\customer\controller;
use think\Controller;
use think\Session;
use think\Lang;
class Login extends Controller {

    public function index(){
        $lang = getLang();
        Lang::load(APP_PATH .'customer/lang/' . $lang . '.php'); //加载该语言下的模块语言包
        return $this->fetch();
    }
    public function login(){
        //假设登录成功了
        //todo::登录逻辑
        //设置session
        Session::set('username','1','Customer');
        $this->redirect('customer/Index/index');
    }
}