<?
/**
 * 这是对Watt Grid进行显示的类
 *
 * @author Terry
 * @package Pft_View_Helper
 */

class Pft_View_Helper_TestGrid
{
	/**
	 * 根据符合Watt:Data里的 Grid 的Schema 的数据创建一个HTML的 Grid显示
	 *
	 * @param unknown_type $gridData
	 */
	public static function buildGrid( $grid, $show=true )
	{
		//这里判断数据是否符合规则
		//将来要根据 Shema 判断
		if( !is_array( $grid ) )
		{
			$e = new Pft_Exception(Pft_I18n::trans("ERR_INVALID_DATATYPE"));
			throw $e;
		}

		if( isset( $grid[ Pft_Util_Grid::GRID_COLS ] )
		 && count( $grid[ Pft_Util_Grid::GRID_COLS ] ) > 0 )
		{
			$isDefCols = true;
			$cols = $grid[ Pft_Util_Grid::GRID_COLS ];
		}else{
			$isDefCols = false;
		}

		$datas = $grid[ Pft_Util_Grid::GRID_DATAS ];
		$out = '<table border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#CCCCCC">';
		if( $isDefCols )
		{
			$out .= "<tr>";
			foreach ( $cols as $col )
			{
				$out .= "<th class=GridTH>".$col["title"]."</th>";
			}
			$out .= "</tr>\n";
		}
		
		foreach ( $datas as $row )
		{
			$out .= "<tr>";
			if( $isDefCols )
			{
				reset( $cols );
				foreach ( $cols as $col )
				{
					if( isset($col["isext"]) && $col["isext"] ){
						if( isset($col["render"]) && trim($col["render"]) != ""  )
						{
							//每个row都分析了一次render，这里要优化！！
							preg_match_all( "/\{([\\w]+)\}/i", $col["render"], $matchs );
							if( is_array( $matchs[1] ) ){
								$tmpLink = $col["render"];
								foreach ( $matchs[1] as $tmpColName ){
									$tmpLink = str_replace( "{".$tmpColName."}", $row[$tmpColName], $tmpLink);
								}
								$showText = $tmpLink;
							}
						}
					}
					else
					{
						$orignText = $row[$col["colname"]];
						$showText = $orignText;
						if( trim( $orignText ) != "" ){
							//只有 $colContent 有值才处理
							
							//处理回调函数
							if( isset($col["callback"]) && trim($col["callback"]) != "" ){
								$callBack = str_replace( "{me}", $orignText, $col["callback"]);
								eval( "\$showText=$callBack;" );
							}
							
							if( isset($col["render"]) && trim($col["render"]) != ""  )
							{
								//每个row都分析了一次render，这里要优化！！
								preg_match_all( "/\{([\\w]+)\}/i", $col["render"], $matchs );
								if( is_array( $matchs[1] ) ){
									$tmpLink = $col["render"];
									foreach ( $matchs[1] as $tmpColName ){
										$tmpLink = str_replace( "{".$tmpColName."}", $row[$tmpColName], $tmpLink);
									}
									$showText = $tmpLink;
								}
							}					
							elseif( isset($col["linkto"]) && trim($col["linkto"]) != "" )
							{
							//有了render，就不用linkto了..
							//处理链接
							//此处与HTML联系，现在也想不清楚了，将来再改进吧..
	
								//echo __FILE__.__LINE__.$col["linkto"];
								preg_match_all( "/\{([\\w]+)\}/i", $col["linkto"], $matchs );
								if( is_array( $matchs[1] ) ){
									$tmpLink = $col["linkto"];
									foreach ( $matchs[1] as $tmpColName ){
										$tmpLink = str_replace( "{".$tmpColName."}", $row[$tmpColName], $tmpLink);
									}
									$showText = "<a href=\"$tmpLink\">".$showText."</a>";
								}
							}
						}
					}
					$out .= "<td>".$showText."</td>";					
				}
			}
			else
			{
				foreach ( $row as $col )
				{
					$out .= "<td>". $col ."</td>";
				}
			}
			$out .= "</tr>\n";
		}
		$out .= "</table>\n";
		
		if( $show )echo $out;
		return $out;
	}
}