<?php
$beginTime = microtime(true);

ini_set('display_errors','on');
error_reporting(E_ALL);

include_once(dirname(__FILE__)."/../../lib/Watt/Db.php");
/*$config_data=include_once(dirname(__FILE__)."/../../config/default/propel.conf.php");
$cg_data=$config_data["datasources"]["propel"]["connection"];

$db_cfg['hostspec'] = $cg_data["hostspec"];
$db_cfg['port'] = '3306';
$db_cfg['username'] = $cg_data["username"];
$db_cfg['password'] = $cg_data["password"];
$db_cfg['database'] = $cg_data["database"];
*/
$db_cfg['hostspec'] = 'localhost';
$db_cfg['port'] = '3306';
$db_cfg['username'] = 'tpm_watt';
$db_cfg['password'] = 'tpm20080808';
$db_cfg['database'] = 'DEV_tpm_watt';

$db_cfg['charset'] = 'utf8';

$db = Watt_Db::getDb($db_cfg);

$default_tables = "";
//------------------------------
$cmd = @$_REQUEST['cmd'];
if( 'backup' == $cmd ){
	$tables = @$_REQUEST['tables'];
	$tableArr = explode("\n",$tables);
	
	$theWriter = new SqlWriter();
	foreach ( $tableArr as $table) {
		$table = trim( $table );
		if( !$table )continue;

		//Drop
		$theWriter->writeln("\nDROP TABLE IF EXISTS {$table};");
		
		//Create New
		$row = $db->getRow("show create table {$table};");
		$createSql = $row["Create Table"];
		$theWriter->writeln($createSql.";\n");
		
		//Insert Data
		$rs = $db->getResult("select * from {$table};");
		
		while ($row = $rs->fetchRow()) {
			$values = array();
			foreach ($row as $key=>$val) {
				if( is_null( $val ) ){
					$values[] = "null";
				}elseif ( is_numeric( $val ) ){
					$values[] = $val;
				}else{
					$values[] = "'".mysql_escape_string($val)."'";
				}
			}
			$insert = "insert into {$table} values(".(implode(',',$values)).");";
			$theWriter->writeln($insert);
		}
	}
	$bak_file_name = $theWriter->getFilename();
	$bak_file_size = $theWriter->getFilesize();
}

//------------------------------------
class SqlWriter{
	private $_sql = '';
	private $_fp;
	private $_filename;
	
	function __construct(){
		$backupFile = date('Ymd-His-').mt_rand(1000,9999).".sql";
		$this->_filename = $backupFile;
		$fp = fopen( $backupFile, 'w+' );
		if($fp){
			$this->_fp = $fp;
			$this->writeln( "SET FOREIGN_KEY_CHECKS=0;\n" );
		}else{
			throw new Exception('Cannot create sql file');
		}
	}
	
	function __destruct(){
		if( $this->_fp )
			fclose( $this->_fp );
	}

	public function getFilename(){
		return $this->_filename;
	}
	
	public function getFilesize(){
		return filesize($this->_filename);
	}

	public function writeln( $line ){
		if( $this->_fp ){
			fwrite( $this->_fp, $line );
			fwrite( $this->_fp, "\n" );
		}
	}
}
//------------------------------

$allTables = $db->getAllAsCol("show tables");

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Backup db to sql</title>
<link rel="stylesheet" type="text/css" href="css.css" />
<style>
.download{
	background-color: #fc9;
}
.download,.download *{
	font-size:18px;
}
</style>
</head>
<body>
<div>
<form method="post">
<table>
	<tr>
		<td valign="top">
			<fieldset>
				<legend>All tables</legend>
				<textarea name="all_tables" rows="20" cols="40"><?
			foreach ($allTables as $table) {
				echo $table."\n";
			}
			?></textarea>
			</fieldset>
		</td>
		<td>
			<!--button onclick="" title="鍔熻兘灏氭湭瀹炵幇" disabled>&gt;&gt;</button-->
		</td>
		<td valign="top">
			<fieldset>
				<legend>To backup tables</legend>
				<textarea name="tables" rows="20" cols="40"><?=isset($tables)?$tables:$default_tables?></textarea>
			</fieldset>
		</td>
	</tr>
	<tr>
		<td colspan="2" valign="top">
			<input type="hidden" name="cmd" value="backup">
			<input type="submit" value="寮�濮嬪浠�">
		</td>
	</tr>
</table>
</form>
</div>
<?
if(isset($bak_file_name)){
?>
<fieldset>
	<legend>Click to download</legend>
	<div class="download">
		<a href="<?=$bak_file_name?>"><?=$bak_file_name?></a> (<?=number_format($bak_file_size/1024,2,'.',',')."KB"?>)
	</div>
</fieldset>
<?
}

$endTime = microtime(true);
?>
<div>Please edit this php file to config database or default backup tables</div>
<div id="footer">Process [<?=round(($endTime-$beginTime)*1000,4)?>ms] Querys [<?=$db->getQueryTimes()?>]</div>
</body>
</html>