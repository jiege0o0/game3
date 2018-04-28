<?php 
	$GameConfig = new stdClass();
	$GameConfig->year = 30*60;//1��ĳ��ȣ���λ��
	$GameConfig->rankLen = 10;//
	$GameConfig->currentLen = 30;//
	$GameConfig->dieLen = 10;//
	$GameConfig->historyLen = 20;//
	$GameConfig->propLogLen = 20;//
	
	function createProp($quality){//��λΪtype,��λƷ��
		$type = numToStr(1);
		$quality = numToStr($quality);
		return $type.$quality.'#'.base64_encode('Prop'.rand(1,999));
	}
	
	//ȡ��������
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
	
	function encodeAction($oo){
		return $oo->t."@".$oo->id."@".$oo->f."@".$oo->ty."@".$oo->r;
	}
	
	function decodeAction($str){
		$temp = explode("@",$str);
		
		$action = new stdClass();
		$action->id = (int)$temp[1];
		$action->t = (int)$temp[0];
		$action->f = (int)$temp[2];
		$action->ty = (int)$temp[3];
		$action->r = $temp[4];
		return $action;
	}
	
	
?> 