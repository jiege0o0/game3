<?php 
	$GameConfig = new stdClass();
	$GameConfig->year = 30*60;//1��ĳ��ȣ���λ��
	
	
	//ȡ��������
	function getRoleBase($ids,&$roleData){
		$obj = new stdClass();
		foreach($roleData as $key=>$value)
		{
			if($obj->{$key})
				continue;
			$obj->{$key} = new stdClass();
			$obj->{$key}->f = $value->f;
			$obj->{$key}->g = $value->g;
			$obj->{$key}->n = $value->n;
			$obj->{$key}->b = $value->b;
			$obj->{$key}->d = $value->d;
		}
		return $obj;
	}
	
	
?> 