<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2017/9/8
 * Time: 15:00
 * 推荐位管理
 */

namespace app\common\model;

use think\exception\DbException;

Class AdSpace extends BaseModel
{
    protected $table = 'tb_ad_space';

    public function banners()
    {
        return $this->hasMany('Banner', 'space_id')->where('status','=',1)->order(['listorder'=>'desc','id'=>'desc','create_time'=> 'desc']);
    }

    public function add($data)
    {
        $data['status'] = 1;
        return $this->save($data);
    }

    /***
     * @param $code
     * @return array
     * 根据广告位的标识来获取数据
     * model 层 返回的数据格式以后全部都要用['status'=>'状态','message'=>'消息','data'=>'数据']
     *
     */
    public function getDataByCode($code)
    {
        try {
            $adSpace = self::get(['code' => $code]);
            $data = collection($adSpace->banners)->toArray();
            return ['status' => true, 'message' => 'ok', 'data' => $data];
        } catch (Exception $exception) {
            return ['status' => false, 'message' => $exception->getMessage(), 'data' => []];
        } catch (DbException $dbException) {
            return ['status' => false, 'message' => $dbException->getMessage(), 'data' => []];
        }
    }
}
