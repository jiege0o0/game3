<?php 
	$id = $msg->id;
	require_once($filePath."game/tool/game_tool.php");
	$url  = $dataFilePath.'user_world/server'.$serverID.'/'.$msg->gameid.'.txt';
	$myData = json_decode(file_get_contents($url));
	$returnData->role = getRoleBase(array($id),$myData->role)->{$id};
	$returnData->role->a = $myData->role->{$id}->a;
	if($returnData->role->d < 100)//未死，要看看action里有没有要处理的
	{
		$returnData->role_action = array();
		foreach($myData->action as $key=>$value)
		{
			if($value->id == $id)
			{
				array_push($returnData->role_action,$value);
			}
		}
	}
?> 