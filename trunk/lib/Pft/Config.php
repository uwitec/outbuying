<?
/**
 * Pft基础类库
 *
 * @author Terry
 * @package Pft
 */

/**
 * 配置管理类
 * 
 * <p>所有的配置信息从这个类中获得</p>
 * 如
 * <code>
 * $appPaht = Pft_Config::getAppPath();
 * </code>
 * 如果想要获得次要配置，则在 getXxxxx() 里输入参数 1 
 *
 * @author Terry
 * @package Pft
 */
class Pft_Config{
	/**
	 * 记录主要配置
	 *
	 * @var array
	 */
	private static $_priCfg;
	/**
	 * 记录次要配置
	 *
	 * @var array
	 */
	private static $_secCfg;
	
	/**
	 * 设置首要config
	 *
	 * @param array $config
	 */
	public static function setPrimaryConfig( $config )
	{
		self::$_priCfg = $config;
	}

	/**
	 * 设置次要config
	 *
	 * @param array $config
	 */
	public static function setSecondaryConfig( $config )
	{
		self::$_secCfg = $config;
	}
	
	/**
	 * 所有 文件配置存储的位置
	 *
	 * @var array
	 */
	private static $_configFiles = array();
	
	/**
	 * 获得主db的配置
	 *
	 * @param int $secondary
	 * @return unknown
	 */
	public static function getMainDbConfig( $secondary = 0 )
	{
		//未来可根据不同的登录用户获取不同的db配置
		//return $db = include( PATH_CONFIG."db.cfg.php" );
		return include( self::getConfigPath($secondary)."db.cfg.php" );
	}

	/**
	 * 获得 程序根目录 PATH_ROOT
	 *
	 * @param integer $secondary
	 * @return string
	 */
	public static function getRootPath( $secondary = 0 )
	{
		return self::_getCfgByName( "PATH_ROOT", $secondary );
	}

	/**
	 * 获得 App所在的目录 PATH_APP
	 *
	 * @param integer $secondary
	 * @return string
	 */
	public static function getAppPath( $secondary = 0 )
	{
		return self::_getCfgByName( "PATH_APP", $secondary );
	}

	/**
	 * 获得 语言文件所在的目录 PATH_LANGUAGE
	 *
	 * @param integer $secondary
	 * @return string
	 */
	public static function getLangPath( $secondary = 0 )
	{
		return self::_getCfgByName( "PATH_LANGUAGE", $secondary );
	}

	/**
	 * 获得 Lib文件所在的目录 PATH_LIB
	 *
	 * @param integer $secondary
	 * @return string
	 */
	public static function getLibPath( $secondary = 0 )
	{
		return self::_getCfgByName( "PATH_LIB", $secondary );
	}

	/**
	 * 获得 配置文件所在的目录 PATH_CONFIG
	 *
	 * @param integer $secondary
	 * @return string
	 */
	public static function getConfigPath( $secondary = 0 )
	{
		//这是为了多站点支持
		if( defined( 'MULTI_SITE_SESSION_NAME' ) && isset( $_SESSION[MULTI_SITE_SESSION_NAME] ) ){
			$site = $_SESSION[MULTI_SITE_SESSION_NAME];
		}else{
			//保持兼容
			$site = 'default';
		}
		if( $site ){
			return self::_getCfgByName( "PATH_CONFIG", $secondary ).$site.'/';
		}else{
			return self::_getCfgByName( "PATH_CONFIG", $secondary );			
		}
	}

	/**
	 * 获得 View 的目录 PATH_VIEW
	 *
	 * @param integer $secondary
	 * @return string
	 */
	public static function getViewPath( $secondary = 0 )
	{
		return self::_getCfgByName( "PATH_VIEW", $secondary );
	}

	/**
	 * 获得 OM model所在的目录 PATH_MODEL
	 *
	 * @param integer $secondary
	 * @return string
	 */
	public static function getModelPath( $secondary = 0 )
	{
		return self::_getCfgByName( "PATH_MODEL", $secondary );
	}

	/**
	 * 获得 propel 的数据配置文件
	 *
	 * @param integer $secondary
	 * @return string
	 */
	public static function getPropelConfFilename( $secondary = 0 )
	{
		return self::getConfigPath( $secondary )."propel.conf.php";
	}

