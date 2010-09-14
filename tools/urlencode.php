<?
error_reporting( E_ALL );

$urlencode_data   = @$_REQUEST['urlencode_data'];

$urldecode_data = @$_REQUEST['urldecode_data'];

//print( $urldecode_data );

if( $urlencode_data ){
	$urldecode_data = urlencode( $urlencode_data );
	//var_dump( $urldecode_data );
}elseif( $urldecode_data ){
	$urlencode_data = urldecode( $urldecode_data );
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<form method="POST">
urlencode:<br/>
<textarea name="urlencode_data" id="urlencode_data" cols="80" rows="10"><?=htmlspecialchars( $urlencode_data )?></textarea>
<br/>
urldecode:<br/>
<textarea name="urldecode_data" id="urldecode_data" cols="80" rows="10"><?=htmlspecialchars( $urldecode_data )?></textarea>
<br/>
<input type="submit">
</form>
</body>
</html>