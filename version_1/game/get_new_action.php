<?php 
	require_once($filePath."game/tool/run_game.php");
		
	$returnData->action = $newAction;
	$returnData->lasttime = $userData->world->lasttime;
	if($newRole && count($newRole)>0)	
		$returnData->role = getRoleBase($newRole,$myData->role);

?> 