	/**
	 * 获得 站点根目录 SITE_ROOT
	 *
	 * @param int $secondary
	 * @return string
	 */
	public static function getSiteRoot( $secondary = 0 )
	{
		return self::_getCfgByName( "SITE_ROOT", $secondary );
	}

	/**
	 * 获得 日志所在目录 PATH_LOG
	 *
	 * @param integer $secondary
	 * @return string
	 */
	public static function getLogPath( $secondary = 0 )
	{
		return self::_getCfgByName( "PATH_LOG", $secondary );
	}
	
	/**
	 * 获得上传文件的目录
	 *
	 * @param integer $secondary
	 * @return string
	 */
	public static function getUploadPath( $secondary = 0 )
	{
		return self::_getCfgByName( "PATH_UPLOAD", $secondary );
	}

	/**
	 * 返回 http://a.b.c 形式的主机地址
	 *
	 * @return string
	 */
	public static function getHttpHost()
	{
		return "http://".$_SERVER['HTTP_HOST'].self::getSiteRoot();
	}
	
	/**
	 * 返回去除in的 http://a.b.c 形式的主机地址
	 * http://wd0.transn.net:8082/debug.php?do=test_testhost
	 * 
	 * @return string
	 */
	public static function getHttpHostOut()
	{
		$httpHost = self::getHttpHost();
		return str_replace( "http://in", "http://", $httpHost);
	}
	
	public static function getPftServer( $secondary = 0 ){return self::_getCfgByName( "WATTSERVER", $secondary );}
/**
 * EpollServer=Epoll服务器地址:
     EpollServerPort=Epoll服务器端口:
     EpollHeartBeat=心跳时间:
     FtpServer=Ftp服务器地址:
     FtpServerPort=Ftp服务器端口:
     FtpUserName=Ftp用户名:
     FtpPassword=Ftp密码
     FtpDir=ftp路径
 * 
 * 
 */
	
	public static function getEpollServer( $secondary = 0 ){return self::_getCfgByName( "EpollServer", $secondary );}
	public static function getEpollServerPort( $secondary = 0 ){return self::_getCfgByName( "EpollServerPort", $secondary );}
	public static function getEpollHeartBeat( $secondary = 0 ){return self::_getCfgByName( "EpollHeartBeat", $secondary );}
	public static function getFtpServer( $secondary = 0 ){return self::_getCfgByName( "TQ_FTP_URL", $secondary );}
	public static function getFtpServerPort( $secondary = 0 ){return self::_getCfgByName( "TQ_FTP_PORT", $secondary );}
	public static function getFtpUserName( $secondary = 0 ){return self::_getCfgByName( "TQ_FTP_NAME", $secondary );}
	public static function getFtpPassword( $secondary = 0 ){return self::_getCfgByName( "TQ_FTP_PW", $secondary );}
	public static function getFtpDir( $secondary = 0 ){return self::_getCfgByName( "TQ_FTP_DIR", $secondary );}
	public static function getEpollGroupId( $secondary = 0 ){return self::_getCfgByName( "EPOLL_GROUP_ID", $secondary );}
	
	public static function getLoginServer( $secondary = 0 ){return self::_getCfgByName( "LOGIN_SERVER", $secondary );}
	
	public static function getCfg( $cfgName, $secondary = 0 )
	{
		return self::_getCfgByName( $cfgName, $secondary );
	}
	
	/**
	 * 自动选择路径加文件
	 * 规则是：
	 * 先从 pri 配置中寻找文件，如果存在，则选择这个路径，
	 * 如果不存在，则从 sce 中找，依次类推。
	 * 在最后一组配置时不判断文件是否存在，直接选择路径。
	 * 返回值是 路径加文件的完整文件名
	 * 
	 *
	 * @param string $sysPathName 系统路径的名称，如 PATH_APP
	 * @param string $relPathFilename 相对路径文件名
	 * @return string 完整的路径文件名
	 */
	public static function getAbsPathFilename( $sysPathName, $relPathFilename )
	{
		$pathFile = self::$_priCfg[$sysPathName].$relPathFilename;
		if ( is_array(self::$_secCfg) && !Pft::isReadable( $pathFile ) )
		{
			$pathFile = self::$_secCfg[$sysPathName].$relPathFilename;
		}
		return $pathFile;
	}
	
