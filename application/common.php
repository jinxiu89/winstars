<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 应用公共文件
//分类
function pagination($obj){
    if (!$obj){
        return '';
    }
    $params = request()->param();
    return $obj->appends($params)->render();
}
//status输出
function status($status) {
    if($status == 1){
        $str = "<span class='label label-success radius'>正常</span>";
    }elseif($status == 0){
        $str = "<span class='label label-error radius'>删除</span>";
    }else {
        $str = "<span class='label label-danger radius'>禁用</span>";
    }
    return $str;
}
//用户登录密码 key 校验
function GetPassword($password){
    return md5(sha1($password).$key='ad;lkfjSDAF@@#$@#Q%4>>><KJJH11111111111111########sdfasdf!!!bbbsdf');
}

function Search($table,$map=array(),$order){
    //公共查询函数
    $data   = model($table)->where($map)->order($order)->paginate();
    $counts = model($table)->where($map)->count();
    if($data){
        $result=array(
            'data'=>$data,
            'count'=>$counts,
        );
        return $result;
    }else{
        return '';
    }
}
// 字符串进行数组处理，以逗号分割组合
function ModelsArr($data,$key,$newKey){
    foreach ($data as $k => $vo){
        $models = explode(",",$vo[$key]);
//        $models = array_chunk($_models,2);
        $vo[$newKey] = $models;
    }
    return $data;
}
/**
 * 校验传递的参数是否是正整数
 * @param $value
 * @return bool
 */
function isPositiveInteger($value){
    if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
        return true;
    } else {
        return false;
    }
}
/***
 * 根据分类ID获取分类名称
 * @param $id
 * @return string
 */
function getServiceCategory($id)
{
    $map = intval($id);
    if ($map == 0){
        return "<span style='color: blue'>一级分类</span>";
    }
    $data = \app\common\model\ServiceCategory::get($map);
    return $data['name'];
}

/***
 * 返回错误信息
 * @param $status
 * @param $message
 * @param $title
 * @param $btn
 * @param string $url 返回跳转url
 * @param array $data 数据
 */
function show($status,$message='', $title='', $btn='', $url='', $data=array()){
    $res=array(
        'status'=>$status,
        'message'=>$message,
        'jump_url'=>$url,
        'data'=>$data,
        'title'=>$title,
        'btn'=>$btn
    );
    exit(json_encode($res));
}

/**参数：
 * $str_cut    需要截断的字符串
 * $length     允许字符串显示的最大长度
 * 程序功能：截取全角和半角（汉字和英文）混合的字符串以避免乱码
 * $sublen    截取的长度
 * ------------------------------------------------------
 * @param $string
 * @param $sublen
 * @param int $start
 * @param string $code
 * @return string
 */
function cut_str($string, $sublen, $start =0, $code ='UTF-8')
{
    if($code =='UTF-8')
    {
        $pa ="/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
        preg_match_all($pa, $string, $t_string);if(count($t_string[0])- $start > $sublen)return join('', array_slice($t_string[0], $start, $sublen))."...";
        return join('', array_slice($t_string[0], $start, $sublen));
    }
    else
    {
        $start = $start*2;
        $sublen = $sublen*2;
        $strlen = strlen($string);
        $tmpstr ='';for($i=0; $i<$strlen; $i++)
    {
        if($i>=$start && $i<($start+$sublen))
        {
            if(ord(substr($string, $i,1))>129)
            {
                $tmpstr.= substr($string, $i,2);
            }
            else
            {
                $tmpstr.= substr($string, $i,1);
            }
        }
        if(ord(substr($string, $i,1))>129) $i++;
    }
        if(strlen($tmpstr)<$strlen ) $tmpstr.="...";
        return $tmpstr;
    }
}
/**
 * @param string $url  get请求地址
 * @param int $httpCode 返回状态码
 * @return mixed
 */
function curl_get($url,&$httpCode = 0){
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

    //不做证书校验 为 false，部署在linux环境下请改为true
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10);
    $file_contents = curl_exec($ch);
    $httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $file_contents;
}