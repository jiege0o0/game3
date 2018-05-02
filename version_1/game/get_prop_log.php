<?php 
	require_once($filePath."game/tool/game_tool.php");
	$url  = $dataFilePath.'user_world/server'.$serverID.'/'.$msg->gameid.'.txt';
	$myData = json_decode(file_get_contents($url));
	$returnData->list = $myData->propLog;
	
	$roleList = array();
	$len = count($myData->propLog);
	for($i=$len-1;$i>=0;$i--)
	{
		$oo = decodeAction($myData->propLog[$i]);
		array_push($roleList,$oo->id);
	}
	
	$returnData->role = getRoleBase($roleList,$myData->role);
?> 