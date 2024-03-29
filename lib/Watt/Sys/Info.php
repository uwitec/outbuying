<?php
class Watt_Sys_Info{
	/**
	* 获得浏览器名称和版本
	*
	* @access public
	* @return string
	*/
	public static function getBrowser()
	{
		//global $_SERVER;
		$agent           = $_SERVER['HTTP_USER_AGENT'];
		$browser       = '';
		$browser_ver     = '';

		if (preg_match('/OmniWeb\/(v*)([^\s|;]+)/i', $agent, $regs))
		{
			$browser       = 'OmniWeb';
			$browser_ver     = $regs[2];
		}

		if (preg_match('/Netscape([\d]*)\/([^\s]+)/i', $agent, $regs))
		{
			$browser       = 'Netscape';
			$browser_ver     = $regs[2];
		}

		if (preg_match('/safari\/([^\s]+)/i', $agent, $regs))
		{
			$browser       = 'Safari';
			$browser_ver     = $regs[1];
		}

		if (preg_match('/MSIE\s([^\s|;]+)/i', $agent, $regs))
		{
			$browser       = 'Internet Explorer';
			$browser_ver     = $regs[1];
		}

		if (preg_match('/Opera[\s|\/]([^\s]+)/i', $agent, $regs))
		{
			$browser       = 'Opera';
			$browser_ver     = $regs[1];
		}

		if (preg_match('/NetCaptor\s([^\s|;]+)/i', $agent, $regs))
		{
			$browser       = '(Internet Explorer ' .$browser_ver. ') NetCaptor';
			$browser_ver     = $regs[1];
		}

		if (preg_match('/Maxthon/i', $agent, $regs))
		{
			$browser       = '(Internet Explorer ' .$browser_ver. ') Maxthon';
			$browser_ver     = '';
		}

		if (preg_match('/FireFox\/([^\s]+)/i', $agent, $regs))
		{
			$browser       = 'FireFox';
			$browser_ver     = $regs[1];
		}

		if (preg_match('/Lynx\/([^\s]+)/i', $agent, $regs))
		{
			$browser       = 'Lynx';
			$browser_ver     = $regs[1];
		}

		if ($browser != '')
		{
			return $browser.' '.$browser_ver;
		}
		else
		{
			return 'Unknow browser';
		}
	}

	/**
	* 获得客户端的操作系统
	*
	* @access private
	* @return string
	*/
	public static function getOs()
	{
		$agent = $_SERVER['HTTP_USER_AGENT'];
		$os = false;

		if (eregi('win', $agent) && strpos($agent, '95'))
		{
			$os = 'Windows 95';
		}
		else if (eregi('win 9x', $agent) && strpos($agent, '4.90'))
		{
			$os = 'Windows ME';
		}
		else if (eregi('win', $agent) && ereg('98', $agent))
		{
			$os = 'Windows 98';
		}
		else if (eregi('win', $agent) && eregi('nt 5.1', $agent))
		{
			$os = 'Windows XP';
		}
		else if (eregi('win', $agent) && eregi('nt 5', $agent))
		{
			$os = 'Windows 2000';
		}
		else if (eregi('win', $agent) && eregi('nt', $agent))
		{
			$os = 'Windows NT';
		}
		else if (eregi('win', $agent) && ereg('32', $agent))
		{
			$os = 'Windows 32';
		}
		else if (eregi('linux', $agent))
		{
			$os = 'Linux';
		}
		else if (eregi('unix', $agent))
		{
			$os = 'Unix';
		}
		else if (eregi('sun', $agent) && eregi('os', $agent))
		{
			$os = 'SunOS';
		}
		else if (eregi('ibm', $agent) && eregi('os', $agent))
		{
			$os = 'IBM OS/2';
		}
		else if (eregi('Mac', $agent) && eregi('PC', $agent))
		{
			$os = 'Macintosh';
		}
		else if (eregi('PowerPC', $agent))
		{
			$os = 'PowerPC';
		}
		else if (eregi('AIX', $agent))
		{
			$os = 'AIX';
		}
		else if (eregi('HPUX', $agent))
		{
			$os = 'HPUX';
		}
		else if (eregi('NetBSD', $agent))
		{
			$os = 'NetBSD';
		}
		else if (eregi('BSD', $agent))
		{
			$os = 'BSD';
		}
		else if (ereg('OSF1', $agent))
		{
			$os = 'OSF1';
		}
		else if (ereg('IRIX', $agent))
		{
			$os = 'IRIX';
		}
		else if (eregi('FreeBSD', $agent))
		{
			$os = 'FreeBSD';
		}
		else if (eregi('teleport', $agent))
		{
			$os = 'teleport';
		}
		else if (eregi('flashget', $agent))
		{
			$os = 'flashget';
		}
		else if (eregi('webzip', $agent))
		{
			$os = 'webzip';
		}
		else if (eregi('offline', $agent))
		{
			$os = 'offline';
		}
		else
		{
			$os = 'Unknown';
		}
		return $os;
	}
	
	public static function test(){
		echo self::getBrowser();
		echo self::getOs();
	}
}

Watt_Sys_Info::test();