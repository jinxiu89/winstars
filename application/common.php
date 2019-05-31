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
function pagination($obj)
{
    if (!$obj) {
        return '';
    }
    $params = request()->param();
    return $obj->appends($params)->render();
}

//status输出
function status($status)
{
    if ($status == 1) {
        $str = "<span class='label label-success radius'>正常</span>";
    } elseif ($status == 0) {
        $str = "<span class='label label-error radius'>删除</span>";
    } else {
        $str = "<span class='label label-danger radius'>禁用</span>";
    }
    return $str;
}

//用户登录密码 key 校验
function GetPassword($password)
{
    return md5(sha1($password) . $key = 'ad;lkfjSDAF@@#$@#Q%4>>><KJJH11111111111111########sdfasdf!!!bbbsdf');
}

function Search($table, $map = array(), $order)
{
    //公共查询函数
    $data = model($table)->where($map)->order($order)->paginate();
    $counts = model($table)->where($map)->count();
    if ($data) {
        $result = array(
            'data' => $data,
            'count' => $counts,
        );
        return $result;
    } else {
        return '';
    }
}
function getThumbnail($str){
    return substr($str, 0, strpos($str, ','));
}
// 字符串进行数组处理，以逗号分割组合
function ModelsArr($data, $key, $newKey)
{
    foreach ($data as $k => $vo) {
        $models = explode(",", $vo[$key]);
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
function isPositiveInteger($value)
{
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
    if ($map == 0) {
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
function show($status, $message = '', $title = '', $btn = '', $url = '', $data = array())
{
    $res = array(
        'status' => $status,
        'message' => $message,
        'jump_url' => $url,
        'data' => $data,
        'title' => $title,
        'btn' => $btn
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
function cut_str($string, $sublen, $start = 0, $code = 'UTF-8')
{
    if ($code == 'UTF-8') {
        $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
        preg_match_all($pa, $string, $t_string);
        if (count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen)) . "...";
        return join('', array_slice($t_string[0], $start, $sublen));
    } else {
        $start = $start * 2;
        $sublen = $sublen * 2;
        $strlen = strlen($string);
        $tmpstr = '';
        for ($i = 0; $i < $strlen; $i++) {
            if ($i >= $start && $i < ($start + $sublen)) {
                if (ord(substr($string, $i, 1)) > 129) {
                    $tmpstr .= substr($string, $i, 2);
                } else {
                    $tmpstr .= substr($string, $i, 1);
                }
            }
            if (ord(substr($string, $i, 1)) > 129) $i++;
        }
        if (strlen($tmpstr) < $strlen) $tmpstr .= "...";
        return $tmpstr;
    }
}

/**
 * @param string $url get请求地址
 * @param int $httpCode 返回状态码
 * @return mixed
 */
function curl_get($url, &$httpCode = 0)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    //不做证书校验 为 false，部署在linux环境下请改为true
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    $file_contents = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $file_contents;
}

/***
 * 判断是否是移动端
 */
function isMobile()
{
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')) {
        return false;
    }
    if (strrpos($_SERVER['HTTP_USER_AGENT'], 'Windows NT 10.0')) {
        return false;
    }
    //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA'])) {
        //找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }

    //判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array(
            'nokia',
            'sony',
            'ericsson',
            'mot',
            'samsung',
            'htc',
            'sgh',
            'lg',
            'sharp',
            'sie-',
            'philips',
            'panasonic',
            'alcatel',
            'lenovo',
            'iphone',
            'ipod',
            'blackberry',
            'meizu',
            'android',
            'netfront',
            'symbian',
            'ucweb',
            'windowsce',
            'palm',
            'operamini',
            'operamobi',
            'openwave',
            'nexusone',
            'cldc',
            'midp',
            'wap',
            'mobile'
        );
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    //协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}

function getTags($keywords){
    $keywords= explode(',',$keywords);
    $tag=array();
    foreach ($keywords as $v){
        $tag[]="&nbsp;&nbsp;<a style='display: inline-block' title=\"{$v}\"href=\"/en_us/product/search/{$v}\">{$v}</a>";
    }
    return implode('',$tag);
}

/**
 * @param $data
 * @param null $attachment
 * @return bool 阿里云的smtp邮件发送
 * 阿里云的smtp邮件发送
 * @throws phpmailerException $data=array(
 * 'toMail'=>'toMail',
 * 'toName'=>'toName',
 * 'subject'=>'subject',
 * 'content'=>'content',
 * 'replyTo'=>'replyTo',
 * 'relyName'=>'relyName',
 * )
 * @internal param $to
 */
function sendMail($data, $attachment = null){
    vendor( 'phpmailer.PHPMailerAutoload' );
    vendor('phpmailer.class#phpmailer');
    $mail=new PHPMailer();
    $mail->CharSet = 'utf-8';
    $mail->IsSMTP();
    $mail->SMTPDebug = 0;
    $mail->Debugoutput = 'html';     // SMTP服务器地址
    $mail->Host = 'smtpdm-ap-southeast-1.aliyun.com';                         // SMTP服务器地址
    $mail->Port = 465;                         // 端口号
    $mail->SMTPAuth = true;                // SMTP登录认证
    $mail->SMTPSecure = 'ssl';            // SMTP安全协议
    $mail->Username = 'system@service.wavlink.us';                 // SMTP登录邮箱
    $mail->Password = 'Wh32Ym69B10c';                 // SMTP登录密码
    $mail->setFrom('system@service.wavlink.us', 'system');            // 发件人邮箱和名称
    $mail->addReplyTo($data['replyTo'], $data['relyName']); // 回复邮箱和名称 $到联系人
    $mail->AddAddress($data['toMail'],$data['toName']); //发给谁？
    $mail->Subject = $data['subject'];
    $mail->Body=$data['content'];
    if ($attachment) { // 添加附件
        if (is_string($attachment)) {
            is_file($attachment) && $mail->AddAttachment($attachment);
        } else if (is_array($attachment)) {
            foreach ($attachment as $file) {
                is_file($file) && $mail->AddAttachment($file);
            }
        }
    }
    $result = $mail->send();
    return $result ? true : $mail->ErrorInfo;
}