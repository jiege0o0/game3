<?php 
	$id = $msg->id;
	require_once($filePath."game/game_tool.php");
	$url  = $dataFilePath.'user_world/server'.$serverID.'/'.$userData->gameid.'.txt';
	$myData = json_decode(file_get_contents($url));
	$returnData->role = $myData->role->{$id};
?> 