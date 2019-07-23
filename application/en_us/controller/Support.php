<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/7/10
 * Time: 16:06
 */

namespace app\en_us\controller;


class Support extends Base
{

    public function index(){
        return $this->fetch($this->template.'/support/index.html');
    }
}