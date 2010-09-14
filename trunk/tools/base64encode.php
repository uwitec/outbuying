<?
error_reporting( E_ALL );

$base64encode_data   = @$_REQUEST['base64encode_data'];

$base64decode_data = @$_REQUEST['base64decode_data'];

//print( $base64decode_data );

if( $base64encode_data ){
	$base64decode_data = base64_encode( $base64encode_data );
	//var_dump( $base64decode_data );
}elseif( $base64decode_data ){
	$base64encode_data = base64_decode( $base64decode_data );
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<form method="POST">
base64encode:<br/>
<textarea name="base64encode_data" id="base64encode_data" cols="80" rows="10"><?=htmlspecialchars( $base64encode_data )?></textarea>
<br/>
base64decode:<br/>
<textarea name="base64decode_data" id="base64decode_data" cols="80" rows="10"><?=htmlspecialchars( $base64decode_data )?></textarea>
<br/>
<input type="submit">
</form>
</body>
</html>