<?php


namespace app\en_us\controller;


use think\Controller;

class Error extends Controller
{
    public function index($code)
    {
        if ($code == 404){
            //todo:404的模板
        }
        if ($code == 403){
            //todo::403 不允许的连接
        }
        if ($code == 500){
            //todo::500 服务器内部错误
        }
        if ($code == 400){
            //todo::...
        }
    }
}