<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2017/8/28
 * Time: 10:00
 *服务分类之 分类管理验证规则。
 */
namespace app\wavlink\validate;

class ServiceCategory extends BaseValidate
{
    /**验证规则**/
    protected $rule = [
        ['id','number','id不合法'],
        ['language_id','number','语言不能为空'],
        ['seo_title','require|max:128','SEO标题不能为空|SEO标题不能太长'],
        ['keywords','require|max:128','关键词不能为空|关键词不能太长'],
        ['description','require|max:128','描述不能为空|描述不能太长'],
        ['status','number|in:-1,0,1','状态必须是数字|状态范围不合法'],
    ];
    /**场景设置**/
    protected $scene = [
    ];
}
