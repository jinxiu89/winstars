<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2017/9/8
 * Time: 15:00
 */
namespace app\common\model;

use think\Session;

Class Customer extends BaseModel
{
    protected $autoWriteTimestamp = 'datetime';
    protected $table = 'customer';
    /**
     * @param $data
     * @return int
     */
    public function CheckCustomer($data) {
        $email = $this::get(['email' => $data['email']]);
        //验证邮箱
        if (!$email || $email['status'] != 1) {
            return -1;
        }
        //验证密码
        if ($email['password'] !== GetPassword($data['password'])) {
            return -2;
        }
        $customerUpdateData = [
            'last_login_time' => date("Y-m-d H:i:s",time()),
            'last_login_ip' => request()->ip(),
        ];
        $this->upDateById($customerUpdateData, $email['vistor_id']);
        Session::set('CustomerInfo',$email,'Customer');
        return 1;
    }

    public function upDateById($data, $id) {
        //allowField 过滤data数组中非数据表中的数据
        return $this->allowField(true)->save($data, ['vistor_id' => $id]);
    }
}