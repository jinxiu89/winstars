<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/9/7
 * Time: 16:30
 */

namespace app\wavlink\validate;


class Partner extends BaseValidate
{
    protected $rule = [
        ['name','require','请输入合作者名称'],
        ['logo','require','必须输入，首页的合作者logo就是这个图片'],
        ['href','require','这是个连接地址，请输入']
    ];
    protected $scene = [
        'add'=>['name','logo','href'],
        'edit'=>['name','logo','href']
    ];
}