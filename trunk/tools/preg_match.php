<?
error_reporting( E_ALL );

$pattern = @$_REQUEST['pattern'];
$subject = @$_REQUEST['subject'];
$mode = @$_REQUEST['mode'];
$result = null;
$matches = null;
if( $pattern && $subject ){
	switch ( $mode ){
		case "preg_match_all":
			$result = preg_match_all( $pattern, $subject, $matches );
			break;
		case "preg_match":
		default:
			$result = preg_match( $pattern, $subject, $matches );
	}
}
?>
int preg_match ( string pattern, string subject [, array matches [, int flags]] )<br/>
int preg_match_all ( string pattern, string subject, array matches [, int flags] )<br/>
<form>
	pattern : example [ /^test/ ]<br/>
	<input type="text" name="pattern" size="51" value="<?=htmlspecialchars($pattern)?>"><br/>
	subject : <br/>
	<textarea name="subject" cols="50" rows="10"><?=htmlspecialchars($subject)?></textarea><br/>
	mode : <input type="radio" name="mode" value="preg_match" <?=($mode=='preg_match')?'checked':''?>> preg_match <input type="radio" name="mode" value="preg_match_all" <?=($mode=='preg_match_all')?'checked':''?>> preg_match_all<br/>
	<input type="submit"><br/>
	result : <?=$result?><br/>
	matches :<br/>
	<textarea cols="50" rows="10"><?
	var_export($matches);
	?></textarea>
</form>