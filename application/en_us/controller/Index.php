<?php

namespace app\en_us\controller;

use app\common\model\Partner;
use app\en_us\validate\Index as IndexValidate;
use app\common\model\Strange;
use app\common\model\AdSpace;
use think\Request;

class Index extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $result = (new AdSpace())->getDataByCode('Index');
        if ($result['status'] == true) {
            $this->assign('banner', $result['data']);
        }
    }

    /***
     * 首页头部使用文件存储静态文件，后台提供一个管理接口
     * @return mixed
     * @throws \Exception
     */
    public function index()
    {
        if (request()->isPost()) {
            abort(404);
//            $data = input('post.');
//            $validate = new IndexValidate();
//            if (!$validate->check($data)) {
//                return show(0, '', '', '', '', $validate->getError());
//            } else {
//                if ((new Strange())->saveData($data)) {
//                    $data = array(
//                        'toMail' => 'john@win-star.com',
//                        'toName' => 'John',
//                        'subject' => 'This is win-star.com Enquiry mail',
//                        'content' => $data['content'],
//                        'replyTo' => $data['email'],
//                        'relyName' => $data['name']
//                    );
//                    try {
//                        $result = sendMail($data);
//                        return $result ? show(1, '', '', '', '', 'success') : show(0, '', '', '', '', '失败');//邮件也发送成功了
//                    } catch (\Exception $exception) {
//                        return show(0, '', '', '', '', $exception->getMessage());
//                    }
//                } else {
//                    return show(0, '', '', '', '', 'failed');
//                }
//            }
        }
        if (request()->isGet()) {
            $partner = ((new Partner())->getDateByStatus());
            $file = fopen(ADMIN_VIEWS . '/cover/cover.html', 'r') or die("不能打开此文件");
            $content = file_get_contents(ADMIN_VIEWS . '/cover/cover.html');
            return $this->fetch($this->template . '/index/index.html',
                [
                    'content' => $content,
                    'partner' => $partner['data'],
                ]);
        }
    }

    public function Job_Fair()
    {
        return $this->fetch($this->template . '/index/Job_Fair.html');
    }


//    public function build_html()
//    {
//        $this->index('index');
//        return show(1, '', '', '', '', '更新首页缓存成功');
//    }

    public function en()
    {
        $this->redirect(url('/en_us/index'));
    }

    public function product($id = '')
    {
        $this->redirect(url('/en_us/index'));
    }
}