<pre>
<?
$time = @$_REQUEST['time'];
$str   = @$_REQUEST['str'];

echo 'Time2Str: '.@date('Y-m-d H:i:s', $time )."\n" ;

echo 'Str2Time: '.@strtotime( $str )."\n";
?>
</pre>
<form>

Timestamp:<input type='input' value='<?=$time?>' name="time"><br>
Str<input type='input' value='<?=$str?>' name="str"><br>
<input type="submit" >

</form>