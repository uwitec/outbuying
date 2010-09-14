<?
/**
 * 这是对 Watt_Menu 进行显示的类
 *
 * @since 1
 * 
 * @author Administrator
 * @package Watt_View_Helper
 */

class Watt_View_Helper_Menu
{
	private $_menu_lib_path;
	
	function __construct()
	{
		$this->_menu_lib_path = Watt_Config::getSiteRoot(1).'js/jsmenu/';
	}
	
	public function buildMenu( $data, $show = true )
	{
		//没调好 先藏掉
		//Terry
		//return "";
		$xhtml = "";
		if( is_array( $data ) )
		{
			$xhtml = '<div id="mainmenu">'."\n";
//			$xhtml .= '<ul>';
//			foreach ( $data as $key=>$val)
//			{
//				$xhtml .= '<li><a href="'.$val[1].'">'.$val[0].'</a></li>'."\n";
//			}
//			$xhtml .= '</ul>';
			
			/**
			 * 这里是读取全部菜单的代码
			 */
//			$c = new Criteria();
//			$c->addAscendingOrderByColumn( TpmCaidanPeer::CD_SHANGJI_ID );
//			$c->addAscendingOrderByColumn( TpmCaidanPeer::CD_PAIXU );
//			$menus = TpmCaidanPeer::doSelect( $c );
			// 结束
			$menu_arr = self::sortMenu($data);
//			$menus = $data;
//			$menu_arr = array();
//			$hash_table = array();
//			foreach ( $menus as $menu )
//			{
//				if( is_array( $menu ) ){
//					$cd_id = $menu['cd_id'];
//					$cd_chuliye = $menu['cd_chuliye'];
//					$cd_shangji_id = $menu['cd_shangji_id'];
//				}elseif( is_object( $menu ) ){
//					$cd_id = $menu->getCdId();
//					$cd_chuliye = $menu->getCdChuliye();
//					$cd_shangji_id = $menu->getCdShangjiId();					
//				}else{
//					continue;
//				}
//				
//				/**
//				 * 使用 $hash_table[$cd_id] 的方式 
//				 * 而不使用
//				 * //$item_arr = array();
//				 * //$hash_table[$cd_id] = $item_arr;
//				 * 的方式是有目的，
//				 * 因为 select 输出的menu 排序可能会把子菜单排在前边
//				 */
//												
//				$hash_table[$cd_id][0] = Watt_I18n::trans( $menu->getCdMingcheng() );
//				$hash_table[$cd_id][1] = $menu->getCdChuliye()?($_SERVER['PHP_SELF'].$cd_chuliye):"";
//				$hash_table[$cd_id][2] = null;
//				
//				if( $cd_shangji_id )
//				{
//					$shangji_id = $cd_shangji_id;
//					/*
//					if( !isset($hash_table[$shangji_id][3]) || !is_array( $hash_table[$shangji_id][3] ) )
//					{
//						//$hash_table[$shangji_id][3] = array();
//					}
//					*/
//					//$hash_table[$shangji_id][3][] = &$hash_table[$cd_id];
//					if( !isset( $hash_table[$shangji_id] ) )
//					{
//						$hash_table[$shangji_id][0] = null;
//						$hash_table[$shangji_id][1] = null;
//						$hash_table[$shangji_id][2] = null;
//					}
//					$hash_table[$shangji_id][] = &$hash_table[$cd_id];
//				}
//				else
//				{
//					$menu_arr[] = &$hash_table[$cd_id];
//				}
//			}
			$roleId = Watt_Session::getSession()->getRoleId();
			$crSessionRoleId = array(
				  '6b32ff50-df19-4e07-d50c-45b6b62bc171' => 'CR'		//说明这个是客户的角色ID
				//, '82109310-4a2e-bcb3-d919-45ffacdcf107' => 'QDKH'
				//, 'e48e869d-da50-ffb9-b086-45ffac2114ff' => 'CSKH'
				, '2798de2b-30bf-9dcb-22cd-45b6b68b315e' => 'TR'
				, '4ade1c61-fac6-8f11-4200-466fa0a2c627' => 'CR'			//都彼客户
				, '61c705eb-0cde-4867-3211-45b6b6753d4d' => 'PR'			//审校
				,'8fdee018-5bd1-1a17-61c4-491a8b139cf9'=>'CRCPM'
				);
				
			if( Watt_Session::getSession()->isOutterUser()/*key_exists( $roleId, $crSessionRoleId )*/ ){
				$xhtml .= "<link rel=\"stylesheet\" href=\"{$this->_menu_lib_path}xqtrmenu.css\">";
			}else{
				$xhtml .= "<link rel=\"stylesheet\" href=\"{$this->_menu_lib_path}xqmenu.css\">";
			}
			
			$xhtml .= "<div id=\"xqwrapper\"><div id=\"xqhead\"><div id=\"xqpositioner\"><div class=\"xqmenu\"> "; 
			foreach ( $menu_arr as $menu_first )
			{
				if ( count($menu_first) == 3 )
				{
					$xhtml .= "<a class=\"xqouter1\" href=\"" . $menu_first[1] . "\">" . $menu_first[0] . "</a>";
				}else{
					$xhtml .= "<a class='xqouter' href='javascript:void(0)' >" . $menu_first[0] . "<table class='xqfirst'><tr><td align='center'>";
					foreach ( $menu_first as $menu_second)
					{
						if ( is_array( $menu_second ))
						{
							if ( count( $menu_second) == 3)
							{
								$xhtml .= "<a class='xqinner' href='" . $menu_second[1] . "'>" . $menu_second[0] . "</a>";
							}else{
								$xhtml .= '<a class="xqsecond" href="javascript:void(0)" >'	.$menu_second[0] . '<table><tr><td>';
								foreach ( $menu_second as $menu_third)
								{
									if ( is_array( $menu_third ))
									{
										$xhtml .= '<a class="xqinner" href="' . $menu_third[1] . '">' . $menu_third[0] .'</a>';
									}else{
										continue;	
									}
								}
								$xhtml .= '</td></tr></table></a>';
							}
						}else{
							continue;
						}
					}
					$xhtml .= "</td></tr></table></a>";
				}
			}
			$xhtml .= '</div>';
			/*firefox下的菜单，有问题，暂时注释掉
			/*firefox下的菜单，有问题，暂时注释掉
			$xhtml .= '<div id="xqnoniemenu">';
			foreach ( $menu_arr as $menu_first)
			{
				if ( count($menu_first) == 3)
				{
					$xhtml .= "<div class=\"xqholder\"><ul><li><a class=\"xqouter\" href=" . $menu_first[1] . ">" . $menu_first[0] . "</a></li></ul></div>";
				}else{
					$xhtml .= "<div class=\"xqholder\"><ul><li><a class=\"xqouter\" href=" . $menu_first[1] . ">" . $menu_first[0] . "</a></li>";
					foreach ( $menu_first as $menu_second)
					{
						if ( is_array( $menu_second ))
						{
							$xhtml .="<li><a class='xqinner' href=" . $menu_second[1] . ">" . $menu_second[0] . "</a></li>";
						}else{
							continue;
						}
					}
					$xhtml .= "</ul></div>";
				}
			}
			*/
			$xhtml .= "</div></div></div>";
			
			if( (Watt_Session::getSession()->getSession()->getRoleShortname() == 'CR' || Watt_Session::getSession()->getSession()->getRoleShortname() == 'CRCPM') 
				&& Watt_Session::getSession()->getData('kh_zizhuxiadan') > 0
			){
				$xhtml .= "<div class='quick_order'>";
				if( Watt_Session::getSession()->isTq() ){
					$xhtml .= "<a TQCmd='CmdType=CallOrder' id='TQCmdTag_CallOrder' style='cursor:hand;' TQFileType=\"".Watt_I18n::trans("EC_PUBLIC_FILETYPES_LIST")."\">".Watt_I18n::trans('快速下单')."</a>";					
				}else{
					if(Watt_Session::getSession()->getData('kh_zizhuxiadan')==1){
						$xhtml .= "<a style='cursor:hand;' href='?do=ec_order_order'\">".Watt_I18n::trans('快速下单')."</a>";	
					}else{
						$xhtml .= "<a style='cursor:hand;' href='?do=ec_dingdan_webadd'\">".Watt_I18n::trans('快速下单')."</a>";	
					}
				}
				$xhtml .= '</div>';
			}
			$xhtml .= '<div class="cls"></div></div>'."\n";
			
					
			if( $show )
			{
				echo $xhtml;
			}
			return $xhtml;
			exit;
			
			
			$menuitems = Watt_View_Helper_Js::makeJsArrayExpress( $menu_arr, 'MENU_ITEMS');
//<link rel="stylesheet" href="{$this->_menu_lib_path}menu.css">			
			$xhtml .= <<<eom
<div id="triger_menu">
<!-- menu script itself. you should not modify this file -->
<script language="JavaScript" src="{$this->_menu_lib_path}menu.js"></script>
<!-- items structure. menu hierarchy and links are stored there -->
eom;
//<script language="JavaScript" src="{$this->_menu_lib_path}menu_items.js"></script>
			$xhtml .= <<<eom
<script>
{$menuitems}
</script>	
eom;
			$xhtml .= <<<eom
<!-- files with geometry and styles structures -->
eom;
			$roleId = Watt_Session::getSession()->getRoleId();
			$crSessionRoleId = array(
				  '6b32ff50-df19-4e07-d50c-45b6b62bc171' => 'CR'		//说明这个是客户的角色ID
				//, '82109310-4a2e-bcb3-d919-45ffacdcf107' => 'QDKH'
				//, 'e48e869d-da50-ffb9-b086-45ffac2114ff' => 'CSKH'
				, '2798de2b-30bf-9dcb-22cd-45b6b68b315e' => 'TR'
				, '4ade1c61-fac6-8f11-4200-466fa0a2c627' => 'CR'			//都彼客户
				, '61c705eb-0cde-4867-3211-45b6b6753d4d' => 'PR'			//审校
				);
			if( Watt_Session::getSession()->isOutterUser()/*key_exists( $roleId, $crSessionRoleId )*/ ){
$xhtml .= <<<eom
<script language="JavaScript" src="{$this->_menu_lib_path}menu_cr_tpl.js"></script>
eom;
			}else{
$xhtml .= <<<eom
<script language="JavaScript" src="{$this->_menu_lib_path}menu_tpl.js"></script>
eom;
			}
			$xhtml .= <<<eom
<script language="JavaScript">
	<!--//
	// Note where menu initialization block is located in HTML document.
	// Don't try to position menu locating menu initialization block in
	// some table cell or other HTML element. Always put it before </body>

	// each menu gets two parameters (see demo files)
	// 1. items structure
	// 2. geometry structure

	new menu (MENU_ITEMS, MENU_POS);
	// make sure files containing definitions for these variables are linked to the document
	// if you got some javascript error like "MENU_POS is not defined", then you've made syntax
	// error in menu_tpl.js file or that file isn't linked properly.
	
	// also take a look at stylesheets loaded in header in order to set styles
	//-->
</script>
</div>	
eom;

			
		}
		if( $show )
		{
			echo $xhtml;
		}
		return $xhtml;
	}

