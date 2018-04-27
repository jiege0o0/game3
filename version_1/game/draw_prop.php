<?php 
	$num = $msg->num;
	$cost = $num * 100;
	$isFree = true;
	do{
		if(!$isFree)
		{
			if($userData->diamond < $cost)
			{
				$returnData -> fail = 1;
				$returnData->sync_diamond = $userData->diamond;
				break;
			}
			$userData->addDiamond(-$cost);
		}
		require_once($filePath."game/tool/game_tool.php");
		$temp = $num==1?9:7;
		$arr = array();
		while($num--)
		{	
			$min = 1;
			$max = $min + 2;
			
			while(!$isFree && rand(0,$temp) == 0)
				$max ++;
			$q = rand($min,$max);
			$prop = createProp($q);
			array_push($arr,$prop);
			$userData->addProp($prop);
		}
		$returnData->prop = $arr;
	}while(false)
?> 