<?
/**
 * 从v1中迁移过来
 * 
 * @since 1
 * @author terry
 */

class Watt_View_Helper_Tabs
{
	/**
	 * 显示选项卡
	 * 
	 * $tabs_arr应符合如下规则
	 * 注意名称使用多语
	 * 
	 * <code>
	 * $main_tabs_arr=array(
	 * '已提交'					=> '?do=main_2',
	 * '未提交'					=> '?do=test_tabs',
	 * '&nbsp;被退回的'			=> '?do=main_3',
	 * '我发出的'					=> '?do=main_4',
	 * '抄送我的'					=> '?do=main_5',
	 * );
	 * 
	 * Watt_View_Helper_Tabs::build( $main_tabs_arr );
	 * </code>
	 * 
	 */
	public static function build( $tabs_arr, $show = true )
	{
		$out  = "";
		$out .= ("<table width=\"98%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td>");
		$out .= ("<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr>");
		foreach	( $tabs_arr  as $tabs_info => $tabs_keys )
		{
			//默认选中
			$tabs_keys_arr=parse_url($tabs_keys);
			
			if( isset( $tabs_keys_arr['query'] ) )
			{
				$path_arr=explode("&",$tabs_keys_arr['query']);
				$do_arr=explode("=",$path_arr[0]);
			}
			

//			if( $tabs_info == $_GET['tabs_name'])
//			{
//				$tabs_css="tabs_down";
//				$onmouseout_css="tabs_down";
//				$onmouseover_css="tabs_down";
//			}
//			else
			if( isset($do_arr[1]) && $do_arr[1] == $_GET['do'] )
			{
				$tabs_css="tabs_down";
				$onmouseout_css="tabs_down";
				$onmouseover_css="tabs_down";
			}
			else
			{
				$tabs_css="tabs";
				$onmouseout_css="tabs";
				$onmouseover_css="tabs_hover";
			}

			$out .= "<td nowrap=\"nowrap\" class=\"".$tabs_css
			      . "\" onclick=\"javascript:location.href='".$tabs_keys
			      //. "&tabs_name=".urlencode($tabs_info)
			      . "'\" onmouseover=\"this.className='".$onmouseover_css
			      . "'\" onmouseout=\"this.className='".$onmouseout_css
			      . "'\" onmousedown=\"this.className='tabs'\">".Watt_I18n::trans($tabs_info)."</td>";
		}
		$out .= ("</tr></table>");
		$out .= ("</td></tr></table>");
		if( $show ) echo $out;
		return $out;
	}	
	
	/**
	 * tabs扩展 
	 * 增加了 回复删除
	 * john 2007-2-12
	 */
	
	public static function buildplus( $tabs_arr, $show = true )
	{
		$out  = "";
		$out .= ("<table width=\"98%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td>");
		$out .= ("<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr>");
		foreach	( $tabs_arr  as $tabs_info => $tabs_keys )
		{
			//默认选中
			$tabs_keys_arr=parse_url($tabs_keys);
			
			if( isset( $tabs_keys_arr['query'] ) )
			{
				$path_arr=explode("&",$tabs_keys_arr['query']);
				$do_arr=explode("=",$path_arr[0]);
			}
			

//			if( $tabs_info == $_GET['tabs_name'])
//			{
//				$tabs_css="tabs_down";
//				$onmouseout_css="tabs_down";
//				$onmouseover_css="tabs_down";
//			}
//			else
			if( isset($do_arr[1]) && $do_arr[1] == $_GET['do'] )
			{
				$tabs_css="tabs_down";
				$onmouseout_css="tabs_down";
				$onmouseover_css="tabs_down";
			}
			else
			{
				$tabs_css="tabs";
				$onmouseout_css="tabs";
				$onmouseover_css="tabs_hover";
			}

			$out .= "<td nowrap=\"nowrap\" class=\"".$tabs_css
			      . "\" onclick=\"javascript:location.href='".$tabs_keys
			      //. "&tabs_name=".urlencode($tabs_info)
			      . "'\" onmouseover=\"this.className='".$onmouseover_css
			      . "'\" onmouseout=\"this.className='".$onmouseout_css
			      . "'\" onmousedown=\"this.className='tabs'\">".$tabs_info."</td>";
		}
		$out .= ("</tr></table>");
		$out .= ("</td><td align='right' style='cursor:pointer'><span onclick='huifushanchu();'>".Watt_I18n::trans('RES_ZIYUAN_HUIFUJINYONG')."</span></td></tr></table>");
		if( $show ) echo $out;
		return $out;
	}	
}