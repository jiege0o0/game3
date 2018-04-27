<?php 
	$GameConfig = new stdClass();
	$GameConfig->year = 30*60;//1年的长度，单位秒
	$GameConfig->rankLen = 10;//
	$GameConfig->currentLen = 30;//
	$GameConfig->dieLen = 10;//
	$GameConfig->historyLen = 20;//
	
	function createProp($quality){//首位为type,次位品质
		$type = numToStr(1);
		$quality = numToStr($quality);
		return $type.$quality.'#'.base64_encode('Prop'.rand(1,999));
	}
	
	//取基础数据
	function getRoleBase($ids,&$roleData){
		$obj = new stdClass();
		foreach($roleData as $key=>$value)
		{
			if(!in_array($key,$ids))
				continue;
			if($obj->{$key})
				continue;
			$obj->{$key} = new stdClass();
			$obj->{$key}->id = $value->id;
			$obj->{$key}->f = $value->f;
			$obj->{$key}->g = $value->g;
			$obj->{$key}->n = $value->n;
			$obj->{$key}->b = $value->b;
			$obj->{$key}->d = $value->d;
			$obj->{$key}->h = $value->h;
		}
		return $obj;
	}
	
	
?> 