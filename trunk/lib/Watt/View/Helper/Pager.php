<?
/**
 * 对 Watt_Util_Pager 输出的 array进行显示的类
 *
 * @author Terry
 * @package Watt_View_Helper
 */

class Watt_View_Helper_Pager{
	/**
	 * 以html的形式进行显示
	 * 接收的数组的定义是这样的
	 * 
	 * $rev[Watt_Util_Pager::PAGER_VAR_TOTAL]      = $this->getTotal();
	 * $rev[Watt_Util_Pager::PAGER_VAR_PAGE_NUM]   = $this->getPageNum();
	 * $rev[Watt_Util_Pager::PAGER_VAR_PAGE_COUNT] = $this->getPageCount();
	 * $rev[Watt_Util_Pager::PAGER_VAR_PAGE_START] = $this->getPageStart();
	 * $rev[Watt_Util_Pager::PAGER_VAR_PAGE_END]   = $this->getPageEnd();
	 * $rev[Watt_Util_Pager::PAGER_VAR_PAGE_SIZE]  = $this->getPageSize();
	 *
	 * 
	 * 
	 * @param array $pageInfo Watt_Util_Pager toArray 出的 Array
	 */
	public static function toHtml( $pageInfo, $pageEvalString="" ){
		//去掉 uri中原有的 pagenum信息
//		$query_string = $_SERVER['QUERY_STRING'];
//		$url = $_SERVER['PHP_SELF'];
//		$query_string = preg_replace( $match, "", $query_string );
//		$uri = $url."?".urlencode( $query_string );
		if( $pageEvalString == "" ){
			$match = "/[&]?".Watt_Util_Pager::PAGER_VAR_PAGE_NUM."=[+-]?[0-9]*/";
			$uri = preg_replace( $match, "", $_SERVER['REQUEST_URI'] );
			$pageEvalString = '"'.$uri.'&'.Watt_Util_Pager::PAGER_VAR_PAGE_NUM.'=".$pg';
		}

		//href="javascript:gotoPage($pg)"
		
	   //"javascript:setPagesize($pz)"
	   
		$html  = '<div class="pageinfo">';	
			
		//提供下拉列表选择 jute 20080416
		$pagesize = array('5'=>5,'10'=>10,'15'=>15,'20'=>20,'50'=>50,'100'=>100,'All'=>2147483647);		
		//$pagesizestr = $pageInfo[Watt_Util_Pager::PAGER_VAR_PAGE_SIZE];		
		if (in_array ($pageInfo[Watt_Util_Pager::PAGER_VAR_PAGE_SIZE], $pagesize)){
			
		}else{
			$pagesize[$pageInfo[Watt_Util_Pager::PAGER_VAR_PAGE_SIZE]] = $pageInfo[Watt_Util_Pager::PAGER_VAR_PAGE_SIZE];			
		}
		

		$pagesizestr = '<select class="search_input" name="pagesizeslect" id="pagesizeslect" onchange="if(this.options[this.selectedIndex].value == 2147483647 ){if(confirm(\''.Watt_I18n::trans('JT_MSG_SHUJUTAIDUOSHISHUJUTAIMANYAOQUEDINGMA').'\')){document.getElementById(\'searchFormPageSize\').value=this.options[this.selectedIndex].value;document.getElementById(\'searchform\').submit();}else{ for(var i=0;i<this.options.length;i++){if(this.options[i].value=='.$pageInfo[Watt_Util_Pager::PAGER_VAR_PAGE_SIZE].'){this.options[i].selected=true;}};return false;}}else{document.getElementById(\'searchFormPageSize\').value=this.options[this.selectedIndex].value;document.getElementById(\'searchform\').submit();}">';
		
		foreach ($pagesize as $key =>$val){				
			$check_str = "";
			if($val == $pageInfo[Watt_Util_Pager::PAGER_VAR_PAGE_SIZE]){
				$check_str = "selected";
			}
			$pagesizestr .='<option value="'.$val.'" '.$check_str.' >'.$key.'</option>';
		}
		$pagesizestr .= '</select>';
		
		$html .= "".Watt_I18n::trans('PAGE')." {$pageInfo[Watt_Util_Pager::PAGER_VAR_PAGE_NUM]}/{$pageInfo[Watt_Util_Pager::PAGER_VAR_PAGE_COUNT]} | "
		        ."{$pagesizestr}/".Watt_I18n::trans('PAGE')." | {$pageInfo[Watt_Util_Pager::PAGER_VAR_PAGE_START]} "
		        ."- {$pageInfo[Watt_Util_Pager::PAGER_VAR_PAGE_END]} ".Watt_I18n::trans('PAGE_OF')." {$pageInfo[Watt_Util_Pager::PAGER_VAR_TOTAL]}";
		        
		if ( $pageInfo[Watt_Util_Pager::PAGER_VAR_PAGE_COUNT] > 1 )	// 超过1页时才显示页面控制按钮 // bobit Tue Dec 11 10:16:27 CST 200710:16:27
		{
		
			$pagerString = self::_getUrlInfo( 1, $pageEvalString );
			$html .= " <a class=\"pagenavigator\" href=\"$pagerString\">|&lt;</a>";
			$pagerString = self::_getUrlInfo( $pageInfo[Watt_Util_Pager::PAGER_VAR_PAGE_NUM]-1, $pageEvalString );
			$html .= " <a class=\"pagenavigator\" href=\"$pagerString\">&lt;</a>";
			$pagerString = self::_getUrlInfo( $pageInfo[Watt_Util_Pager::PAGER_VAR_PAGE_NUM]+1, $pageEvalString );
			$html .= " <a class=\"pagenavigator\" href=\"$pagerString\">&gt;</a>";
			$pagerString = self::_getUrlInfo( $pageInfo[Watt_Util_Pager::PAGER_VAR_PAGE_COUNT], $pageEvalString );
			$html .= " <a class=\"pagenavigator\" href=\"$pagerString\">&gt;|</a>";
			
			$pagerString = self::_getUrlInfo( "$('".Watt_Util_Pager::PAGER_VAR_PAGE_GOTONUM."').value", $pageEvalString );
			$html .= " <input id=\"".Watt_Util_Pager::PAGER_VAR_PAGE_GOTONUM."\" size=\"1\">";
			$html .= " <a class=\"pagenavigator\" href=\"$pagerString\">".Watt_I18n::trans('PAGE_GOTO')."</a>";
		}
		
//		$html .= "<a class=\"pagenavigator\" href=\"{$uri}&".Watt_Util_Pager::PAGER_VAR_PAGE_NUM."=1\">first</a>";
//		$html .= " <a class=\"pagenavigator\" href=\"{$uri}&".Watt_Util_Pager::PAGER_VAR_PAGE_NUM."=".($pageInfo[Watt_Util_Pager::PAGER_VAR_PAGE_NUM]-1)."\"\">prev</a>";
//		$html .= " <a class=\"pagenavigator\" href=\"{$uri}&".Watt_Util_Pager::PAGER_VAR_PAGE_NUM."=".($pageInfo[Watt_Util_Pager::PAGER_VAR_PAGE_NUM]+1)."\"\">next</a>";
//		$html .= " <a class=\"pagenavigator\" href=\"{$uri}&".Watt_Util_Pager::PAGER_VAR_PAGE_NUM."=".$pageInfo[Watt_Util_Pager::PAGER_VAR_PAGE_COUNT]."\"\">last</a>";
		$html .= "</div>";
		return $html;
	}
	
	/**
	 * $pg的名字不能修改！有重要作用！
	 *
	 * @param int $pg
	 * @param string $pageNavString
	 * @return string
	 */
	private static function _getUrlInfo( $pg, $pageEvalString ){
		$eval = "";
		eval( "\$eval = $pageEvalString;" );
		//var_dump($eval);
		return $eval;
	}	
}

