<?php 
	require_once($filePath."game/game_tool.php");
	$url  = $dataFilePath.'user_world/server'.$serverID.'/'.$msg->gameid.'.txt';
	$myData = json_decode(file_get_contents($url));
	$returnData->list = $myData->rank;
	$returnData->role = getRoleBase($myData->rank,$myData->role);
?> 