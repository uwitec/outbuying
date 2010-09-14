<?
/**
 * 用jscalendar显示一 dataDateSelector
 *
 * @example 
 * $dateselector = new Watt_View_Helper_DateSelector();
 * $dateselector->setShowTimes(true);
 * echo $dateselector->build('rwd_renwukaishishijian',$rwd_renwukaishishijian,array('id'=>'rwd_renwukaishishijian'));
 * 
 * @author Terry
 * @package Watt_View_Helper
 * @version 0.1.1
 * Wed Dec 24 15:44:14 CST 2008 修正一个notice
 */

/**
 * Enter description here...
 *
 */

class Watt_View_Helper_DateSelector extends Zend_View_Helper_FormElement
{
	private static $_isBuildedCommonJs = false;
	private $_calendar_lib_path;
	private $_calendar_lib_path_new;
	private $_calendar_theme_file;
	
	private $_showTimes = false;
	
	private $_showTimeSecs=false;
	
	public function setShowTimes( $v ){$this->_showTimes = $v;}
	public function getShowTimes(){return $this->_showTimes;}
	
	public function setShowTimeSecs( $v ){$this->_showTimeSecs = $v;}
	public function getShowTimeSecs(){return $this->_showTimeSecs;}
	
	function __construct()
	{
		$this->_calendar_lib_path = Watt_Config::getSiteRoot().'js/john/jscalendar/';
		$this->_calendar_lib_path_new = Watt_Config::getSiteRoot().'js/calendar/';
		$this->_calendar_theme_file = "calendar-win2k-cold-1";	//不要带.css
		
		//$this->_calendar_lib_path = Watt_Config::getSiteRoot().'js/calendar/';
		//$this->_calendar_lib_path_new = Watt_Config::getSiteRoot().'js/calendar/';
		//$this->_calendar_theme_file = "calendar-win2k-cold-1";	//不要带.css
		//calendar-win2k-cold-1
		
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
			if(!isset($attribs['id'])){
	            $id = "date" . mt_rand( 1000, 9999 );
				$attribs[ "id" ] = $id;
			}else{
				$id = $attribs[ "id" ] ;
			}
			$sitePath = Watt_Config::getSiteRoot();
			
            $xhtml  = Watt_View_Helper::buildElmentByVartype( "C", $name, $value, $attribs );
            $xhtml .= $this->_getCommonHtml();
            /*
            $xhtml = '<input type="text"'
                   . ' name="' . htmlspecialchars($name) . '"'
                   . ' value="' . htmlspecialchars($value) . '"'
                   . $this->_htmlAttribs($attribs)
                   . ' />';*/
            
			$xhtml .= <<<EOT
<img id="dateButton_{$id}" src="{$sitePath}js/john/jscalendar/calendar.gif" border="0" align="absmiddle" style="cursor:pointer" />
<!--<input   name="start_date[{\$i}]" type="text" class="Input1" id="start_date[{\$i}]" value="{\$start_mren}" size="14" readonly/>-->
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "{$id}",
EOT;
	if( $this->_showTimes ){
		$xhtml .= '
        ifFormat       :    "%Y-%m-%d %H:%M",
        showsTime      :    true,';
	}
	else if($this->_showTimeSecs)
	{
		$xhtml .= '
        ifFormat       :    "%Y-%m-%d %H:%M:%S",
        showsTime      :    true,';
	}
	else{
		$xhtml .= '
        ifFormat       :    "%Y-%m-%d",';
	}
	$xhtml .= <<<EOT
        button         :    "dateButton_{$id}",
        singleClick    :    true,
        step           :    1
    });
</script>
EOT;
	
           /* $xhtml .= <<<EOT
        <img id="dateButton_{$id}" src="{$sitePath}js/john/jscalendar/calendar.gif" border="0" align="absmiddle" style="cursor:pointer" onClick="Calendar.setDayHM(document.getElementById({$id}),0,'');"/>       
EOT;*/
        }

        return $xhtml;
    }
	
	private function _getCommonHtml()
	{
		$out = "";
		if( !self::$_isBuildedCommonJs ){
			self::$_isBuildedCommonJs = true;
			$out  = '<link rel="stylesheet" type="text/css" media="all" href="' .
                   $this->_calendar_lib_path . $this->_calendar_theme_file .
                   '.css" />';
			$out .= '<script type="text/javascript" src="'.$this->_calendar_lib_path.'calendar.js"></script>';
			$out .= '<script type="text/javascript" src="'.$this->_calendar_lib_path.'lang/calendar-en.js"></script>';
			$out .= '<script type="text/javascript" src="'.$this->_calendar_lib_path.'calendar-setup.js"></script>';
			/*
			if (Watt_Session::getSession()->getlanguage() == 'en')
			{	
				$out .= '<script type="text/javascript" src="'.$this->_calendar_lib_path_new.'lang/calendar-en.js"></script>';			
			}else{
				$out .= '<script type="text/javascript" src="'.$this->_calendar_lib_path_new.'lang/calendar-cn.js"></script>';			
			}		
			$out .= '<script type="text/javascript" src="'.$this->_calendar_lib_path_new.'calendar.js"></script>';
			*/
		}
		return $out;
	}
}