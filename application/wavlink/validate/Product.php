<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2017/8/28
 * Time: 10:00
 *内容管理之 产品管理验证规则。
 */
namespace app\wavlink\validate;

class Product extends BaseValidate
{
    /**验证规则**/
    protected $rule = [
        ['id','number','id不合法'],
        ['name','require|max:128|token','产品名称不能为空|产品名称不能多余128个字符长度|重复提交'],
        ['model','require|max:64','产品型号不能为空|产品型号不能太长'],
        ['seo_title','require|max:128','SEO标题不能为空|SEO标题不能太长'],
        ['url_title','require|unique:Product','url标题必须填|不能重复'],
        ['keywords','require|max:128','关键词不能为空|关键词不能太长'],
        ['description','require','描述不能为空'],
        ['image_litpic_url' ,'max:500','缩略图外链地址太长'],
        ['image_pc_url' ,'max:500','PC封面外链地址太长'],
        ['image_mobile_url' ,'max:500','手机端封面外链地址太长'],
        ['language_id','require|number|gt:0','语言不能为空'],
        ['cates','require','分类是不是没有选择'],
        ['status','number|in:-1,0,1','状态必须是数字|状态范围不合法'],
    ];
    /**场景设置**/
    protected $scene = [
        "add"=>['name','model','seo_title','url_title','keywords','description']
    ];
}