	/**
	 * 
	 * @author y31
	 * Fri Aug 08 13:29:15 CST 2008
	 *
	 * @param array $data
	 * @return array
	 */
	public static function sortMenu( $data ){
		$menus = $data;
		$menu_arr = array();
		$hash_table = array();
		foreach ( $menus as $menu )
		{
			if( is_array( $menu ) ){
				$cd_id = $menu['cd_id'];
				$yemian=$menu['cd_chuliye'];
				$cd_chuliye = $yemian;
				$cd_shangji_id = $menu['cd_shangji_id'];
			}elseif( is_object( $menu ) ){
				$cd_id = $menu->getCdId();
				$yemian=$menu->getCdChuliye();
				$cd_chuliye = $yemian;
				$cd_shangji_id = $menu->getCdShangjiId();					
			}else{
				continue;
			}
			
			/**
			 * 使用 $hash_table[$cd_id] 的方式 
			 * 而不使用
			 * //$item_arr = array();
			 * //$hash_table[$cd_id] = $item_arr;
			 * 的方式是有目的，
			 * 因为 select 输出的menu 排序可能会把子菜单排在前边
			 */
											
			$hash_table[$cd_id][0] = Watt_I18n::trans( $menu->getCdMingcheng() );
			$hash_table[$cd_id][1] = $menu->getCdChuliye()?($_SERVER['PHP_SELF'].$cd_chuliye):"";
			//$hash_table[$cd_id][2] = null;
			/**
			 * 为了ext有个id
			 * @author y31
			 * Fri Aug 08 13:45:18 CST 2008
			 */
			$hash_table[$cd_id][2] = $menu->getCdId();
			
			if( $cd_shangji_id )
			{
				$shangji_id = $cd_shangji_id;
				/*
				if( !isset($hash_table[$shangji_id][3]) || !is_array( $hash_table[$shangji_id][3] ) )
				{
					//$hash_table[$shangji_id][3] = array();
				}
				*/
				//$hash_table[$shangji_id][3][] = &$hash_table[$cd_id];
				if( !isset( $hash_table[$shangji_id] ) )
				{
					$hash_table[$shangji_id][0] = null;
					$hash_table[$shangji_id][1] = null;
					$hash_table[$shangji_id][2] = null;
				}
				$hash_table[$shangji_id][] = &$hash_table[$cd_id];
			}
			else
			{
				$menu_arr[] = &$hash_table[$cd_id];
			}
		}
		return $menu_arr;
	}
	
