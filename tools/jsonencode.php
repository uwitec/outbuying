<?
ini_set('display_errors','on');
error_reporting( E_ALL );

$jsonencode_data   = @$_REQUEST['jsonencode_data'];

$jsondecode_data = @$_REQUEST['jsondecode_data'];

//print( $jsondecode_data );

if( $jsonencode_data ){
	$jsondecode_data = json_encode( $jsonencode_data );
	//var_dump( $jsondecode_data );
}elseif( $jsondecode_data ){
	$jsonencode_data = var_export(json_decode( $jsondecode_data ),true);
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<form method="POST">
jsonencode:<br/>
<textarea name="jsonencode_data" id="jsonencode_data" cols="80" rows="10"><?=htmlspecialchars( $jsonencode_data )?></textarea>
<br/>
jsondecode:<br/>
<textarea name="jsondecode_data" id="jsondecode_data" cols="80" rows="10"><?=htmlspecialchars( $jsondecode_data )?></textarea>
<br/>
<input type="submit">
</form>
</body>
</html>