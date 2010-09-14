<?
include( Pft_Config::getCfg('PATH_ROOT').'inc/view/header.inc.php' );
?>

<div>
<form action="?do=kw_kw_search" method="POST">
<input type="text" name="s">
<input type="submit">
</form>
</div>
<div class="keywords_list">
	<h1>Best</h1>
<?
	if( is_array( $hot_keywords ) ){
		foreach ($hot_keywords as $aKeywords) {
		?>
	<div class="keywords"><a href="?do=kw_kw_search&s=<?=urlencode($aKeywords['kws_words'])?>"><?=h(substr($aKeywords['kws_words'],0,16))?>(<?=$aKeywords['kws_ct_count']?>)</a></div>
		<?
		}		
	}
?>
	<div class="cls"></div>
	<h1>New</h1>
<?
	if( is_array( $newest_keywords ) ){
		foreach ($newest_keywords as $aKeywords) {
		?>
	<div class="keywords"><a href="?do=kw_kw_search&s=<?=urlencode($aKeywords['kws_words'])?>"><?=h(substr($aKeywords['kws_words'],0,16))?>(<?=$aKeywords['kws_ct_count']?>)</a></div>
		<?
		}		
	}
?>
	<div class="cls"></div>
</div>
<?
include( Pft_Config::getCfg('PATH_ROOT').'inc/view/footer.inc.php' );
?>