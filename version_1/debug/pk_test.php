<?php 
$id=$msg->id;
$hangIndex=$userData->hang->level + 1;
$mapIndex = ceil($hangIndex/100);
require_once($filePath."pk/pk_tool.php");
require_once($filePath."cache/map".$mapIndex.".php");


do{		
	if(!$userData->testEnergy(1))//û����
	{
		$returnData -> fail = 1;
		break;
	}
	
	foreach($userData->atk_list->list as $key=>$value)
	{
		if($value->id == $id)
		{
			$list = $value->list;
			break;
		}
	}

	if(!$list)
	{
		$returnData -> fail = 2;
		break;
	}
	
	$team2Data = array("id"=>1,"list"=>$list,"force"=>1,"type"=>1,"hp"=>3,"award"=>"");
	
	$pkData = new stdClass();
	$pkData->seed = time();
	$pkData->players = array();
	array_push($pkData->players,createUserPlayer(1,1,$userData,$list));
	array_push($pkData->players,createNpcPlayer(2,2,$team2Data));
	
	$returnData -> pkdata = $pkData;
	// $userData->addEnergy(-1);
	// $userData->pk_common->pktype = 'hang';
	// $userData->pk_common->pkdata = $pkData;
	// $userData->pk_common->level = $hangIndex;
	// $userData->pk_common->time = time();
	// $userData->setChangeKey('pk_common');

}while(false)


?> 