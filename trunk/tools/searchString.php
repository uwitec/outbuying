<?php
$begin = microtime(true);
function loadLangString( $langPath ){
	$arrString = array();
	if( !is_dir( $langPath ) ) return ;
	$d = dir( $langPath );
	if( is_object( $d ) ){
		while (false !== ($entry = $d->read())){
			if( is_file( $langPath.$entry ) && strtolower(substr($entry,-4,4)) == '.php' ){
				$str2s = include_once( $langPath.$entry );
				if( is_array( $str2s ) ){
					$arrString = array_merge( $arrString, $str2s);							
				}
			}
		}
		$d->close();			
	}
	return $arrString;
}

$s = $_POST['s'];
if( $s ){
	$langPathZH = dirname(__FILE__)."/../../language/default/";
	$langPathEN = dirname(__FILE__)."/../../language/en/";
	$langPathJA = dirname(__FILE__)."/../../language/jp/";
	$arrZH = loadLangString($langPathZH);
	$arrEN = loadLangString($langPathEN);
	$arrJA = loadLangString($langPathJA);	
	
	$results = array();
	foreach ($arrZH as $key => $val) {
		if(strpos($val, $s) !== false
		|| strpos($key, $s) !== false
		){
			$row = array();
			$row['key'] = $key;
			$row['zh-CN'] = $val;
			$row['en'] = key_exists($key, $arrEN)?$arrEN[$key]:"<i>!NOT EXIST</i>";
			$row['ja'] = key_exists($key, $arrJA)?$arrJA[$key]:"<i>!NOT EXIST</i>";
			$results[] = $row;
		}
	}
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style>
*{font-size:12px;}
th{background-color:#CCC;}
td{background-color:#EEE;}
.red{color:#F00;}
</style>
</head>
<body>
<form method="post">
	<label for="search_string">请输入中文文字或KEY中可能出现的文字：</label>
	<input type="text" id="search_string" name="s" value="<?=@$s?>">
	<input type="submit"/>
</form>
<?
if(isset($results) && count($results)>0){
?>
<table>
	<tr>
		<th>KEY</th>
		<th>zh-CN</th>
		<th>en</th>
		<th>ja</th>
	</tr>
<?
	foreach ($results as $row) {
?>
	<tr>
		<td><?=str_replace($s, "<span class=\"red\">$s</span>" ,$row['key'])?></td>
		<td><?=str_replace($s, "<span class=\"red\">$s</span>" ,$row['zh-CN'])?></td>
		<td><?=$row['en']?></td>
		<td><?=$row['ja']?></td>
	</tr>
<?	
	}
?>
</table>
<?
}else{
?>
No result.
<?
}

$end = microtime(true);
$usedTime = ($end - $begin) * 1000;
?>
<div>Used time : <?=$usedTime?>ms</div>
</body>
</html>