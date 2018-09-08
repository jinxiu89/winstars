<?php

namespace app\en_us\controller;

use app\common\model\Partner;
use app\en_us\validate\Index as IndexValidate;
use app\common\model\Strange;

class Index extends Base
{
    /***
     * 首页头部使用文件存储静态文件，后台提供一个管理接口
     * @return mixed
     */
    public function index()
    {
        $partner = ((new Partner())->getDateByStatus());
        $file = fopen(ADMIN_VIEWS . '/cover/cover.html', 'r') or die("不能打开此文件");
        $content = file_get_contents(ADMIN_VIEWS . '/cover/cover.html');
        return $this->fetch('', ['content' => $content, 'partner' => $partner['data']]);
    }

    public function add()
    {
        if (request()->isAjax()) {
            $data = input('post.');
            $validate = new IndexValidate();
            if (!$validate->check($data)) {
                return show(1, '', '', '', '', $validate->getError());
            } else {
                if ((new Strange())->saveData($data)) {
                    return show(1, '', '', '', '', 'success');
                } else {
                    return show(0, '', '', '', '', 'failed');
                }
            }
        }
    }

    public function build_html()
    {
        $this->index('index');
        return show(1, '', '', '', '', '更新首页缓存成功');
    }

    public function en()
    {
        $this->redirect(url('/en_us/index'));
    }

    public function product($id = '')
    {
        $this->redirect(url('/en_us/index'));
    }
}