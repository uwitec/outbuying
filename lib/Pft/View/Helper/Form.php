<?
/**
 * 自动生成一个Form
 *
 * @author Terry
 * @package Pft_View_Helper
 */

class Pft_View_Helper_Form 
{
	/**
	 * 当字符长度大于这个值时，使用textarea来显示
	 *
	 * @var int
	 */
	protected static $_bigTextLen = 60;
	/**
	 * 忽略显示的变量的变量名列表
	 *
	 * @var array
	 */
	protected static $_ignoreFieldList = array( "CreatedAt", "UpdatedAt"
	                                          , "created_at", "updated_at"
	                                          , "ZuId", "zu_id"
	                                          , "Shifoushanchu", "shifoushanchu"
	                                          );
	
	/**
	 * 使用array建立form
	 *
	 * @param mix $data
	 * @param string $action = ""
	 * @param string $method = "post"
	 * @param boolean $enable = true
	 * @param boolean $show = true
	 * @return string HTML
	 */
	public static function buildFormWithDbData( $data, $action="", $method="post", $enable=true, $show=true, $dataDesc=array() )
	{
		//忽略列表应可动态增加
		$formId = mt_rand ( 100, 999 );
		$out  = "<div><form id='form".$formId."' action=\"$action\" method=\"".$method."\" onsubmit=\"$('{$formId}_submit_area').hide();$('{$formId}_submit_area_mask').show();if(Tpm.Validator.checkForm(this)){return true;}else{ $('{$formId}_submit_area_mask').hide();$('{$formId}_submit_area').show();return false; }\">\n";
		$out .= '<table class="formtable">';
		if( is_array( $data ) )
		{
			$i=0;
			foreach ( $data as $key => $val )
			{
				if( self::_isIgnoreField($key) )continue;

				if( $i == 0 ){
					$out .= "<tr>";
				}elseif ($i % 2 == 0){
					$out .= "</tr>\n<tr>";
				}
				$i++;
				$out .= "<th class=GridTH>".Pft_I18n::trans($key)."</th>";
				$out .= "<td>";
				if( $enable )
				{
					if( strlen( $val ) > self::$_bigTextLen )
					{
						$out .= "<textarea cols=".self::$_bigTextLen." id=\"$key\" name=\"$key\" rule=\"\" ruletip=\"\">" . htmlspecialchars($val) ."</textarea>";
					}
					else
					{
						$out .= Pft_View_Helper::buildElmentByVartype( "", $key, $val, array('rule'=>'','ruletip'=>''));
						//$out .= "<input type=\"text\" name=\"$key\" value=\""
				    	//      . htmlspecialchars($val) ."\" />";						
					}
				}
				else
				{
					$out .= htmlspecialchars($val);
				}
				    	  
				$out .= "</td>";
				if( key_exists( $key, $dataDesc ) )
				{
					$out .= "<td class=\"formdesc\">";
					$out .= htmlspecialchars( $dataDesc[$key] );
					$out .= "</td>";
				}
				//$out .= "</tr>\n" ;
			}
			if( $i > 0 ){
				$out .= "</tr>\n" ;				
			}
			if( $enable )
			{
				//$out .= "<tr><td colspan=99 align=center><input type='submit' class='btn'/> <input type='reset' class='btn'/></td></tr>\n";
$out .= "
<tr><td colspan=99 align=center class='bottom'>
	<div align=\"center\" id=\"{$formId}_submit_area\">
		<input type='submit' name='Submit' value=\"".Pft_I18n::trans('SUBMIT')."\" class='btn'/> 
		<input type='reset' value=\"".Pft_I18n::trans('RESET')."\" class='btn'/>
		<input type='button' class='btn' onclick=\"history.back()\" value=\"".Pft_I18n::trans('GOBACK')."\"/>
	</div>
	<div align='center' id='{$formId}_submit_area_mask' style='display:none' ondblclick=\"$('{$formId}_submit_area_mask').hide();$('{$formId}_submit_area').show();\">
		".Pft_I18n::trans('数据提交中，请稍候...')."
	</div>
</td></tr>";
			}
		}
		else
		{
			
		}
		$out .= "</table><input type='hidden' id='{$formId}_op' name='op' value='1'></form></div>\n";
		if( $show )echo $out;
		return $out;
	}

