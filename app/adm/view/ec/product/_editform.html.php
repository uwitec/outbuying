<div><form id='form767' action="" method="post" onsubmit="$('form767_submit_area').hide();$('form767_submit_area_mask').show();if(Tpm.Validator.checkForm(this)){return true;}else{ $('form767_submit_area_mask').hide();$('form767_submit_area').show();return false; }">
<table class="formtable"><tr><th><?=Pft_I18n::trans("k_id")?></th><td><input type="text" id="k_id" name="k_id" value="<?=$k_id?>" rule="" ruletip=""/></td><td class="formdesc">&nbsp;</td></tr>
<tr><th><?=Pft_I18n::trans("p_name")?></th><td><input type="text" id="p_name" name="p_name" value="<?=$p_name?>" rule="" ruletip=""/></td><td class="formdesc">&nbsp;</td></tr>
<tr><th><?=Pft_I18n::trans("p_price")?></th><td><input type="text" id="p_price" name="p_price" value="<?=$p_price?>" rule="" ruletip=""/></td><td class="formdesc">&nbsp;</td></tr>
<tr><th><?=Pft_I18n::trans("p_info")?></th><td><input type="text" id="p_info" name="p_info" value="<?=$p_info?>" rule="" ruletip=""/></td><td class="formdesc">&nbsp;</td></tr>
<tr><th><?=Pft_I18n::trans("p_img_link")?></th><td><input type="text" id="p_img_link" name="p_img_link" value="<?=$p_img_link?>" rule="" ruletip=""/></td><td class="formdesc">&nbsp;</td></tr>
<tr><th><?=Pft_I18n::trans("p_unit")?></th><td><input type="text" id="p_unit" name="p_unit" value="<?=$p_unit?>" rule="" ruletip=""/></td><td class="formdesc">&nbsp;</td></tr>
<tr><th><?=Pft_I18n::trans("is_del")?></th><td><input type="text" id="is_del" name="is_del" value="<?=$is_del?>" rule="" ruletip=""/></td><td class="formdesc">&nbsp;</td></tr>
<tr><td colspan="3" align="center">
	<div align="center" id="form767_submit_area">
		<input type="submit" name="Submit" value="<?=Pft_I18n::trans('SUBMIT')?>" class='btn'/>
		<input type="reset" value="<?=Pft_I18n::trans('RESET')?>" class="btn"/>
		<input type="button" class="btn" onclick="history.back()" value="<?=Pft_I18n::trans('GOBACK')?>"/>
	</div>
	<div align="center" id="form767_submit_area_mask" style="display:none" ondblclick="$('form767_submit_area_mask').hide();$('form767_submit_area').show();">
		<?=Pft_I18n::trans('数据提交中，请稍候...')?>
	</div>
</td></tr>
</table><input type="hidden" id="p_id" name="p_id" value="<?=$p_id?>" /><input type='hidden' id='form767_op' name='op' value='1'></form></div>
