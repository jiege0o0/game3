<?php 
	require_once($filePath."game/run_game.php");
		
	$returnData->action = $newAction;
	if($newRole && count($newRole)>0)	
		$returnData->role = getRoleBase($newRole,$myData->role);

?> 