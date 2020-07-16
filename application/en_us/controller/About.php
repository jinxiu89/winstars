<?php
/**
 * Created by PhpStorm.
 * User: wavlink
 * Date: 2017/11/25
 * Time: 10:15
 */

namespace app\en_us\controller;

use app\common\model\About as AboutModel;
use think\Request;
use app\common\model\AdSpace;

class About extends Base
{

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $result = (new AdSpace())->getDataByCode('About');
        if ($result['status'] == true) {
            $this->assign('banner', $result['data']);
        }
    }

    /***
     * 前端关于我们的数据输出，美化URL，将原来的ID转换为title，
     * 语言判断，以便以后输出多种语言的页面，
     * 思路：必备一个$about参数，用于接受到底调用那一篇资料，通过模型查找符合要求的资料，另外如果找不到还需要报404错误，
     * 到时候还需要调试没一个错误，不允许出现系统异常。
     * @param string $about
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index($about="Company_Overview")
    {
        if (empty($about) or !isset($about)) {
            abort(404);
        }
        $result = AboutModel::getDetailsByUrlTitle($about, $this->code);
        if (!isset($result) or empty($result)) {
            abort(404);
        }
        return $this->fetch($this->template . '/about/index.html', [
            'result' => $result
        ]);
    }

    /***
     * @param $about
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function details($about)
    {
        if (empty($about) or !isset($about)) {
            abort(404);
        }
        $result = AboutModel::getDetailsByUrlTitle($about, $this->code);
        if (!isset($result) or empty($result)) {
            abort(404);
        }
        return $this->fetch($this->template . '/about/index.html', [
            'result' => $result
        ]);
    }
}