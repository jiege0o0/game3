<?php 
	require_once($filePath."cache/base.php");
	//��ǰ�ȼ��£�������λ���ļ��
	function getPropCD($clv,$slv,$tlv){
		$hourEarn = ($clv-$slv + 1)*(1 + $tlv*5/100);
		if($hourEarn <= 0)
			return 0;
		return 3600/$hourEarn;
	}


	do{
		$sql = "select * from ".getSQLTable('shop')." where gameid='".$userData->gameid."'";
		$result = $conne->getRowsRst($sql);
		if($result && isSameDate($result['time']))
		{
			$returnData->shop = json_decode($result['shop']);
			break;
		}

		//{id,num,diamond},
		//����shop����	
		$arr = array();
		$level = $userData->hang->level;
		foreach($prop_base as $key=>$value)
		{
			if($value['hanglevel'] && $value['hanglevel']<=$level)//��Դ����
			{
				$propCD = getPropCD($level,$value['hanglevel'],$userData->getTecLevel(300 + $key));
				array_push($arr,array(
					'id'=>$key,
					'num'=>round(24*3600/$propCD),
					'diamond'=>60
				));
			}
			else if($value['diamond'] && $key != 101)
			{
				//������...
				$num = rand(1,5);
				array_push($arr,array(
					'id'=>$key,
					'num'=>$num,
					'diamond'=>$num * $value['diamond']
				));
			}
		}
	
				
		//Ǯ
		$coinCD = 3600/(90+$level*10 + floor($level/5)*20);
		array_push($arr,array(
					'id'=>'coin',
					'num'=>round(24*3600/$coinCD),
					'diamond'=>60
				));
		//������
		if($level >= 10 && $userData->getPropNum(101) == 0)
		{
			array_push($arr,array(
					'id'=>101,
					'num'=>1,
					'diamond'=>$prop_base[101]['diamond']
				));
		}

		
		if(count($arr) > 5)//���ȡ6��
		{
			usort($arr,randomSortFun);
			$arr = array_slice($arr,0,5);
		}
		//����(����)
		$num = rand(10,20);
		array_push($arr,array(
					'id'=>'energy',
					'num'=>$num,
					'diamond'=>$num*5
				));
		
		$returnData->shop = $arr;
		if($result)
			$sql = "update ".getSQLTable('shop')." set shop='".json_encode($arr)."',time=".time()." where gameid='".$userData->gameid."'";
		else
			$sql = "insert into ".getSQLTable('shop')."(gameid,shop,time) values('".$userData->gameid."','".json_encode($arr)."',".time().")";
		$conne->uidRst($sql);

		
		
	}while(false);
	
?> 