	public static function buildMenuEx( $data, $show = true )
	{
		//没调好 先藏掉
		//Terry
		//return "";
		$xhtml = "";
		if( is_array( $data ) )
		{
			$menu_arr = self::sortMenu($data);
			$roleId = Watt_Session::getSession()->getRoleId();
			$crSessionRoleId = array(
				  '6b32ff50-df19-4e07-d50c-45b6b62bc171' => 'CR'		//说明这个是客户的角色ID
				//, '82109310-4a2e-bcb3-d919-45ffacdcf107' => 'QDKH'
				//, 'e48e869d-da50-ffb9-b086-45ffac2114ff' => 'CSKH'
				, '2798de2b-30bf-9dcb-22cd-45b6b68b315e' => 'TR'
				, '4ade1c61-fac6-8f11-4200-466fa0a2c627' => 'CR'			//都彼客户
				, '61c705eb-0cde-4867-3211-45b6b6753d4d' => 'PR'			//审校
				);
//			if( key_exists( $roleId, $crSessionRoleId ) ){
//				$xhtml .= "<link rel=\"stylesheet\" href=\"{$this->_menu_lib_path}xqtrmenu.css\">";
//			}else{
//				$xhtml .= "<link rel=\"stylesheet\" href=\"{$this->_menu_lib_path}xqmenu.css\">";
//			}

			$xhtml = '<div id="mainmenu" 
			style=""
			onmouseout="this.style.left=\'-118px\'" onmouseover="$(\'mainmenu\').style.left=\'0px\'">'."\n";
//			$xhtml .= '<ul>';
//			foreach ( $data as $key=>$val)
//			{
//				$xhtml .= '<li><a href="'.$val[1].'">'.$val[0].'</a></li>'."\n";
//			}
//			$xhtml .= '</ul>';
			
			// 结束
			if( Watt_Session::getSession()->getSession()->getRoleShortname() == 'CR' ){
				$xhtml .= "<div class='quick_order'>";
				if( Watt_Session::getSession()->isTq() ){
					$xhtml .= "<a TQCmd='CmdType=CallOrder' id='TQCmdTag_CallOrder' style='cursor:hand;' TQFileType=\"".Watt_I18n::trans("EC_PUBLIC_FILETYPES_LIST")."\">".Watt_I18n::trans('快速下单')."</a>";
				}else{
					//$xhtml .= "<a id='TQCmdTag_CallOrder' style='cursor:hand;' href='?do=ec_dingdan_add'\">".Watt_I18n::trans('快速下单')."</a>";
					$xhtml .= "<a id='TQCmdTag_CallOrder' style='cursor:hand;' href='?do=twftpm_start_startNewFlowAndExecute&lclx_id=16'\">".Watt_I18n::trans('快速下单')."</a>";
				}
				$xhtml .= '</div>';
			}
			$xhtml .= '<div class="cls"></div>'."\n";
			
			$xhtml .= '<div style="float:left;width:117px;background-color:#FFF;">'."\n";
			$xhtml .= self::_buildMenuArr( $menu_arr, 'menulist' );
			$xhtml .= '</div>'."\n";
			
			$xhtml .= '<div class="cls"></div>'."\n";
			$xhtml .= '</div>';
//			echo "<pre>Terry at [".__FILE__."(line:".__LINE__.")]\nWhen [Thu Jul 17 11:48:15 CST 2008] :\n ";
//			var_dump( $menu_arr );
//			echo "</pre>";
//			exit();
			
		}
		
		if( $show )
		{
			echo $xhtml;
		}
		return $xhtml;
	}
	
