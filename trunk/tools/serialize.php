<?
error_reporting( E_ALL );

$serialize_data   = @$_REQUEST['serialize_data'];

//$serialize_data = 'a:31:{i:0;a:5:{s:2:"id";s:1:"1";s:5:"cn_jt";s:6:"北京";s:5:"cn_tw";s:6:"北京";s:2:"en";s:7:"Beijing";s:2:"jp";s:6:"北京";}i:1;a:5:{s:2:"id";s:1:"2";s:5:"cn_jt";s:6:"上海";s:5:"cn_tw";s:6:"上海";s:2:"en";s:8:"Shanghai";s:2:"jp";s:6:"上海";}i:2;a:5:{s:2:"id";s:1:"3";s:5:"cn_jt";s:6:"天津";s:5:"cn_tw";s:6:"天津";s:2:"en";s:7:"Tianjin";s:2:"jp";s:6:"天津";}i:3;a:5:{s:2:"id";s:1:"4";s:5:"cn_jt";s:6:"重庆";s:5:"cn_tw";s:6:"重庆";s:2:"en";s:9:"Chongqing";s:2:"jp";s:6:"重庆";}i:4;a:5:{s:2:"id";s:1:"5";s:5:"cn_jt";s:9:"内蒙古";s:5:"cn_tw";s:9:"内蒙古";s:2:"en";s:9:"Neimenggu";s:2:"jp";s:9:"内蒙古";}i:5;a:5:{s:2:"id";s:1:"6";s:5:"cn_jt";s:6:"辽宁";s:5:"cn_tw";s:6:"辽宁";s:2:"en";s:8:"Liaoning";s:2:"jp";s:6:"辽宁";}i:6;a:5:{s:2:"id";s:1:"7";s:5:"cn_jt";s:6:"吉林";s:5:"cn_tw";s:6:"吉林";s:2:"en";s:6:"Jinlin";s:2:"jp";s:6:"吉林";}i:7;a:5:{s:2:"id";s:1:"8";s:5:"cn_jt";s:9:"黑龙江";s:5:"cn_tw";s:9:"黑龙江";s:2:"en";s:12:"Heilongjiang";s:2:"jp";s:9:"黑龙江";}i:8;a:5:{s:2:"id";s:1:"9";s:5:"cn_jt";s:6:"江苏";s:5:"cn_tw";s:6:"江苏";s:2:"en";s:7:"Jiangsu";s:2:"jp";s:6:"江苏";}i:9;a:5:{s:2:"id";s:2:"10";s:5:"cn_jt";s:6:"安徽";s:5:"cn_tw";s:6:"安徽";s:2:"en";s:5:"Anhui";s:2:"jp";s:6:"安徽";}i:10;a:5:{s:2:"id";s:2:"11";s:5:"cn_jt";s:6:"福建";s:5:"cn_tw";s:6:"福建";s:2:"en";s:6:"Fujian";s:2:"jp";s:6:"福建";}i:11;a:5:{s:2:"id";s:2:"12";s:5:"cn_jt";s:6:"江西";s:5:"cn_tw";s:6:"江西";s:2:"en";s:7:"Jiangxi";s:2:"jp";s:6:"江西";}i:12;a:5:{s:2:"id";s:2:"13";s:5:"cn_jt";s:6:"山东";s:5:"cn_tw";s:6:"山东";s:2:"en";s:8:"Shandong";s:2:"jp";s:6:"山东";}i:13;a:5:{s:2:"id";s:2:"14";s:5:"cn_jt";s:6:"河南";s:5:"cn_tw";s:6:"河南";s:2:"en";s:5:"Henan";s:2:"jp";s:6:"河南";}i:14;a:5:{s:2:"id";s:2:"15";s:5:"cn_jt";s:6:"湖北";s:5:"cn_tw";s:6:"湖北";s:2:"en";s:5:"Hubei";s:2:"jp";s:6:"湖北";}i:15;a:5:{s:2:"id";s:2:"16";s:5:"cn_jt";s:6:"湖南";s:5:"cn_tw";s:6:"湖南";s:2:"en";s:5:"Hunan";s:2:"jp";s:6:"湖南";}i:16;a:5:{s:2:"id";s:2:"17";s:5:"cn_jt";s:6:"广东";s:5:"cn_tw";s:6:"广东";s:2:"en";s:9:"Guangdong";s:2:"jp";s:6:"广东";}i:17;a:5:{s:2:"id";s:2:"18";s:5:"cn_jt";s:6:"广西";s:5:"cn_tw";s:6:"广西";s:2:"en";s:7:"Guangxi";s:2:"jp";s:6:"广西";}i:18;a:5:{s:2:"id";s:2:"19";s:5:"cn_jt";s:6:"海南";s:5:"cn_tw";s:6:"海南";s:2:"en";s:6:"Hainan";s:2:"jp";s:6:"海南";}i:19;a:5:{s:2:"id";s:2:"20";s:5:"cn_jt";s:6:"四川";s:5:"cn_tw";s:6:"四川";s:2:"en";s:7:"Sichuan";s:2:"jp";s:6:"四川";}i:20;a:5:{s:2:"id";s:2:"21";s:5:"cn_jt";s:6:"贵州";s:5:"cn_tw";s:6:"贵州";s:2:"en";s:7:"Guizhou";s:2:"jp";s:6:"贵州";}i:21;a:5:{s:2:"id";s:2:"22";s:5:"cn_jt";s:6:"云南";s:5:"cn_tw";s:6:"云南";s:2:"en";s:6:"Yunnan";s:2:"jp";s:6:"云南";}i:22;a:5:{s:2:"id";s:2:"23";s:5:"cn_jt";s:6:"西藏";s:5:"cn_tw";s:6:"西藏";s:2:"en";s:6:"Xizang";s:2:"jp";s:6:"西藏";}i:23;a:5:{s:2:"id";s:2:"24";s:5:"cn_jt";s:6:"陕西";s:5:"cn_tw";s:6:"陕西";s:2:"en";s:6:"Shanxi";s:2:"jp";s:6:"陕西";}i:24;a:5:{s:2:"id";s:2:"25";s:5:"cn_jt";s:6:"甘肃";s:5:"cn_tw";s:6:"甘肃";s:2:"en";s:5:"Gansu";s:2:"jp";s:6:"甘肃";}i:25;a:5:{s:2:"id";s:2:"26";s:5:"cn_jt";s:6:"青海";s:5:"cn_tw";s:6:"青海";s:2:"en";s:7:"Qinghai";s:2:"jp";s:6:"青海";}i:26;a:5:{s:2:"id";s:2:"27";s:5:"cn_jt";s:6:"宁夏";s:5:"cn_tw";s:6:"宁夏";s:2:"en";s:7:"Ningxia";s:2:"jp";s:6:"宁夏";}i:27;a:5:{s:2:"id";s:2:"28";s:5:"cn_jt";s:6:"新疆";s:5:"cn_tw";s:6:"新疆";s:2:"en";s:8:"Xinjiang";s:2:"jp";s:6:"新疆";}i:28;a:5:{s:2:"id";s:2:"29";s:5:"cn_jt";s:6:"香港";s:5:"cn_tw";s:6:"香港";s:2:"en";s:8:"Hongkong";s:2:"jp";s:6:"香港";}i:29;a:5:{s:2:"id";s:2:"20";s:5:"cn_jt";s:6:"澳门";s:5:"cn_tw";s:6:"澳门";s:2:"en";s:5:"Macao";s:2:"jp";s:6:"澳门";}i:30;a:5:{s:2:"id";s:2:"31";s:5:"cn_jt";s:6:"台湾";s:5:"cn_tw";s:6:"台湾";s:2:"en";s:6:"Taiwan";s:2:"jp";s:6:"台湾";}}';

$unserialize_data = @$_REQUEST['unserialize_data'];

//print( $unserialize_data );

if( $serialize_data ){
	$unserialize_data = var_export( unserialize( $serialize_data ), true );
	//var_dump( $unserialize_data );
}elseif( $unserialize_data ){
	eval( "\$data = ".$unserialize_data.";" );
	$serialize_data = serialize( $data );
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<form method="POST">
serialize_data:<br/>
<textarea name="serialize_data" id="serialize_data" cols="80" rows="10"><?=htmlspecialchars( $serialize_data )?></textarea>
<br/>
unserialize_data:<br/>
<textarea name="unserialize_data" id="unserialize_data" cols="80" rows="10"><?=htmlspecialchars( $unserialize_data )?></textarea>
<br/>
<input type="submit">
</form>
</body>
</html>