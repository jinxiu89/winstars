<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/7/8
 * Time: 18:18
 */

namespace app\wavlink\validate;


use think\Validate;

class Driver extends Validate
{
    protected $rule = [
        "id" => 'require|number',
        "name" => 'require|max:64|token',
        "language_id" => 'require|number',
        "url_title" => 'require|max:64',
        "version" => 'require|max:11',
        "title" => 'require|max:64',
        "keywords" => 'require|max:64',
        "description" => 'require|max:120',
    ];
    protected $message = [
        "id.require" => 'id必须填',
        "id.number" => 'id不合法',
        "name.require" => '驱动名称必须填',
        "name.max" => '最大字符长度不能超过64个字符',
        "name.token" => '请勿重复提交',
        "language_id.require" => '必须有language_id',
        "language_id.number" => 'language_id不能为非数字',
        "url_title.require" => 'url标题必须填',
        "url_title.max" => 'url标题最大长度不能超过64个字符',
        "version.require" => '版本号必须填',
        "version.max" => '版本号长度必须在11个字符以内',
        "title.require" => 'SEO标题不能为空',
        "title.max" => 'SEO标题不能长于64个字符',
        "keywords.require" => '关键词必须填',
        "keywords.max" => '关键词必须少于64个字符',
        "description.require" => '描述必须填',
        "description.max" => '描述最长不能超过120个字符',
    ];
    protected $scene = [
        'edit' => ['id', 'name', 'language_id', 'url_title', 'version', 'title', 'keywords', 'description'],
        'add' => ['name', 'language_id', 'url_title', 'version', 'title', 'keywords', 'description'],
    ];
}