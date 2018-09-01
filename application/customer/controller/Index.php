<?php
 namespace app\customer\controller;
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2018/3/13
 * Time: 16:28
 */
class Index extends Base
{

    public function index(){
        return $this->fetch();
    }
    public function add($id = ''){
        return $id;
    }

}