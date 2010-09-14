<?
/**
 * 专为TPM 内网to外网准备的
 *
 */
class Watt_Sync_Filter_TpmInToOut extends Watt_Sync_Filter{
	/**
	 * 过滤
	 *
	 * @param array $data
	 * @return $data
	 */
	public function filter( $data ){
		//调用配置
		$config = Watt_Config::getCfgFromFile('sync/TpmInToOut.filter.conf.php');		
		//var_dump($config);
		//白名单过滤,删除白名单里不存在的		
		
		if (!isset($config['whiteList']) || (isset($config['whiteList']) && !count($config['whiteList'])))
		{//不存在白名单或白名单为空，将不进行白名单过滤
			
		}else if (isset($config['whiteList'][$data['tableName']]) && is_array($config['whiteList'][$data['tableName']]) && !count($config['whiteList'][$data['tableName']]))
		{//白名单中设置该表的值是空数组，表示该表的所有数据都允许，白名单过滤不执行任何操作
		}
		else if (isset($config['whiteList']) && count($config['whiteList']) && !isset($config['whiteList'][$data['tableName']]))
		{//白名单里不存在该表名
			return array();
		}
		else if (isset($config['whiteList'][$data['tableName']]) && count($config['whiteList'][$data['tableName']]))
		{
			if(isset($data['cols']) && is_array($data['cols']) && count($data['cols']))
			{
				foreach ($data['cols'] as $col => $val){
					$is_del = true;
					foreach ($config['whiteList'][$data['tableName']] as $col_){
						if ($col == strtoupper($col_)){
							$is_del = false;
						}
					}
					if ($is_del)
					{
						//echo "white:".$col."<br>";
						//删除
						unset($data['cols'][$col]);
					}
				}
			}
		}
		
		/*if(isset($data['cols']) && is_array($data['cols']) && count($data['cols']))
		{
			foreach ($data['cols'] as $col => $val){
				$is_del = true;
				if (isset($config['whiteList'][$data['tableName']]) && count($config['whiteList'][$data['tableName']]))
				{
					foreach ($config['whiteList'][$data['tableName']] as $col_){
						if ($col == strtoupper($col_)){
							$is_del = false;
						}
					}
				}
				if ($is_del)
				{
					//echo "white:".$col."<br>";
					//删除
					unset($data['cols'][$col]);
				}
			}
		}*/		
		//黑名单过滤,删除黑名单里存在的
		if (isset($config['blackList'][$data['tableName']]) && !count($config['blackList'][$data['tableName']]))
		{//如果黑名单中存在该表，并且该表的设为空数组
			return array();			
		}else if (isset($config['blackList'][$data['tableName']]) && count($config['blackList'][$data['tableName']]))
		{
			foreach ($config['blackList'][$data['tableName']] as $col_){
				if(isset($data['cols'][strtoupper($col_)])){
					//echo "black:".strtoupper($col_)."<br>";
					//删除
					unset($data['cols'][strtoupper($col_)]);
				}
			}
		}
		return $data;
	}
}