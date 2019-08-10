<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2018/4/17
 * Time: 15:34
 */

namespace app\wavlink\validate;


class Faq extends BaseValidate
{
    protected $rule = [
        'id' => "require|number|gt:0",
        'language_id' => 'require|number|gt:0',
        'category_id' => 'require|number|gt:0',
        'title' => 'require|max:128',
        'url_title' => 'require|max:32',
        'keywords' => 'require|max:128',
        'description' => 'require|max:255',
        'answer' => 'require',
        'sorting' => 'require|number|gt:0',
        'status' => 'require|number|gt:0',
    ];
    protected $message = [
        'id.require' => 'ID在修改编辑时为必选参数',
        'id.number' => 'ID必须为非负整数',
        'id.gt' => 'ID必须为非负整数',
        'language_id.require' => '无论哪个场景下，语言ID必须有',
        'language_id.number' => '语言ID必须为非负整数',
        'language_id.gt' => '语言ID必须为非负整数',
        'title.require' => 'FAQ标题必须填',
        'keywords.require' => '关键词必须填！',
        'keywords.max' => '关键词必须在128个字符以下！',
        'description.require' => '描述必须填！',
        'description.max' => '关键词必须在128个字符以下',
        'answer.require' => '必须要有一个合理的解答才行？',
        'sorting.require' => '排序必须填！',
        'sorting.number' => '排序必须是一个大于0的整数！',
        'sorting.gt' => '排序必须是一个大于0的整数！',
        'status.require' => '状态二选一！',
        'status.number' => '状态必须是正整数！',
        'status.gt' => '状态必须是正整数！'
    ];
    protected $scene = [
        'edit' => ['id', 'language_id', 'category_id', 'title', 'keywords', 'description', 'answer','sorting'],
        'add' => ['language_id', 'category_id', 'title', 'url_title', 'keywords', 'description', 'answer','sorting'],
    ];
}