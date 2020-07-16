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

/***
 * 添加日期：2019-04-02
 * 添加人：邱锦
 * 用途：给定一个分类ID 获取他所有的父分类 并合并成str到前端展现
 * @param $id
 * @return string
 * @throws \think\exception\DbException
 */
function getParent($id){
    // 根据parent_id来获取新增的这个分类是属于哪个分类的
    $cate=collection(\app\common\model\Category::all())->toArray();
    $id=intval($id);
    $parents=\app\common\helper\Category::getParents($cate,$id);
    foreach ($parents as $v){
        $name[]=$v['name'];
    }
    $parent=implode("->",$name);
    return $parent;
}

//产品管理类获取所属分类
function getChild($id){
    $id = intval($id);
    $data = \app\common\model\Product::getCategoryWithProduct($id);
    $name=[];
    foreach ($data as $v){
        $name[]=$v['name'];
    }
    $nameStr = implode("->",$name);
    return $nameStr;
}

//首页图片管理获取分类
function getAdSpaceName($id){
    $ids = intval($id);
    $data = \app\common\model\AdSpace::get($ids);
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

///**
// * 记录数查询
// * 用于排序操作
// * @param $table
// * @param $map
// * @param $order
// * @param $limit
// * @param $field
// * @param $map2
// * @return array|false|PDOStatement|string|\think\Model
// * @throws \think\db\exception\DataNotFoundException
// * @throws \think\db\exception\ModelNotFoundException
// * @throws \think\exception\DbException
// */
//function limit($table,$map,$order,$limit,$field,$map2){
//    $_listorder = model($table)->where($map)
//        ->where($map2)
//        ->field($field)
//        ->order($order)
//        ->limit($limit)
//        ->select();
//    $listorder = collection($_listorder)->toArray();
//
//    if (!empty($listorder)){
//        return $listorder;
//    }else{
////        return show(0,'已经是置顶或者置底了，移动它的位置请上移或者下移，或者直接修改排序','');
//        return false;
//    }
//}

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

