<?php 
$serverID = $_GET["serverid"];
if(!$serverID)
	die('no serverID');
	
$filePath = dirname(__FILE__).'/';
require_once($filePath."_config.php");


	
	
$connect=mysql_connect($sql_url,$sql_user,$sql_password)or die('message=F,Could not connect: ' . mysql_error()); 
mysql_select_db($sql_db,$connect)or die('Could not select database'); 
mysql_query("set names utf8");

//自己的数据
mysql_query("
Create TABLE g3_".$sql_table."user_data(
gameid varchar(32) NOT NULL Unique Key,
uid INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
nick varchar(30),
tec_force SMALLINT UNSIGNED default 0,
level TINYINT UNSIGNED default 1,
exp INT UNSIGNED default 0,
diamond INT UNSIGNED default 100,
rmb INT UNSIGNED default 100,
prop Text,
use_prop Text,
current_role Text,
history_role Text,
active Text,
land_key varchar(63),
last_land INT UNSIGNED
)",$connect)or die("message=F,Invalid query: " . mysql_error()); 




echo "成功".time();
?>