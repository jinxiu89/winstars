<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/9/8
 * Time: 13:25
 */

namespace app\en_us\validate;



class Index extends Base
{
    protected $rule =[
        ['name','require|max:64','your name is require|max length not '],
        ['email','require|email','Email is require|Email format error'],

    ];
}