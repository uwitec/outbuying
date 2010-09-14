<?
/**
 * 专为TPM 外网to内网准备的过滤器
 *
 */
class Watt_Sync_Filter_TpmOutToIn extends Watt_Sync_Filter{
	/**
	 * 过滤
	 *
	 * @param array $data
	 * @return $data
	 */
	public function filter( $data ){
		//调用配置
		$config = Watt_Config::getCfgFromFile('sync/TpmOutToIn.filter.conf.php');		
		//var_dump($config);
		//白名单过滤,删除白名单里不存在的
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
		if (isset($config['blackList'][$data['tableName']]) && count($config['blackList'][$data['tableName']]))
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