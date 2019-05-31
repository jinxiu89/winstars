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
    /***
     * $file  fopen打开顶部静态文件，
     * 注意htmlspecialchars 这个问题  解决 codemirror文本域问题的
     * @return mixed
     */
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
                return show(1,'','','','', '保存成功');
            }else{
                return show(0,'','','','', '保存失败');
            }
        }
    }
    public function cover_css(){
        $file=fopen(STATIC_PATH.'/frontend/en-us/css/style/Home.css','r') or die("文件打不开");
        $content=file_get_contents(STATIC_PATH.'/frontend/en-us/css/style/Home.css');
        $content=htmlspecialchars($content);
        fclose($file);
        return $this->fetch('',['content'=>$content]
        );
    }
    public function cover_css_save(){
        $file=fopen(STATIC_PATH.'/frontend/en-us/css/style/Home.css','w') or die("文件打不开");
        if(request()->isAjax()){
            $data=input('post.');
            $data=$data['code'];
            if(fwrite($file,$data)){
                return show(1,'','','','', '保存成功');
            }else{
                return show(0,'','','','', '保存失败');
            }
        }
    }
}