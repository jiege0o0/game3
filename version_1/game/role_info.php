<?php 
	$id = $msg->id;
	require_once($filePath."game/game_tool.php");
	$url  = $dataFilePath.'user_world/server'.$serverID.'/'.$msg->gameid.'.txt';
	$myData = json_decode(file_get_contents($url));
	debug($id);
	debug($myData->role->{$id});
	$returnData->role = $myData->role->{$id};
	if($returnData->role->d < 100)//δ����Ҫ����action����û��Ҫ�����
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