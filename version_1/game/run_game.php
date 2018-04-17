<?php 
do{
	if(!$userData->world->begintime)
	{
		$userData->world->begintime = time() - 24*3600;
		$userData->world->lasttime = 12*3600;
		$userData->world->icetime = 0;
		$userData->world->roleid = 1;
		$userData->world->force = rand(6,8);
	}
	$currentTime = (time() - $userData->world->begintime)- $userData->world->icetime;
	$toTime = $currentTime + 3600;//���ɵ�������ʱ��
	$cd = $toTime - $userData->world->lasttime;
	if($cd < 10*60)//10�����ڲ�����
		break;
	if($cd > 3600*24)//����1��Ľ��붳��ʱ��
	{
		$iceCD = $cd - 3600*24;
		$userData->world->icetime += $iceCD;
		$toTime -= $iceCD;
		$currentTime -= $iceCD;
		$cd = 3600*24;
	}
	require_once($filePath."game/game_tool.php");
	$myfile = fopen("webdictionary.txt", "w+");
	$myData = fread($myfile,filesize("webdictionary.txt"));
	if(!$myData)
	{
		$myData = new stdClass();
		$myData->world = array();
		
		for($i=0;$i<20;$i++)
		{
			array_push($myData->world,createPlace());
		}
		
		$myData->current = array();//��ǰ����
		$myData->rank = array();//����
		$myData->action = array();//��ִ��
		$myData->historyAction = array();//�����־
		$myData->role = new stdClass();//��ɫ����
		$myData->cleanTime = time();//Ҫ��ʵʱ��
	}
	
	
	$aliveNum = 0;//���ڵĽ�ɫ����
	$aliveArr = array();
	$dieArr = array();
	foreach($myData->current as $key=>$value)
	{
		if($myData->role[$value]->d)
			array_push($dieArr,$value);
		else
		{
			array_push($aliveArr,$value);
			$aliveNum ++;
		}
	}
	
	//��������
	for($i=0;i<$cd;i+=rand(50,80))
	{
		$roleTime = $userData->world->lasttime + $i;
		if($aliveNum < 30)//����
		{
			$role = createRole($userData->world->roleid,$roleTime);
			array_push($aliveArr,$userData->world->roleid);
			$myData->role->{$userData->world->roleid} = $role
			$userData->world->roleid ++;
			$aliveNum ++;
		}
		else
		{
			$index = rand(0,9);
			$id = $aliveArr[$index];
			$role = $myData->role->{$id};
			array_splice($aliveArr,$index,1);

			$action = createAction($role,$myData->world,$roleTime);
			array_push($myData->action,$action);
			if($role->d)
			{
				$aliveNum --;
				array_push($dieArr,$id);
			}
			else
			{
				array_push($aliveArr,$id);
			}
		}
	}
	$userData->world->lasttime = $toTime; 
	$myData->current = array_merge($aliveArr,$dieArr);
	
	//����������
	$rankLen = 20;
	$newDie = array();
	$rankForce = 0;
	if(count($myData->rank) >= $rankLen)
	{
		$id = $myData->rank[$rankLen-1];
		$rankForce = $myData->role->{$id}->f; 
	}
	while(true)
	{
		if(!$myData->action[0] || $myData->action[0]->t > $currentTime)
			break;
		$oo = array_shift($myData->action);
		$id = $oo->id;
		$role = $myData->role->{$id};
		$role->f += $oo->f;
		if($role->ty == 1)//����
		{
			$role->d = $oo->t;
			array_push($newDie,$id);
			
			$index = array_search($id, $myData->current);
			array_splice($myData->current,$index,1);
			
			if($role->f > $rankForce)
				array_push($myData->rank,$id);
		}
		array_push($role->a,$oo);
		array_unshift($myData->historyAction,$oo);
	}
	
	//��¼20��������ҳ��ʾ
	if(count($myData->historyAction) > 20)
	{
		$myData->historyAction = array_slice($myData->historyAction,0,20);
	}
	
	
	if(count($myData->rank) > $rankLen)
	{
		//��������
		$arr = array();
		foreach($myData->rank as $key=>$value)
		{
			array_push($arr,$myData->role->{$value});
		}
		usort($arr,"my_rank_sort");
		$myData->rank = array();
		foreach($myData->rank as $key=>$value)
		{
			if($key >=$rankLen)
				break;
			array_push($myData->rank ,$value->id);
		}
	}
	
	
	
	//�����������
	if(time() - $myData->cleanTime > 48*3600)
	{
		//���ڽ�ɫ
		$myData->cleanTime = time();
		$cleanDieTime = $currentTime - 36*3600;
		$ownData = new stdClass();
		$removeArr = array();
		
		$len = count($myData->rank);
		for($i=$len-1;$i>=0;$i--)
		{
			$ownData->{$myData->rank[$i]} = true;
		}
		
		foreach($myData->role as $key=>$value)
		{
			if($value->d > 100 && $value->d < $cleanDieTime && !$ownData->{$key})
			{
				array_push($removeArr,$key);
			}
		}
		
		foreach($removeArr as $key=>$value)
		{
			unset($myData->role->{$value});
		}
		
		//���ڵط�
		array_shift($myData->world);
		$maxWorld = 18 + 2*$userData->level;
		while(count($myData->world) < $maxWorld)
			array_push($myData->world,createPlace());
	}
	
	
	
}while(false)

function my_rank_sort($a,$b)
{
	if ($a->f > $b->f)
		return -1;
	if ($a->f < $b->f)
		return 1;
	return 0;
}
?> 










