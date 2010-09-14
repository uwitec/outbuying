<?php
/**
 * @desc Log.Class.php
 * @author Marty
 * @version Fri Jan 05 20:56:13 CST 2007 20:56:13 ??��?�确�?
 */
class Watt_LogM
{
	private static $logFileName;
	private static $logFp;
	
	private static $logArray = array();
	
	public static $tureOn = false;
	public function __construct()
	{
		;
	}
	private static function openLogFile()
	{
		if (!self::$logFileName)
		{
			$time = microtime(true);
			self::$logFileName = substr(($time - floor($time)), 2).'_'.basename($_SERVER['PHP_SELF']).'.log';
		}
		
		if (!file_exists("log") || !is_dir("log"))
		{
			mkdir("log");
		}
		else
		{
			/**
			 * @desc 自动清日志
			 * @author Marty
			 * @version Mon Jan 22 15:03:21 CST 2007 15:03:21 更新确认
			 */
/*			if ($dh = opendir("log")) {
				$files = array();
				while (($file = readdir($dh)) !== false) {
					if ($file != '.' && $file != '..')
					{
						$files[] = $file;
					}
				}
				closedir($dh);
			}
			if (count($files > 5))
			{
				foreach ($files as $file)
				{
					//unlink("log/".$file);
				}
			}*/
		}
		
		if (self::$logFp = fopen("log/".self::$logFileName, 'a'))
		{
			chmod("log/".self::$logFileName, 0777);
			self::addLog("system", "=================== open ".$_SERVER['PHP_SELF']." logfile success ===================");
		}
	}
	public static function turnOn()
	{
		self::$tureOn = true;
	}
	public static function turnOff()
	{
		self::$tureOn = false;
	}
	public static function addLog($type, $message)
	{
		if (self::$tureOn)
		{
			$time = microtime(true);
			self::$logArray[] = array('ip' => $_SERVER['REMOTE_ADDR'],
				'date' => date('Y-m-d H:i:s'),
				'time' => $time - floor($time),
				'type' => $type,
				'mesg' => print_r($message, 1)
			);
		}
	}
	public static function addTplVar($tplName, $tplValue)
	{
		if (self::$tureOn)
		{
			$time = microtime(true);
			self::$logArray[] = array('ip' => $_SERVER['REMOTE_ADDR'],
				'date' => date('Y-m-d H:i:s'),
				'time' => $time - floor($time),
				'type' => 'smarty',
				'name' => $tplName,
				'mesg' => print_r($tplValue, 1)
			);
		}
	}
	public static function display($type = '')
	{
		if (self::$tureOn)
		{
			if ($_REQUEST['debug'] == '')
			{
			header("Content-Type:text/html;charset=utf-8");
			echo '<pre>';
			}
			foreach (self::$logArray as $array)
			{
				if ($type)
				{
					if ($type == $array['type'])
					{
						if ('smarty' == $array['type'])
						{
							echo sprintf("[%s] [%s] \t [%s] \t [%s] %s\n",
								$array['ip'],
								$array['time'],
								$array['type'],
								$array['name'],
								$array['mesg']
							);
						}
						else
						{
							echo sprintf("[%s] [%s] \t [%s] \t %s\n",
								$array['ip'],
								$array['time'],
								$array['type'],
								$array['mesg']
							);
						}
					}
				}
				else
				{
					echo sprintf("[%s] [%s] \t [%s] \t %s\n",
						$array['ip'],
						$array['time'],
						$array['type'],
						$array['mesg']
					);
				}
			}
		}
	}
	public static function save()
	{
		ob_start();
		self::display();
		$ob_content = ob_get_clean();
		$fp = fopen('log/'.basename($_SERVER['PHP_SELF']).','.microtime(true).'.log', 'w');
		fwrite($fp, $ob_content);
		fclose($fp);
	}

}
?>