<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/9/6
 * Time: 17:41
 */

namespace app\wavlink\controller;


class Cover extends BaseAdmin
{
    public function edit(){
        $file=fopen(ADMIN_VIEWS.'/cover/cover.html','r') or die("不能打开此文件");
        $content=file_get_contents(ADMIN_VIEWS.'/cover/cover.html');
        $content=htmlspecialchars($content);
        fclose($file);
        return $this->fetch(''
        ,['content'=>$content]);
    }
    public function save(){
        $file=fopen(ADMIN_VIEWS.'/cover/cover.html','w') or die("不能打开此文件");
        if(request()->isAjax()){
            $data=input('post.');
            $data=$data['code'];
            if(fwrite($file,$data)){
                return show(1,'','','','', '添加成功');
            }else{
                return show(0,'','','','', '添加失败');
            }
        }
    }
}