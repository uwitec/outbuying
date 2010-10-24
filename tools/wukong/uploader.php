<?
session_start();
$result = array();

if (isset($_FILES['Filedata']) )
{
	$file = $_FILES['Filedata']['tmp_name'];
	$temp_file=$_FILES['Filedata']['name'];
	
	$toFileFloder = dirname(__FILE__)."/images/caixi/";
	if(!is_dir($toFileFloder))
	{
		@mkdir($toFileFloder);
		@chmod($toFileFloder,0777);
	}
	$toFile=$toFileFloder.$temp_file;
	if( move_uploaded_file( $file, $toFile ) ){
		$_SESSION["uploadFile"]="./images/caixi/".$temp_file;
	}else{
		$_SESSION["uploadFile"]='error';
	}
}
?>