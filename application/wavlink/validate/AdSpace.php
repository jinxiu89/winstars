<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2017/8/28
 * Time: 10:00
 *内容管理之 推荐位管理验证规则。
 */
namespace app\wavlink\validate;

class AdSpace extends BaseValidate
{
    /**验证规则**/
    protected $rule = [
        ['id','number','id不合法'],
        ['name','require|unique:AdSpace,name|max:128','广告位不能为空|已经添加该推荐位类别'],
        ['code','require|unique:AdSpace,code|max:32|alphaDash','code标识不能为空|code标识必须唯一|code标识不能太长|code标识必须为字母和数字，下划线_及破折号-'],
        ['description','require|max:128','说明不能为空|说明不能太长（128个字符以下）'],
        ['status','number|in:-1,0,1','状态必须是数字|状态范围不合法'],
    ];
    /**场景设置**/
    protected $scene = [
        'add'   => ['name','code','description','status'],
        'edit'  => ['id','name','code','description','status'],
    ];
}
