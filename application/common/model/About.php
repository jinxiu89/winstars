<?php
/**
 * Created by PhpStorm.
 * User: guo
 * Date: 2017/9/8
 * Time: 15:00
 */

namespace app\common\model;

use app\common\model\Language as LanguageModel;
use think\Cache;

class About extends BaseModel
{
    protected $table = "about";

    /***
     * @param string $code
     * @return false|mixed|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 获取about 数据，并缓存起来
     */
    public function getAbouts($code = "en_us")
    {
        $language_id = LanguageModel::getLanguageCodeOrID($code);
        $data = ['status' => 1, 'language_id' => $language_id,];
        $field = 'id,url_title,name';
        if (!$this->debug) {
            if (!Cache::get('footerAbout')) {
                Cache::set('footerAbout', $this->where($data)->field($field)->select());
            }
        }
        return $this->debug ? $this->where($data)->field('id,url_title,name')->select() : Cache::get('footerAbout');
    }

    /***
     * @param $code
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getListByCode($code)
    {
        $language_id = LanguageModel::getLanguageCodeOrID($code);
        $data = ['status' => 1, 'language_id' => $language_id];
        $field = 'id,name,url_title';
        return collection($this->where($data)->field($field)->select())->toArray();
    }
}
