<?php 
do{
	$userData->world;
	$userData->current_role;
	$userData->history_role;
	
	//处理旧的
	
	
	//增加新的
	
	
	
	
}while(false)

//解释
function decodeRole($str,$needAction){
	$temp = explode("|",$str);
	$arr = array(
	'id'=>(int)$temp[0],
	// 'name'=>(int)$temp[1],
	'gender'=>(int)$temp[2],
	'born'=>(int)$temp[3],
	'force'=>(int)$temp[4],
	'endTime'=>(int)$temp[5],
	'waitCD'=>(int)$temp[6],
	'dieTime'=>(int)$temp[7]
	);
	if($needAction)
	{
		$arr['nextAction'] = decodeAction($temp[8]);
		$arr['history'] = decodeAction($temp[9]);
	}
	return $arr;
}

function decodeAction($str){
	$temp = explode(",",$str);
	$arr = array(
		'time'=>(int)$temp[0],
		'force'=>(int)$temp[1],
		'type'=>(int)$temp[2]
		// 'remark'=>(int)$temp[3],
	);
	return $arr;
}

//编码
function encodeRole($data){


	return implode('|',$arr);
}

function encodeAction($data){
	$arr = array(
		$data['time'],
		$data['force'],
		$data['type'],
		base64_encode(json_encode($data['remark'])),
	);
	return implode(',',$arr);

}
	
?> 










