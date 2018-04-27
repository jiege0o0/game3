<?php 
	function createPlace(){//��λΪtype
		$type = numToStr(1);
		return $type.'#'.base64_encode('Place'.rand(1,999));
	}
	
	function createName(){//
		return base64_encode('name'.rand(1,999));
	}

	//�½�һ����ɫ
	function createRole($id,$time){
		global $GameConfig,$userData; 
		$role = new stdClass();
		$role->id = $id;
		$role->g = rand(1,2);//gender
		$role->n = createName($role->g);//nick
		$role->h = rand(1,400);//head
		$role->b = $time - rand(5*$GameConfig->year,10*$GameConfig->year);//born
		$role->f = (int)($userData->world->force*rand(80,120)/100);//force
		$role->a = array();//action
		$role->t = $time;//lastTime
		$min = (22 + rand(0,$userData->level*2))*$GameConfig->year;
		$max = (60 + $userData->level*2)*$GameConfig->year;
		if($min > $max)
			$max = $min + $GameConfig->year;
		$role->d = -rand($min,$max);//dieTime
		
		//�����¼�
		$action = createAction($role,$role->t);
		array_push($role->a,$action);
		
		return $role;
	}
	
	//�½�һ����Ϊ
	function createAction(&$role,$time,&$world=null){
		global $GameConfig,$userData; 
		$remark = array();
		
		$action = new stdClass();
		$action->id = $role->id;
		$action->t = $time;
		$action->f = (int)($userData->world->force*rand(3,5)/10*(($time - $role->t)/$GameConfig->year));//ÿ����Ȼ�����ٶ�
		$action->ty = 0;//type 0��ͨ 1������2����
		
		if($world)
		{
			$place = $world[rand(0,count($world)-1)];
			array_push($remark,$place);//�ص�
		}
		else
		{
			array_push($remark,rand(0,999));//�ص�
		}
		
		
		do{
			//---------------------------------------���� 0
			if(count($role->a) == 0)
			{
				$action->f = 0;
				$action->ty = 1;
				array_push($remark,numToStr(0));
				break;
			}
			
			//---------------------------------------���� 2
			$isDie = $time > -$role->d + $role->b;
			if($isDie)
			{
				debug((int)(($time - $role->b)/$GameConfig->year));
				$action->f = 0;
				$action->ty = 2;
				$role->d = 0;
				array_push($remark,numToStr(2));//type
				break;
			}
		
			//---------------------------------------���ֵ��� 1
			$len = count($userData->prop->list);
			if(rand(1,10) <= 3 && rand(0,$len) >= rand(0,10))
			{
				$propTime = rand(0,$len-1);
				$prop = $userData->prop->list[$propTime];
				if($prop)
				{
					$type = strToNum(substr($prop,0,1));
					$quality = strToNum(substr($prop,1,1));
					$action->f += (int)(pow($quality+3,1.5)*(1+rand(-10,10)/100));
					array_push($remark,numToStr(1));//type
					array_push($remark,$userData->prop->list[$propTime]);
					
					$userData->removeProp($userData->prop->list[$propTime],$time);
					break;
				}
			}
			
			
			
			
			
			//---------------------------------------ʲô��û���� 61
			array_push($remark,61);
			break;
		}while(false);
		
		
		array_push($remark,numToStr(rand(0,916132831)));//���5λ�ĸ��Ӳ���
	
		
		
		$action->r = implode(',',$remark);
		
		return $action;
	}
	
	
?> 