<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/7/8
 * Time: 13:32
 */

namespace app\common\model;


class DriverDownload extends BaseModel
{
    protected $table = 'tb_driver_download';

    public function driver()
    {
        return $this->belongsTo('Driver');
    }

}