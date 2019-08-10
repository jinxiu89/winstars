<?php
function arr_unique($arr2d){
	foreach ($arr2d as $k=>$v) {
		$v=join(',',$v);
		$temp[]=$v;
	}
	if($temp){
		$temp=array_unique($temp);
		foreach ($temp as $k=>$v) {
			$temp[$k]=explode(',', $v);
		}
		return $temp;
	}
}

function getDriverUrl($id){
    $obj=new app\common\model\DriverDownload();
    try {
        $result = $obj->field('requirement,url')->where(['driver_id' => $id])->select();
        $html=[];
        foreach ($result as $item){
            $html[]="<a href='{$item['url']}' target='_blank' class='bg_inverted tc border_bottom_color dInlineBlack'>{$item['requirement']}</a>";
        }
        return implode('', $html);

    } catch (\think\db\exception\DataNotFoundException $e) {
    } catch (\think\db\exception\ModelNotFoundException $e) {
    } catch (\think\exception\DbException $e) {
    }
}
