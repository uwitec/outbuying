<?
/**
 * 用jscalendar显示一 dataDateSelector
 *
 * @author Terry
 * @package Pft_View_Helper
 */

/**
 * Enter description here...
 *
 */

class Pft_View_Helper_DingdanSelector extends Zend_View_Helper_FormElement
{
	private static $_isBuildedCommonJs = false;
	private $_selector_lib_path;
	
	function __construct()
	{
		$this->_selector_lib_path = Pft_Config::getSiteRoot().'js/tpm/';
	}
	
    public function build($name, $value = null, $attribs = null, $disable=false)
    {
        //$info = $this->_getInfo($name, $value, $attribs);
        //extract($info); // name, value, attribs, options, listsep, disable
        
        // build the element
        if ($disable) {
            // disabled
            $xhtml = $this->_hidden($name, $value)
                   . htmlspecialchars($value);
        } else {
            // enabled
			$id = "dingdan_selector_".$name;
			$attribs[ "id" ] = $id;
			$sitePath = Pft_Config::getSiteRoot();

            $xhtml = $this->_getCommonHtml();			
            $xhtml .= Pft_View_Helper::buildElmentByVartype( "C", $name, $value, $attribs );
			//echo "<script>alert('aa');</script>";
            /*
            $xhtml = '<input type="text"'
                   . ' name="' . htmlspecialchars($name) . '"'
                   . ' value="' . htmlspecialchars($value) . '"'
                   . $this->_htmlAttribs($attribs)
                   . ' />';*/
			$xhtml .= '<a href="#" class="btn" type="button" id="btnSelectCr" onclick="ddSelTool.show(  \''.$attribs[ "id" ].'\', this,\'\',\'s0=\'+document.all.'.$name.'.value+\'\' );return false;">'.i18ntrans('#选').'</a>';
        }

        return $xhtml;
    }
	private function _getCommonHtml()
	{
		$out = "";
		if( !self::$_isBuildedCommonJs ){
			self::$_isBuildedCommonJs = true;
			$out .= '<script type="text/javascript" src="'.$this->_selector_lib_path.'selectordd.js"></script>';
		}
		return $out;
	}
}