	/**
	 * 从配置文件中读取配置数组
	 *
	 * @param string $cfgFilename 相对配置目录的配置文件名称，开始不要带/
	 */
	public static function getCfgFromFile( $cfgFilename ){
		$realPathname = self::getConfigPath().ltrim($cfgFilename,"/");
		return include($realPathname);
	}
	
	/**
	 * 如果用备用配置，将 $secondary 设为 1
	 * 这里用 int 而非 boolean，是为了使用多个配置作准备
	 *
	 * @param string $cfgName
	 * @param int $secondary
	 * @return mix
	 */
	private static function _getCfgByName( $cfgName, $secondary = 0 )
	{
		if( $secondary > 0 )
		{
			if( is_array( self::$_secCfg ) )
			{
				return isset(self::$_secCfg[$cfgName])?self::$_secCfg[$cfgName]:null;
			}
			else
			{
				//return null;
				return isset(self::$_priCfg[$cfgName])?self::$_priCfg[$cfgName]:null;
			}
		}
		else
		{
			return isset(self::$_priCfg[$cfgName])?self::$_priCfg[$cfgName]:null;
		}
	}
	
	/**
	 * 获得默认的组id
	 * 企业管理员对用户注册的组进行设置。
	 * 或者直接取根组
	 * 
	 * @todo ->从配置里取
	 */
	public static function getDefaultZuId(){
		return 1;
	}

	/**
	 * 获得权限相关的sql条件。
	 * 外边需要用AND来做。
	 *
	 * @param string $tablename
	 * @return string
	 */
	public static function getCond( $tablename='' ){
//		if( $tablename ){
//			return ( " $tablename.ZU_ID = 'xxxxxx'" );
//		}else{
//			return ( " ZU_ID = 'xxxxxx'" );
//		}
		//return ( $tablename.ZU_ID = 'xxxxxx' )
		
		$zu_id = Pft_Session::getSession()->getGroupId();
		
		//return " (1=1) ";
		if( $tablename )$tablename = $tablename.'.';
		return " ({$tablename}shifoushanchu='n' and ( {$tablename}ZU_ID='$zu_id' or {$tablename}ZU_ID is null or {$tablename}ZU_ID = '') ) ";
	}
	/**
	 * 格式化输出
	 *
	 * @param unknown_type $string
	 * @param unknown_type $option
	 * @return unknown
	 */
	public static function sechof($string,$option=false)
	{
		$num=2;
		if($option)
		{
			$num=$option;
		}
		return number_format($string,$num,'.',',');
	}
	/**
	 * 读取指定配置文件的配置
	 *
	 * @param string $cfgName  配置项名称
	 * @param string $fileName 配置文件名
	 * @param int $secondary 是否备用
	 * @return mix
	 */
	public static function getCfgInFile( $cfgName, $fileName='', $secondary = 0 ){
		if( !$fileName ){
			return self::_getCfgByName( $cfgName, $secondary );
		}
		
		//$fileKey = self::_formatConfFileName( $fileName );
		if( !key_exists( $fileName, self::$_configFiles ) ){
			self::$_configFiles[$fileName] = @include( self::getConfigPath( $secondary ).$fileName );
		}
		if( $cfgName ){
			return self::$_configFiles[$fileName][$cfgName];
		}else{
			return self::$_configFiles[$fileName];
		}
	}
	
	private static function _formatConfFileName( $filename ){
		return base64_encode( $filename );
	}
	
	/**
	 * 加载配置文件
	 * @param string $fileName
	 * @author y31
	 * Mon Mar 16 21:38:36 CST 2009
	 */
	public static function loadCfgFile( $fileName ){
		if( !key_exists( $fileName, self::$_configFiles ) ){
			self::$_configFiles[$fileName] = @include( self::getConfigPath( $secondary ).$fileName );
		}
		return self::$_configFiles[$fileName];
	}
}