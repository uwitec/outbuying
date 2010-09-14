<form method="POST" action="?do=kw_content_addContent">
Content:<br/>
<textarea name="ct_content" rows="5" cols="80"></textarea><br/>
Name:<br/>
<input name="ct_adduser"><br/>
Email:<br/>
<input name="ct_email"><br/>
<input type="hidden" name="kws_id" value="<?=$kws_id?>">
<input type="hidden" name="op" value="1">
<input type="submit">
</form>