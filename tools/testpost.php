<?
error_reporting( E_ALL );

$post_url   = @$_REQUEST['post_url'];

if( $post_url ){
	$array = parse_url($post_url);
//	echo "<pre>Terry at [".__FILE__."(line:".__LINE__.")]\nWhen [Thu Mar 13 18:23:45 CST 2008] :\n ";
//	var_dump( $array );
//	echo "</pre>";
	//exit();
	$URL = $array["scheme"]."://"
	      .$array["host"]
	      .$array["path"];
	$Strings = "&".$array["query"];
	
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$URL);
	curl_setopt($ch, CURLOPT_HTTP_VERSION, 1.0);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $Strings);
	ob_start();
	curl_exec($ch);
	$data=ob_get_contents();
	ob_end_clean();
	curl_close ($ch);
	
	echo "Return Data:<pre>";
	echo $data;
	echo "</pre>";
}
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<form method="POST">
post_url:<br/>
<textarea name="post_url" id="post_url" cols="80" rows="10"><?=htmlspecialchars( $post_url )?></textarea>
<br/>
<input type="submit">
</form>
</body>
</html>