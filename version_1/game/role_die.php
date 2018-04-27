<?php 
	require_once($filePath."game/tool/game_tool.php");
	$url  = $dataFilePath.'user_world/server'.$serverID.'/'.$msg->gameid.'.txt';
	$myData = json_decode(file_get_contents($url));
	$returnData->list = $myData->die;
	$returnData->role = getRoleBase($myData->die,$myData->role);
?> 