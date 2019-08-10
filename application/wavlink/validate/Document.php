<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2017/8/28
 * Time: 10:00
 *服务管理之文档中心验证规则
 */

namespace app\wavlink\validate;

class Document extends BaseValidate
{
//    /**验证规则**/
//    protected $rule = [
//        ['id','number','id不合法'],
//        ['name','require|unique:document,name|max:128','文档名称不能为空|已经添加该文档名称|文档名称太长'],
//        ['title','require|max:128','文档标题不能为空|文档标题不能太长'],
//        ['seo_title','require|max:128','SEO标题不能为空|SEO标题不能太长'],
//        ['status','number|in:-1,0,1','状态必须是数字|状态范围不合法'],
//    ];
    protected $rule = [
        "id" => 'require|number',
        "language_id" => 'require|number',
        "title" => 'require|max:64|token',
        "sorting" => 'require|number|gt:0',
        "url_title" => 'require|max:64',
        "version" => 'require|max:11',
        "keywords" => 'require|max:64',
        "description" => 'require|max:120',
    ];
    protected $message = [
        "id.require" => "编辑时，ID是必须的",
        "id.number" => "ID必须是非负数字",
        "title.require" => '名称必须填',
        "title.max" => '最大字符长度不能超过64',
        "sorting.require" => '排序时必须填写排序字段',
        "sorting.number" => '排序字段必须是整数',
        "sorting.gt" => '排序字段必须大于0的整数',
        "url_title.require" => "url标题必须填，如果出现此问题，请检查程序生成url标题的算法",
        "url_title.max" => "url标题长度必须在64个字符以下",
        'version.require' => "版本是用于后期的维护，请务必填写",
        'version.max' => "版本字符长度必须在11个字符以下",
        "keywords.require" => '关键词必须填',
        "keywords.max" => '关键词必须少于64个字符',
        "description.require" => '描述必须填',
        "description.max" => '描述最长不能超过120个字符',
    ];
    /**场景设置**/
    protected $scene = [
        'edit' => ['id', 'language_id', 'version', 'title', 'keywords', 'description'],
        'add' => ['language_id', 'url_title', 'version', 'title', 'keywords', 'description'],
        'sort' => ['sorting'],
    ];
}
