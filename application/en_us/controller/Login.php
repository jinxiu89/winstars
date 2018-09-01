<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2017/12/9
 * Time: 14:42
 */
namespace app\en_us\controller;
use think\Controller;
use think\Session;
class Login extends Controller
{

    public function index(){
//        $module = request()->module();
//        Session::clear('Customer');
//        Session::set('langModel',$module,'Customer');
//        $this->redirect(url('customer/Login/index'),'',302,['lang' => $module]);
        return view('base/404');
    }
}