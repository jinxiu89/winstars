<?php
namespace app\wavlink\validate;
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2018/1/9
 * Time: 13:36
 */

use app\lib\exception\ParameterException;
use think\Request;
use think\Validate;

class BaseValidate extends Validate
{
    /***
     * @param string $scene
     * @return bool
     * @throws ParameterException
     */
    public function goCheck($scene=''){
        // 获取http传入的参数
        // 对这些参数做检验
        $request = Request::instance();
        $params = $request->param();
        $result = $this->scene($scene)->check($params);
        if(!$result){
            $e = new ParameterException();
            $e->msg = $this->error;
            throw $e;
        }else{
            return true;
        }
    }
    public function goDateCheck($data){
        $result = $this->check($data);
        if(!$result){
            $e = new ParameterException();
            $e->msg = $this->error;
            throw $e;
//            return $this->error;
        }else{
            return true;
        }
    }
    /**
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * 验证参数id是否是正整数
     * @return bool
     */
    protected function isPositiveInteger($value, $rule = '', $data = '', $field = '') {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 判断url_title 唯一的
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function urlTitleIsOnly($value,$rule = '', $data = '', $field = ''){
        $con = request()->controller();
        $map = [
            'url_title'=>$value,
            'language_id'=>$data['language_id']
        ];
        if (empty($data['id'])){
            $result = model($con)->where($map)
                ->field('url_title')
                ->find();
        }else{
            $result = model($con)->where('id','neq',$data['id'])
                ->where($map)
                ->field('url_title')
                ->find();
        }
        if ($result){
            return false;
        }else{
            return true;
        }
    }
}