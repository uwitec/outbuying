<?
/**
 * 针对yui-ext的助手
 *
 */
class Pft_View_Helper_Ext{
	private static $_loadedTableGridLib = false;
	
	/**
	 * 使用yui-ext建立TableGrid
	 * 
	 * <code>
	 * $grid->showMe( array('gridId'=>'sql_grid') );
	 * Pft_View_Helper_Ext::buildTableGrid( 'sql_grid' );
	 * </code>
	 *
	 * @param string $gridId grid的ID
	 */
	public static function buildTableGrid( $gridId ){
		if( !self::$_loadedTableGridLib ){
?>
<script src="<?=Pft_Config::getSiteRoot()?>js/ext-1.0/tpm/TableGrid.js"></script>
<?
			self::$_loadedTableGridLib = true;
		}
?>
<script>
/*
 * Ext JS Library 1.0
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */
Ext.onReady(function() {
	// create the grid
    var grid = new Ext.grid.TableGrid('<?=$gridId?>');
    grid.render();
});
</script>
<?
	}	
}