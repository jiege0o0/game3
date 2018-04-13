<?php
	require_once($filePath."tool/conn.php");
	require_once($filePath."object/game_user.php");
	$gameid = $serverID.'_'.$msg->id;
	$sql = "select * from ".getSQLTable('user_data')." where gameid='".$gameid."'";
	$userData = $conne->getRowsRst($sql);
	
	if($userData)//有这个玩家
	{
		$time = time();
		$sql = "update ".getSQLTable('user_data')." set last_land=".$time.",land_key='".$time."' where gameid='".$gameid."'";
		$conne->uidRst($sql);
		
		
		$writeDB = false;
		$lastLand = $userData['last_land'];
		$userData['last_land'] = $time;
		$userData['land_key'] = $time;
		$userData = new GameUser($userData,true);
		
		
		//用户数据处理
		$addMailAward = false;
		
		if(!isSameDate($lastLand))
		{
			$oo = new stdClass();
			$oo->des = base64_encode('测试期间登录奖励');
			$oo->award = new stdClass();
			$oo->award->diamond = 100;
			$oo = json_encode($oo);
			$sql = "insert into ".getSQLTable('mail')."(from_gameid,to_gameid,type,content,stat,time) values('sys','".$userData->gameid."',101,'".$oo."',0,".$time.")";
			$conne->uidRst($sql);
			
			// $userData->openData['mailtime'] = $time;
			// $userData->setOpenDataChange();
			// $userData->setChangeKey('mailtime');
			
			$addMailAward = true;
			$writeDB = true;
		}
		
		if(!$userData->active->p0 || $userData->active->p0<1520498485)
		{
			$oo = new stdClass();
			$oo->des = base64_encode('系统给你发奖罗~');
			$oo->award = new stdClass();
			$oo->award->coin = 10;
			$oo->award->props = new stdClass();
			$oo->award->props->{1} = 1;
			$oo = json_encode($oo);
			$sql = "insert into ".getSQLTable('mail')."(from_gameid,to_gameid,type,content,stat,time) values('sys','".$userData->gameid."',101,'".$oo."',0,".$time.")";
			$conne->uidRst($sql);
			
			
			// $userData->active->p0 = $time;
			// $userData->openData['mailtime'] = $time;
			// $userData->setOpenDataChange();
			// $userData->setChangeKey('mailtime');
			// $userData->setChangeKey('active');
			
			$addMailAward = true;
			$writeDB = true;
		}
		
		if($writeDB)
		{
			$userData->write2DB(true);
		}
		
		//未开邮件
		if($msg->mailtime)
		{
	
			if($addMailAward)
				$returnData->mailnum = 1;
			else
			{
				$msgtime = max($msg->mailtime,time() - 72*3600);
				$sql = "select * from ".getSQLTable('mail')." where to_gameid='".$userData->gameid."' and type>100 and stat!=1 and time>".$msgtime;
				$result = $conne->getRowsArray($sql);
				debug($sql);
				if($result)
					$returnData->mailnum = count($result);
			}
		}
		
		
		
		//其它数据返回
		$userData->pk_version = $pk_version;
		$returnData->data = $userData;
		$userData->opentime = $serverOpenTime;
		
		$logtime = 1508117174;
		if($msg->logtime < $logtime)
		{
			$returnData->logtext = new stdClass();
			$returnData->logtext->text = 
				'队伍技能增加至50个|'.
				'UI优化';
			$returnData->logtext->time = $logtime;
		}
		
	}
	else//没这个玩家，要新增
	{
		$returnData-> fail = 2;
		$returnData-> stopLog = true;
	}
?> 