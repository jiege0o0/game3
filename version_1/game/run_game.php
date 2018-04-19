<?php
require_once($filePath."game/game_tool.php");
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
	$toTime = $currentTime + 30*60;//生成到的世界时间
	$cd = $toTime - $userData->world->lasttime;
	$historyActionRole = array();
	
	$url  = $dataFilePath.'user_world/server'.$serverID.'/'.$userData->gameid.'.txt';
	
	if(file_exists($url))
		$myData = json_decode(file_get_contents($url));
		
	if($myData && $cd < 10*60)//10分钟内不处理
	{
		foreach($myData->historyAction as $key=>$value)
		{
			array_push($historyActionRole,$value->id);
		}
		break;
	}
		
	if($cd > 3600*24)//超过1天的进入冻结时间
	{
		$iceCD = $cd - 3600*24;
		$userData->world->icetime += $iceCD;
		$toTime -= $iceCD;
		$currentTime -= $iceCD;
		$cd = 3600*24;
	}
	
	
	require_once($filePath."game/game_action.php");
	
	if(!$myData)
	{	
		$myData = new stdClass();
		$myData->world = array();
		
		for($i=0;$i<20;$i++)
		{
			array_push($myData->world,createPlace());
		}
		
		$myData->current = array();//当前数据
		$myData->rank = array();//排行
		$myData->action = array();//待执行
		$myData->historyAction = array();//最近日志
		$myData->role = new stdClass();//角色数据
		$myData->cleanTime = time();//要真实时间
	}
	
	
	
	$aliveNum = 0;//存在的角色数量
	$aliveArr = array();
	$dieArr = array();
	foreach($myData->current as $key=>$value)
	{
		if($myData->role->{$value}->d)
			array_push($dieArr,$value);
		else
		{
			array_push($aliveArr,$value);
			$aliveNum ++;
		}
	}
	
	$newRole = array();
	//新增数据
	for($i=0;$i<$cd;$i+=rand(50,80))
	{
		$roleTime = $userData->world->lasttime + $i;
		if($aliveNum < 30)//创建
		{
			$role = createRole($userData->world->roleid,$roleTime);
			array_push($aliveArr,$userData->world->roleid);
			array_push($newRole,$userData->world->roleid);
			$myData->role->{$userData->world->roleid} = $role;
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
	
	//处理动作数据
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
		if($role->ty == 1)//死了
		{
			$role->d = $oo->t;
			array_push($newDie,$id);
			
			$index = array_search($id, $myData->current);
			array_splice($myData->current,$index,1);
			
			if($role->f > $rankForce)
				array_push($myData->rank,$id);
		}
		array_unshift($role->a,$oo);
		array_unshift($myData->historyAction,$oo);
		array_unshift($historyActionRole,$id);
	}
	
	
	
	//记录20条用于首页显示
	if(count($myData->historyAction) > 20)
	{
		$myData->historyAction = array_slice($myData->historyAction,0,20);
	}
	
	
	if(count($myData->rank) > $rankLen)
	{
		//重新排序
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
	
	
	
	//清理过期数据
	if(time() - $myData->cleanTime > 48*3600)
	{
		//过期角色
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
		
		//过期地方
		array_shift($myData->world);
		$maxWorld = 18 + 2*$userData->level;
		while(count($myData->world) < $maxWorld)
			array_push($myData->world,createPlace());
	}
	
	
	$myfile = fopen($url, "w+");
	fwrite($myfile, json_encode($myData));
	fclose($myfile);
	
	$writeDB = true;
	$userData->setChangeKey('world');
	
}while(false);

//排行榜排序
function my_rank_sort($a,$b)
{
	if ($a->f > $b->f)
		return -1;
	if ($a->f < $b->f)
		return 1;
	return 0;
}


?> 