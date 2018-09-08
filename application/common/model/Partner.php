<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/9/7
 * Time: 16:27
 */

namespace app\common\model;


class Partner extends BaseModel
{
    protected $autoWriteTimestamp = "datetime";
    protected $table = "tb_partner";
    public function getDateByStatus(){
        $model='Partner';
        $map=[];
        $order=[
            'id'    =>'asc',
        ];
        return Search($model,$map,$order);
    }
}