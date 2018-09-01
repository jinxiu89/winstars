<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2017/12/9
 * Time: 14:24
 */
namespace app\vistor\common\model;
use think\Model;

Class VistorModel extends Model{
    /**
     * 用户登录认证
     * @return string
     * @internal param $data
     */

    public function login($data){
        $map['email'] = $data['email'];
        $map['status'] = 1;
        $password = $data['password'];
        //获取用户数据
        $user = model("Vistor")->getUser($map);
        if (GetPassword($password) == $user['password']){
            return $user['vistor_id'];
        }else{
            return '登录失败';
        }
//        if (is_array($user)){
//            /*
//             * 验证用户密码
//             */
//            if (GetPassword($password) === $user['password']){
////                $this->updateLogin($user['vistor_id']);//更新用户信息
////                $this->autoLogin($user);
//                return $user; //登录成功，返回用户IDp
//            }else{
//                return -2;//密码错误
//            }
//        }else{
//            return -1; //用户不存在或者被禁用
//        }
    }

    /**
     * 更新用户登录信息
     * @param $uid
     */
    protected function updateLogin($uid){
        $data = array(
            'vistor_id'  => $uid,
            'last_login_time' => time(),
            'last_login_ip'   => get_client_ip(1),
        );
        model("Vistor")->save($data);
    }
}