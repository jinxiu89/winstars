<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/7/8
 * Time: 18:18
 */

namespace app\wavlink\validate;


use think\Validate;

class DriverDownload extends Validate
{
    protected $rule = [
        "id" => 'require|number',
        "driver_id" => 'require|number',
        "requirement" => 'require|max:16',
        "size" => 'require|max:8',
        "type" => 'require|max:11',
        "url" => 'require|max:255',
    ];
    protected $message = [
        "id.require" => '非法操作',
        "id.number" => 'id不合法',
        "driver_id.require" => '不能脱离产品添加驱动',
        "driver_id.number" => 'driver_id非法',
        "requirement.require" => '系统支持必须选择一个，指你上传的文件的包是完整包还是分开的包',
        "requirement.max" => '系统支持字段不能超过16个字符，我相信你永远触发不到这个',
        "size.require" => '文件大小必须填写，用户友好度增加',
        "size.max" => '文件大小长度最大8个字符串',
        "type.require" => '类型必须填写',
        "type.max" => '类型长度不能超过11个字符',
        "url.require" => '下载地址需要填写',
        "url.max" => '最大字符长度为255',

    ];
    protected $scene = [
        'edit' => ['id', 'driver_id','requirement','size', 'type','url'],
        'add' => ['driver_id','requirement','size', 'type','url'],
    ];
}