<?php
namespace app\en_us\controller;
use app\common\model\Images as ImagesModel;
class Index extends Base
{
    public function index()
    {
        $file=fopen(ADMIN_VIEWS.'/cover/cover.html','r') or die("不能打开此文件");
        $content=file_get_contents(ADMIN_VIEWS.'/cover/cover.html');
        return $this->fetch('',['content'=>$content]);
    }
    public function build_html(){
        $this->index('index');
        return show(1,'','','','','更新首页缓存成功');
    }
    public function en(){
        $this->redirect(url('/en_us/index'));
    }
    public function product($id=''){
        $this->redirect(url('/en_us/index'));
    }
}