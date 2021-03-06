<?php	
class GameUser{

	public $gameid;
	public $uid;
	public $nick;
	public $level;
	public $exp;
	public $tec_force;
	public $last_land;
	public $land_key;
	public $prop;
	public $diamond;
	public $use_prop;
	public $world;
	public $active;
	public $rmb;
	
	
	private $haveSetCoin = false;
	private $changeKey = array();

	//初始化类
	function __construct($data,$openData=null){
		$this->gameid = $data['gameid'];
		$this->uid = $data['uid'];
		$this->nick = $data['nick'];
		$this->level = (int)$data['level'];
		$this->tec_force = (int)$data['tec_force'];
		$this->last_land = $data['last_land'];
		
		
		if($openData == null)
			return;
		
		$this->rmb = (int)$data['rmb'];
		$this->diamond = (int)$data['diamond'];
		$this->land_key = (int)$data['land_key'];
		$this->prop = $this->decode($data['prop'],'{"list":[],"remove":[]}');//已移除的
		$this->use_prop = $this->decode($data['use_prop']);
		$this->world = $this->decode($data['world']);
		$this->active = $this->decode($data['active'],'{"task":{}}');//活动		
	}
	
	function decode($v,$default = null){
		if(!$v)
		{
			if($default)
				$v = $default;
			else
				$v = '{}';
		}
		return json_decode($v);
	}
	
	function addTaskStat($key){
		global $returnData;
		if(!$this->active->task->stat)
			$this->active->task->stat = new stdClass();
		if(!$this->active->task->stat->{$key})
		{
			$this->active->task->stat->{$key} = 1;
			$this->setChangeKey('active');
			if(!$returnData->sync_task)
				$returnData->sync_task = array();
			$returnData->sync_task['stat'] = $this->active->task->stat;
		}
	}

	
	function setChangeKey($key){
		$this->changeKey[$key] = 1;
	}
	function setOpenDataChange(){
		$this->openDataChange = true;
	}
	
	
	
	//==============================================   end
	
	function addDiamond($v){
		if(!$v)
			return;
		global $returnData;
		$this->diamond += $v;
		$this->setChangeKey('diamond');
		$returnData->sync_diamond = $this->diamond;
	}

	
	//取道具数量
	function getPropNum($propID){
		if($this->prop->{$propID})
			return $this->prop->{$propID};
		return 0;
	}
	
	//改变道具数量
	function addProp($propID){
		array_push($this->prop->list,$propID);
		$this->setChangeKey('prop');	

	}
	
	function removeProp($propID,$time){
		global $returnData;
		array_push($this->prop->remove,$propID.'@'.$time);
		$index = array_search($propID,$this->prop->list);
		if($index === 0 || $index>0)
		{
			array_splice($this->prop->list,$index,1);
		}
		$this->setChangeKey('prop');

		if(!$returnData->remove_prop)
			$returnData->remove_prop = array();
		array_push($returnData->remove_prop,$propID.'@'.$time);
	}
	


	
	//把结果写回数据库
	function write2DB($fromLogin = false){
		//return false;
		function addKey($key,$value,$needEncode=false){
			if($needEncode)
				return $key."='".json_encode($value)."'";
			else 
				return $key."=".$value;
		}
		
		global $conne,$msg,$mySendData,$sql_table,$returnData;
		
		if(!$fromLogin)
		{
			$returnData->sync_opendata = $this->openData;
		}
		
		$arr = array();
		
		if($this->changeKey['rmb'])
			array_push($arr,addKey('rmb',$this->rmb));
		if($this->changeKey['level'])
			array_push($arr,addKey('level',$this->level));
		if($this->changeKey['tec_force'])
			array_push($arr,addKey('tec_force',$this->tec_force));
		if($this->changeKey['diamond'])
			array_push($arr,addKey('diamond',$this->diamond));
		if($this->changeKey['exp'])
			array_push($arr,addKey('exp',$this->exp));
			
		if($this->changeKey['use_prop'])
			array_push($arr,addKey('use_prop',$this->use_prop,true));
		if($this->changeKey['prop'])
			array_push($arr,addKey('prop',$this->prop,true));
		if($this->changeKey['active'])
			array_push($arr,addKey('active',$this->active,true));
		if($this->changeKey['world'])
			array_push($arr,addKey('world',$this->world,true));	
				
			
			
		if(count($arr) > 0)
		{
			array_push($arr,addKey('last_land',time()));	
			$sql = "update ".getSQLTable('user_data')." set ".join(",",$arr)." where gameid='".$this->gameid."'";
			 //debug($sql);
			if(!$conne->uidRst($sql))//写用户数据失败
			{
				$mySendData->error = 4;
				return false;
			}
		}		
		
		if($this->openDataChange)
		{
			$arr = array();
			if($this->changeKey['masterstep'])
				array_push($arr,addKey('masterstep',"'".$this->openData['masterstep']."'"));
			if($this->changeKey['mailtime'])
				array_push($arr,addKey('mailtime',$this->openData['mailtime']));
				
			if(count($arr))
			{
				$sql = "update ".getSQLTable('user_open')." set ".join(",",$arr)." where gameid='".$this->gameid."'";
				// debug($sql);
				if(!$conne->uidRst($sql))//写用户数据失败
				{
					$mySendData->error = 4;
					return false;
				}
			}
			
		}
		$this->changeKey = array();
		return true;
			
	}
}

//获取其它玩家的数据
function getUser($gameid){
	global $conne;
	$sql = "select * from ".$sql_table."user_data where id='".$gameid."'";
	$result = $conne->getRowsRst($sql);
	if($result)
		return new GameUser($result);
	return null;
}
?>