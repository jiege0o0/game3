<?php 
	function createPlace(){//首位为type
		$type = 1;
		return $type.'#'.base64_encode('Place'.rand(1,999));
	}

	function createProp($quality=-1){//首位为type,次位品质
		$quality = 1;
		$type = 1;
		return $type.$quality.'#'.base64_encode('Prop'.rand(1,999));
	}
	
	function createName(){//
		return base64_encode('name'.rand(1,999));
	}

	//新建一个角色
	function createRole($id,$time){
		global $GameConfig,$userData; 
		$role = new stdClass();
		$role->id = $id;
		$role->g = rand(1,2);//gender
		$role->n = createName($role->g);//nick
		$role->s = rand(1,300);//show
		$role->b = $time - rand(5*$GameConfig->year,10*$GameConfig->year);//born
		$role->f = (int)$userData->world->force*rand(80,120)/100;//force
		$role->a = array();//action
		$role->t = $time;//lastTime
		$role->d = 0;//dieTime
		
		//出生事件
		$remark = array();
		array_push($remark,'1');
		array_push($remark,'1'.base64_encode('Place'.rand(1,999)));
		$action = new stdClass();
		$action->t = $role->b;
		$action->ty = 0;
		$action->f = 0;
		$action->r = implode('#',$remark);
		array_push($role->a,$action);
		
		return $role;
	}
	
	//新建一个行为
	function createAction(&$role,&$world,$time){
		$remark = array();
		
		$action = new stdClass();
		$action->id = $role->id;
		$action->t = $time;
		$action->f = rand(1,10);
		$action->ty = 0;//type
		$action->r = implode('#',$remark);
		
		return $action;
	}
	
	
?> 