	public static function buildFormForWukong( $data
	                                         , $action=""
                                             , $method="post"
                                             , $enable=true
                                             , $show=false
                                             , $ignoreList=array()
                                             , $hiddenList=array() )
	{
		//忽略列表应可动态增加
		
		$hiddenKeys = array();
		
		$formId = "form".mt_rand ( 100, 999 );
		$out  = "<div>";
		if( $enable ) $out .= "<form id='".$formId."' action=\"$action\" method=\"".$method."\" onsubmit=\"$('{$formId}_submit_area').hide();$('{$formId}_submit_area_mask').show();if(Tpm.Validator.checkForm(this)){return true;}else{ $('{$formId}_submit_area_mask').hide();$('{$formId}_submit_area').show();return false; }\">\n";
		if( is_array( $data ) )
		{
			$out .= '<table class="formtable">';
			foreach ( $data as $key => $val )
			{
				if( self::_isIgnoreField($key) )continue;
				if( key_exists( $key, $hiddenList) ){
					$hiddenKeys[] = $key;
					continue;
				}
				$out .= "<tr>";
				$out .= "<th><?=Pft_I18n::trans(\"$key\")?></th>";
				$out .= "<td>";
				if( $enable )
				{
					if( strlen( $val ) > self::$_bigTextLen )
					{
						$out .= "<textarea cols=".self::$_bigTextLen." id=\"$key\" name=\"$key\" rule=\"\" ruletip=\"\"><?=\$"
				    	      . $val ."?></textarea>";
					}
					else
					{
						//$out .= Pft_View_Helper::buildElmentByVartype( "", $key, $val );
						$out .= "<input type=\"text\" id=\"$key\" name=\"$key\" value=\"<?=\$"
				    	      . $val ."?>\" rule=\"\" ruletip=\"\"/>";						
					}
				}
				else
				{
					$out .= "<?=\$" . $val . "?>";
				}
				    	  
				$out .= '</td>';
				$out .= '<td class="formdesc">&nbsp;</td>';
				$out .= "</tr>\n";
			}
			if( $enable )
			{
				$out .= <<<EOT
<tr><td colspan="3" align="center">
	<div align="center" id="{$formId}_submit_area">
		<input type="submit" name="Submit" value="<?=Pft_I18n::trans('SUBMIT')?>" class='btn'/>
		<input type="reset" value="<?=Pft_I18n::trans('RESET')?>" class="btn"/>
		<input type="button" class="btn" onclick="history.back()" value="<?=Pft_I18n::trans('GOBACK')?>"/>
	</div>
	<div align="center" id="{$formId}_submit_area_mask" style="display:none" ondblclick="$('{$formId}_submit_area_mask').hide();$('{$formId}_submit_area').show();">
		<?=Pft_I18n::trans('数据提交中，请稍候...')?>
	</div>
</td></tr>\n
EOT;
			}
			$out .= "</table>";
			foreach ( $hiddenKeys as $key )
			{
				$out .= "<input type=\"hidden\" id=\"$key\" name=\"$key\" value=\"<?=\$"
					    	      . $data[$key] ."?>\" />";
			}
		}
		else
		{
			
		}

		if( $enable ) $out .= "<input type='hidden' id='{$formId}_op' name='op' value='1'></form>";
		$out .= "</div>\n";
		if( $show )echo $out;
		return $out;
	}

	private static function _isIgnoreField( $key )
	{
		return in_array( $key, self::$_ignoreFieldList );
	}
	
	
}
