<?php

//分类所属分类
function getCategory($id){
    $model = request()->controller();
    $id = intval($id);
    if (empty($id)){
        return '<span style="color: blue">这是一级分类</span>';
    }
    $category = model($model)->find($id);
    return '<span style="color: blue">'.$category['name'].'</span>';
}

//产品管理类获取所属分类
function getChild($id){
    $id = intval($id);
    $data = \app\common\model\Product::getCategoryWithProduct($id);
    $name=[];
    foreach ($data as $v){
        $name[]=$v['name'];
    }
    $nameStr = implode("|",$name);
    return $nameStr;
}

//首页图片管理获取分类
function featured($id){
    $ids = intval($id);
    $data = \app\common\model\Featured::get($ids);
    $name = $data['name'];
    if ($ids == 1){
        $str = "<span style='color:red'>$name</span>";
    }elseif ($ids ==2 ){
        $str = "<span  style='color:blue'>$name</span>";
    }elseif ($ids==3){
        $str = "<span  style='color: orange'>$name</span>";
    }elseif ($ids == 4){
        $str = "<span style='color: pink'>$name</span>";
    }
    else{
        $str = "<span style='color: green'>$name</span>";
    }
    return $str;
}

//得到语言id获取语言名称，并改变其字体颜色
function getLanguage($id)
{
    $map= intval($id);
    $data = \app\common\model\Language::get($map);
    $name = $data['name'];
    if($map == 1){
        $str = "<span style='color:blue;'>$name</span>";
    }elseif ($map == 2){
        $str = "<span style='color:red;'>$name</span>";
    }else{
        $str = "<span style='color:orange;'>$name</span>";
    }
    return $str;
}
//根据语言id，单纯的获取语言名称，
function getLanguageOne($id) {
    $map = intval($id);
    $data = \app\common\model\Language::get($map);
    return $data['name'];
}

//首页图片管理获取类别
function imagesType($id)
{
    $ids = intval($id);
    $type = Config('images.images_type');
    foreach ($type as $k => $v) {
        if ($ids == $k) {
            return $v;
        }
    }
}
//固件处理状态信息
function ticket_status($status){
    if($status == 1){
        $str = "<span class='label label-success radius'>已处理</span>";
    }elseif($status == 0){
        $str = "<span class='label label-error radius'>垃圾信息</span>";
    }else {
        $str = "<span class='label label-danger radius'>未处理</span>";
    }
    return $str;
}

/**
 * @param $toMail
 * @param $toName
 * @param $subject
 * @param $content
 * @param null $attachment
 * @return bool 阿里云的smtp邮件发送
 * 阿里云的smtp邮件发送
 * @internal param $to
 */
function sendMail($toMail, $toName, $subject, $content, $attachment = null){
    vendor( 'phpmailer.PHPMailerAutoload' );
    vendor('phpmailer.class#phpmailer');
    $mail=new PHPMailer();
    $mail->CharSet = 'utf-8';
    $mail->IsSMTP();
    $mail->SMTPDebug = 0;
    $mail->Debugoutput = 'html';
//    $mail->Host = 'smtpdm.aliyun.com';                         // SMTP服务器地址
    $mail->Host = 'smtpdm-ap-southeast-1.aliyun.com';                         // SMTP服务器地址
    $mail->Port = 465;                         // 端口号
    $mail->SMTPAuth = true;                // SMTP登录认证
    $mail->SMTPSecure = 'ssl';            // SMTP安全协议
//    $mail->Username = 'ghfhzj@www.guohongfu.top';                 // SMTP登录邮箱
//    $mail->Password = 'GuoHuang19941993';                 // SMTP登录密码
//    $mail->setFrom('ghfhzj@www.guohongfu.top','Guo');            // 发件人邮箱和名称
//    $mail->addReplyTo('1215798914@qq.com', 'Guo'); // 回复邮箱和名称
    $mail->Username = 'system@service.wavlink.us';                 // SMTP登录邮箱
    $mail->Password = 'Wh32Ym69B10c';                 // SMTP登录密码
    $mail->setFrom('system@service.wavlink.us', 'system');            // 发件人邮箱和名称
    $mail->addReplyTo('content@wavlink.com', 'Wavlink.com'); // 回复邮箱和名称
    $mail->AddAddress($toMail,$toName);
    $mail->Subject = $subject;
    $mail->Body=$content;
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
//    if($result){
//        return true;
//    }else{
//        return false;
//    }
}

/**
 * 记录数查询
 * 用于排序操作
 * @param $table
 * @param $map
 * @param $order
 * @param $limit
 * @param $map2
 * @param $field
 * @return array|false|PDOStatement|string|\think\Model
 */
function limit($table,$map,$order,$limit,$field,$map2){
    $_listorder = model($table)->where($map)
        ->where($map2)
        ->field($field)
        ->order($order)
        ->limit($limit)
        ->select();
    $listorder = collection($_listorder)->toArray();

    if (!empty($listorder)){
        return $listorder;
    }else{
//        return show(0,'已经是置顶或者置底了，移动它的位置请上移或者下移，或者直接修改排序','');
        return false;
    }
}

/**
 * 获取某个目录下的php文件名的函数
 */
function getControllers($dir){
    $pathList = glob($dir.'/*.php');
    $res = [];
    foreach ($pathList as $key=>$value){
        $res[] = basename($value,'.php');
    }
    return $res;
}

/**
 * 获取某个控制器的方法名的函数
 * 过滤父级Base控制器的方法，只保留自己的
 */
function getActions($className,$base='/app/wavlink/controller/BaseAdmin'){
    $methods = get_class_methods(new $className());
    $baseMethods = get_class_methods(new $base());
    $res = array_diff($methods,$baseMethods);
    return $res;
}

/**
 * 删除缓存文件
 * @param $path
 * @param bool $diedir
 * @return string
 */
function delcache($path,$diedir = false)
{
    $message = "";
    $handle = opendir($path);
    if ($handle) {
        while (false !== ( $item = readdir($handle) )) {
            if ($item != "." && $item != "..") {
                if (is_dir("$path/$item")) {
                    $msg = delcache("$path/$item", $diedir);
                    if ( $msg ){
                        $message .= $msg;
                    }
                } else {
                    $message .= "删除文件" . $item;
                    if (unlink("$path/$item")){
                        $message .= "成功<br />";
                    } else {
                        $message .= "失败<br />";
                    }
                }
            }
        }
        closedir($handle);
        if ($diedir){
            if ( rmdir($path) ){
                $message .= "删除目录" . dirname($path) . "<br />";
            } else {
                $message .= "删除目录" . dirname($path) . "失败<br />";
            }
        }
    } else {
        if (file_exists($path)) {
            if (unlink($path)){
                $message .= "删除文件" . basename($path) . "<br />";
            } else {
                $message .= "删除文件" . basename($path) . "失败<br />";
            }
        } else {
            $message .= "文件" . basename($path) . "不存在<br />";
        }
    }
    return $message;
}

