<?

class Pft_View_Helper_Form_Select extends Zend_View_Helper_FormElement
{
	public static function build($name, $value = null, $attribs = null, $selectValue = null,$is_zhanghu=null)
	{
		$attribStr = '';
		if( $attribs && is_array($attribs) ){
			foreach ($attribs as $key=>$attrib) {
				$attribStr .= " $key=\"$attrib\"";
			}
		}
		else
			$attribStr = $attribs;
			
		//$str="onchange=pmxuanze(\"".$is_zhanghu."\",this.value)";
		
		$html = "";
		$html .= '<select name="'.$name.'" '.$attribStr.' >'."\n";
		//$out .= '<option>               </option>';
		if( is_array( $value ) )
		{
			foreach ( $value as $key=>$value )
			{
				//var_dump( "[$selectValue].[$value]" );
				$selSign =($key == $selectValue)&&( $selectValue !== '' )?"selected":"";
				$html .= '<option value="'.htmlspecialchars($key).'" '.$selSign.'>'.@htmlspecialchars($value).'</option>'."\n";
			}			
		}
		else
		{
			$html .= '<option value="'.$value.'">'.$value.'</option>'."\n";
		}
		$html .= '</select>'."\n";
		return $html;
	}
}