<?php 
	//把数字变成1位的字符(最大值为9+26+26 = 61)
	function numToStr($num){
		if(!$num)
			return '0';
		$str = '';
		while($num)
		{
			$str = _numToStr($num%62).$str;
			$num = (int)($num/62);
		}
		return $str;
	}
	
	function _numToStr($num){
		if($num<10)
			return chr(48 + $num);
		$num -= 10;
		if($num<26)	
			return chr(65 + $num);
		$num -= 26;
		return chr(97 + $num);
	}
	
	//把1位的字符变成数字(最大值为9+26+26 = 61)
	function strToNum($str){
		$num = 0;
		$arr = str_split($str);
		$len = count($arr);
		for($i=0;$i<$len;$i++)
		{
			$num += pow(62,$len-$i-1)*_strToNum($arr[$i]);
		}
		return $num;
	}
	
	function _strToNum($str){
		$num = ord($str);
		if($num >= 97)
			return $num - 97 +26 + 10;
		if($num >= 65)
			return $num - 65 + 10;
		return $num - 48;
	}
	// echo rand(9,10.256);
		//echo pow(61,0);
		 echo strToNum('zz');
?> 