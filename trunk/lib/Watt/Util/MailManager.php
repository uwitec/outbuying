<?
require_once('../Exception.php');
//邮件模板管理类
class MailManager
{
	//添加邮件模板
	public static function add($arr)
	{
		if(is_array($arr)&&count($arr))
		{
			$str="";
			foreach ($arr as $key=>$val)
			{
				
			}
		}
		else 
		{
			return false;
		}
	}
}

?>