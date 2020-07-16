<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2017/8/28
 * Time: 10:00
 *内容管理之 文章管理验证规则。
 */
namespace app\wavlink\validate;

class Banner extends BaseValidate
{
    /**验证规则**/
    protected $rule = [
        ['id','number','id不合法'],
        ['listorder','isPositiveInteger','排序必须是正整数'],
        ['title','require|max:100','标题必须填|标题太长了'],
        ['space_id','require','选择广告空间'],
        ['url','max:255','链接不能太长'],
        ['status','integer|in:-1,0,1','状态必须是数字|状态范围不合法'],
    ];

    /**
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * 验证推荐位的正常产品数
     * 幻灯片不限产品
     * 第二屏热卖限一个产品
     * 第三屏主流限4个产品
     * 第四屏新品限一个产品
     * 推荐位限3个产品
     * 公告推荐位一个正常产品
     *
     * @return bool
     * @throws \think\Exception
     */
    protected function AdSpaceLimit($value, $rule = '', $data = '', $field = '') {
        if (!empty($data['id'])){
            return true;
        }else{
            $map = [
                'space_id' => $value,
                'status'      => 1
            ];
            $con = request()->controller();
            $count = model($con)->where($map)->count();
            if($value == 1){
                return true;
            }elseif ($value == 2 && $count<1){
                return true;
            }elseif ($value == 3 && $count < 4 ){
                return true;
            }elseif ($value == 4 && $count > 1){
                return true;
            }elseif ($value == 5 && $count < 3 ){
                return true;
            }elseif ($value == 6 && $count < 2){
                return true;
            }else{
                return false;
            }
        }
    }
    /**场景设置**/
    protected $scene = [
        'add'   => ['listorder','title','space_id','url'],
        'edit'  => ['id','listorder','title','space_id','url'],
        'changeStatus'=>['id','status']
    ];
}
