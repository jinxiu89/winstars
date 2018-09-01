<?php

namespace app\api\controller;

use think\Controller;
use think\Request;

class Image extends Controller
{
    public function upload()
    {
        $file = Request::instance()->file('file');
        //给定一个目录
        $info = $file->move('upload', '', false);
        if ($info && $info->getPathname()) {
            //'/' 这个斜杠对应的是网站入口文件,public
            return show(1, 'success', '/' . $info->getPathname());
        } else {
            return show(0, 'upload error');
        }
    }

    /***
     * 用于markdown的上传图片
     * 成功，返回url地址，不成功返回失败
     *
     */
    public function markdownUpload()
    {
        $file = request()->file('editormd-image-file');
        $info = $file->move('upload', '', false);
        if ($info) {
            $data = array(
                "success" => 1,
                'message'=> 'success',
                "url" => '/'.$info->getPathname()
            );
            echo json_encode($data);
        }else{
            echo json_encode(array(
                'success'=>0,
                'message'=>'failed',
            ));
        }
    }

    public function editorUpload()
    {
        $file = Request::instance()->file('imgFile');
        //给定一个目录
        $info = $file->move('ueditor');
        if ($info && $info->getPathname()) {
            //'/' 这个斜杠对应的是网站入口文件,public
            $data = array(
                'error' => 0,
                'url' => '/' . $info->getPathname(),
            );
            print_r(json_encode($data));
        } else {
            return show(1, 'upload error', '', '', '', $info->getError());
        }
    }

    public function imageAddress()
    {
        $data = [
            'path' => '/ueditor',
            'dir' => 'ueditor'
        ];

        return json_encode($data);
    }
}
