<?php
require_once($filePath."game/tool/game_tool.php");
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
			$oo = decodeAction($value);
			array_push($historyActionRole,$oo->id);
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
	
	
	require_once($filePath."game/tool/game_action.php");
	
	if(!$myData)
	{	
		$myData = new stdClass();
		$myData->world = array();
		
		for($i=0;$i<20;$i++)
		{
			array_push($myData->world,createPlace());
		}
		
		$myData->current = array();//当前数据
		$myData->die = array();//当前数据
		$myData->rank = array();//排行
		$myData->action = array();//待执行
		$myData->historyAction = array();//最近日志
		$myData->role = new stdClass();//角色数据
		$myData->refreshTime = time();//要真实时间
		$myData->dealTime = time();//要真实时间
		$myData->propLog = array();//道具日志
	}
	
	
	
	$aliveNum = 0;//存在的角色数量
	$aliveArr = array();
	$dieArr = array();
	foreach($myData->current as $key=>$value)
	{
		if($myData->role->{$value}->d >= 0)
			array_push($dieArr,$value);
		else
		{
			array_push($aliveArr,$value);
			$aliveNum ++;
		}
	}
	
	$newRole = array();
	$newAction = array();
	//新增数据
	for($i=0;$i<$cd;$i+=rand(50,80))
	{
		$roleTime = $userData->world->lasttime + $i;
		if($aliveNum < $GameConfig->currentLen)//创建
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

			$action = createAction($role,$roleTime,$myData->world);
			array_push($myData->action,$action);
			array_push($newAction,$action);
			if($role->d >= 0)
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
	$rankLen = $GameConfig->rankLen;
	$newDie = array();
	$rankForce = 0;
	if(count($myData->rank) >= $rankLen)
	{
		$id = $myData->rank[$rankLen-1];
		$rankForce = $myData->role->{$id}->f; 
	}	
	
	while(true)
	{
		if(!$myData->action[0])
			break;
		$id = $myData->action[0]->id;
		$role = $myData->role->{$id};
		if($myData->action[0]->t + $role->b > $currentTime)
			break;
		$oo = array_shift($myData->action);
		$role->f += $oo->f;
		$action = encodeAction($oo);
		if($oo->ty == 2)//死了
		{
			$role->d = $oo->t;
			array_push($newDie,$id);
			array_unshift($myData->die,$id);
			
			$index = array_search($id, $myData->current);
			array_splice($myData->current,$index,1);
			
			if($role->f > $rankForce)
				array_push($myData->rank,$id);
		}
		else if($oo->ty == 3)//用道具,记日志
		{
			// $head = numToStr($role->h);
			// if(strlen($head) == 1)
				// $head = '0'.$head;
			array_unshift($myData->propLog,$action);//.'@'.$head.$role->g.$role->n
		}
		
		array_unshift($role->a,$action);
		array_unshift($myData->historyAction,$action);
		array_unshift($historyActionRole,$id);
	}
	
	//记录10条最近死亡
	if(count($myData->die) > $GameConfig->dieLen)
	{
		$myData->die = array_slice($myData->die,0,$GameConfig->dieLen);
	}
	
	//记录20条用于首页显示
	if(count($myData->historyAction) > $GameConfig->historyLen)
	{
		$myData->historyAction = array_slice($myData->historyAction,0,$GameConfig->historyLen);
		$historyActionRole = array_slice($historyActionRole,0,$GameConfig->historyLen);
	}
	
	if(count($myData->propLog) > $GameConfig->propLogLen)
	{
		$myData->propLog = array_slice($myData->propLog,0,$GameConfig->propLogLen);
	}
	
	
	if(count($myData->rank) >= $rankLen)
	{
		//重新排序
		$arr = array();
		foreach($myData->rank as $key=>$value)
		{
			array_push($arr,$myData->role->{$value});
		}
		usort($arr,"my_rank_sort");
		$myData->rank = array();
		foreach($arr as $key=>$value)
		{
			if($key >=$rankLen)
				break;
			array_push($myData->rank ,$value->id);
		}
	}
	
	//移除已生效的道具
	$arr = array();
	$b = false;
	foreach($userData->prop->remove as $key=>$value)
	{
		$temp = explode("@",$value);
		if((int)($temp[1]) > $currentTime)
			array_push($arr,$value);
		else
			$b = true;
	}
	if($b)
	{
		$userData->prop->remove = $arr;
		$userData->setChangeKey('prop');
	}
	
	
	
	//清理过期数据
	if(time() - $myData->dealTime > 3*3600)
	{
		$myData->dealTime = time();
		$ownData = new stdClass();
		$removeArr = array();
		
		
		//过期角色
		$len = count($myData->rank);//保留排行榜角色数据
		for($i=$len-1;$i>=0;$i--)
		{
			$ownData->{$myData->rank[$i]} = true;
		}
		
		$len = count($myData->die);//保留最近死亡角色数据
		for($i=$len-1;$i>=0;$i--)
		{
			$ownData->{$myData->die[$i]} = true;
		}

		$len = count($historyActionRole);//保留最近日志角色数据
		for($i=$len-1;$i>=0;$i--)
		{
			$ownData->{$historyActionRole[$i]} = true;
		}
		
		$len = count($myData->propLog);//保留道具日志角色数据
		for($i=$len-1;$i>=0;$i--)
		{
			$oo = decodeAction($myData->propLog[$i]);
			$ownData->{$oo->id} = true;
		}
		
		
		
		foreach($myData->role as $key=>$value)
		{
			if($value->d > 100 && !$ownData->{$key})
			{
				array_push($removeArr,$key);
			}
		}
		
		foreach($removeArr as $key=>$value)
		{
			unset($myData->role->{$value});
		}
		
		//过期地方
		if(time() - $myData->refreshTime > 24*3600)
		{
			$myData->refreshTime = time();
			array_shift($myData->world);
			$maxWorld = 18 + 2*$userData->level;
			while(count($myData->world) < $maxWorld)
			array_push($myData->world,createPlace());
		}
		
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