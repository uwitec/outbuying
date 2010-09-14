<?
class Watt_View_Helper_SearchTip extends Zend_View_Helper_FormElement
{
	private static $_isBuildedCommonJs = false;
	private $_selector_lib_path;
	
	function __construct()
	{
		$this->_selector_lib_path = Watt_Config::getSiteRoot().'js/tpm/';
	}
	
    public function build($name, $value = null, $attribs = null, $disable=false,$extend = null)
    {
        //$info = $this->_getInfo($name, $value, $attribs);
        //extract($info); // name, value, attribs, options, listsep, disable
        $xhtml = '';
        // build the element
        if ($disable) {
            // disabled
            $xhtml = $this->_hidden($name, $value)
                   . htmlspecialchars($value);
        } else {
            // enabled
            if( !self::$_isBuildedCommonJs ){
				$xhtml .= $this->_getCommonHtml();
				//$xhtml .= "<div id='chaxun_zhushou_xMsg' style='padding:1px;background-color:white;display:none;left:360px;top:160px;position:absolute;'></div>";
				self::$_isBuildedCommonJs = true;
            }	

            $id = "dingdan_selector_".$name;
			$attribs[ "id" ] = $id;
			//$attribs[ "onkeyup" ] = "div_chaxun_zhushou('" . $extend['SearchType'] . "')";
			//$attribs[ "onblur" ] = "div_chaxun_zhushou_yincang()";
	
            $xhtml .= Watt_View_Helper::buildElmentByVartype( "C", $name, $value, $attribs );
			$xhtml .= <<<EOT
			<script type="text/javascript">
			Tpm.SearchTip.setup( '{$id}', '{$extend['SearchType']}' );
			</script>
EOT;
            //echo "<script>alert('aa');</script>";
            /*
            $xhtml = '<input type="text"'
                   . ' name="' . htmlspecialchars($name) . '"'
                   . ' value="' . htmlspecialchars($value) . '"'
                   . $this->_htmlAttribs($attribs)
                   . ' />';*/
		}
        return $xhtml;
    }
	private function _getCommonHtml()
	{
		$out = "";
		$out .= '<script type="text/javascript" src="'.$this->_selector_lib_path.'searchtip.js"></script>';
		return $out;
	}
}
?>