	private static function _buildMenuArr( $menuArr, $menuItemId='' ){
//		echo "<pre>Terry at [".__FILE__."(line:".__LINE__.")]\nWhen [Thu Jul 17 12:06:20 CST 2008] :\n ";
//		var_dump( $menuArr );
//		echo "</pre>";
//		exit();
		
		$rev = '';
		$rev .= '<div id="'.$menuItemId.'">'."\n";
		if( is_array( $menuArr ) ){
			foreach ($menuArr as $menuItem) {
				if( !is_array( $menuItem ) ){
					continue;
				}
//				echo "<pre>Terry at [".__FILE__."(line:".__LINE__.")]\nWhen [Thu Jul 17 17:51:17 CST 2008] :\n ";
//				var_dump( $menuItem );
//				echo "</pre>";
				//exit();
				
				$rev .= self::_buildMenuItem( $menuItem );
			}
		}
		$rev .= '</div>'."\n";
	
		return $rev;
	}
	
	private static function _buildMenuItem( $menuItem, $level=0 ){
		$rev = '';
		if( count( $menuItem ) > 3 ){
			$subMenuId = 'sub_'.md5( serialize($menuItem) );
			$rev .= '<div onmouseover="Element.show(\''.$subMenuId.'\');this.onmouseover=null;" 
			onclick="Element.toggle(\''.$subMenuId.'\')" 
			style="cursor:pointer;text-align:center;background-color:#69C;color:#FFF;border-top:1px solid #FFF;"
			class="menuitem_l'.$level.'">'."\n [+]";
		}else{
			if( 0 == $level ){
				$rev .= '<div style="text-align:center;background-color:#69C;color:#FFF;border-top:1px solid #FFF;" class="menuitem_l'.$level.'">'."\n";
			}else{
				$rev .= '<div style="text-align:center;background-color:#FEC;color:#FFF;border-top:1px solid #FFF;" class="menuitem_l'.$level.'">'."\n";				
			}
		}
		if( $menuItem[1] ){
			$rev .= "<a href=\"{$menuItem[1]}&aname=".urlencode($menuItem[0])."\">{$menuItem[0]}</a>";
		}else{
			$rev .= $menuItem[0];
		}
		$rev .= '</div>'."\n";
		if( count( $menuItem ) > 3 ){
			$rev .= '<div id="'.$subMenuId.'" style="display:none;border:1px solid #666;">'."\n";
			for ( $i=3;$i<count( $menuItem );$i++ ){
				$rev .= self::_buildMenuItem( $menuItem[$i], ++$level );
			}
			$rev .= '</div>'."\n";
		}
		return $rev;
	}
}