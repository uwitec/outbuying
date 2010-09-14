<?
/**
 * 为了少Include一些文件，从 Zend Framework 复制过来
 * 主要用于基本管理
 *
 * @package    Pft
 */

/**
 * Pft_Exception
 */
//require_once 'Pft/Exception.php';

/**
 *
 * @category   Pft
 * @package    Pft
 */
final class Pft
{
    /**
     * Object registry provides storage for shared objects
     * @var array
     */
    static private $_registry = array();


    /**
     * Singleton Pattern
     */
    private function __construct()
    {}


    /**
     * Loads a class from a PHP file.  The filename must be formatted
     * as "$class.php".
     *
     * If $dirs is a string or an array, it will search the directories
     * in the order supplied, and attempt to load the first matching file.
     *
     * If $dirs is null, it will split the class name at underscores to
     * generate a path hierarchy (e.g., "Zend_Example_Class" will map
     * to "Zend/Example/Class.php").
     *
     * If the file was not found in the $dirs, or if no $dirs were specified,
     * it will attempt to load it from PHP's include_path.
     *
     * @param string $class
     * @param string $dirs
     * @throws Pft_Exception
     * @return void
     */
    static public function loadClass($class, $dirs = null)
    {
		if (class_exists($class, false)) {
            return;
        }

		//Terry Add  查看到底load了多少个类
		/*
		if( defined("DEBUG") && DEBUG && $class != "Pft_Debug" )
		{
			Pft_Debug::addInfoToDefault( "Pft", "Before Loaded Class [ $class ]." );
		}
		//-------------------------------
		*/

		if( strtoupper( $class ) == "PROPEL"
		 || strtoupper( $class ) == "CRITERIA" )
		{
			self::preLoadPropelClasses();
		}
		else
		{
			//这里是原来的load class内容
			
			// autodiscover the path from the class name
			$path = str_replace('_', DIRECTORY_SEPARATOR, $class);
			if ($dirs === null && $path != $class) {
				// use the autodiscovered path
				$dirs = dirname($path);
				$file = basename($path) . '.php';
			} else {
				$file = $class . '.php';
			}

			self::loadFile($file, $dirs, true);

			if (!class_exists($class, false)) {
				/*
				throw new Pft_Exception("File \"$file\" was loaded "
								   . "but class \"$class\" was not found within.");
				*/
				throw new Exception("File \"$file\" was loaded "
								   . "but class \"$class\" was not found within.");
			}
		}
		//Terry Add  查看到底load了多少个类
		/*
		if( defined("DEBUG") && DEBUG && $class != "Pft_Debug" )
		{
			Pft_Debug::addInfoToDefault( "Pft", "After Loaded Class [ $class ]." );
		}
		//-------------------------------
		*/
	}


    /**
     * Loads an interface from a PHP file.  The filename must be formatted
     * as "$interface.php".
     *
     * If $dirs is a string or an array, it will search the directories
     * in the order supplied, and attempt to load the first matching file.
     *
     * If $dirs is null, it will split the interface name at underscores to
     * generate a path hierarchy (e.g., "Pft_Example_Interface" will map
     * to "Pft/Example/Interface.php").
     *
     * If the file was not found in the $dirs, or if no $dirs were specified,
     * it will attempt to load it from PHP's include_path.
     *
     * @param string $interface
     * @param string $dirs
     * @throws Pft_Exception
     * @return void
     */
    static public function loadInterface($interface, $dirs = null)
    {
        if (interface_exists($interface, false)) {
            return;
        }

        // autodiscover the path from the interface name
        $path = str_replace('_', DIRECTORY_SEPARATOR, $interface);
        if ($dirs === null && $path != $interface) {
            // use the autodiscovered path
            $dirs = dirname($path);
            $file = basename($path) . '.php';
        } else {
            $file = $interface . '.php';
        }

        self::loadFile($file, $dirs, true);

        if (!interface_exists($interface, false)) {
            throw new Pft_Exception("File \"$file\" was loaded "
                               . "but interface \"$interface\" was not found within.");
        }
    }


    /**
     * Loads a PHP file.  This is a wrapper for PHP's include() function.
     *
     * $filename must be the complete filename, including any
     * extension such as ".php".  Note that a security check is performed that
     * does not permit extended characters in the filename.  This method is
     * intended for loading Pft Framework files.
     *
     * If $dirs is a string or an array, it will search the directories
     * in the order supplied, and attempt to load the first matching file.
     *
     * If the file was not found in the $dirs, or if no $dirs were specified,
     * it will attempt to load it from PHP's include_path.
     *
     * If $once is TRUE, it will use include_once() instead of include().
     *
     * @param  string        $filename
     * @param  string|null   $directory
     * @param  boolean       $once
     * @throws Exception
     * @return void
     */
    static public function loadFile($filename, $dirs=null, $once=false)
    {
        // security check
        //if (preg_match('/[^a-z0-9\-_.]/i', $filename)) {
        /*搞不定这个check 注释掉 Terry
        if (preg_match('/[^a-z0-9\-_.\\\\\\/\\~]/i', $filename)) {
        	//因为本系统中action文件可能是带 / \ 的，所以这里改写了
        	//上面是原来的样子
        	//Terry
        	//echo "loadFilefail:".$filename."\n";
            throw (new Pft_Exception("Security check: Illegal character in filename"));
        }
    	*/
        
        /**
         * Determine if the file is readable, either within just the include_path
         * or within the $dirs search list.
         */
        $filespec = $filename;

        if ($dirs === null) {
            $found = self::isReadable($filespec);
        } else {
            foreach ((array)$dirs as $dir) {
                $filespec = rtrim($dir, '\\/') . DIRECTORY_SEPARATOR . $filename;
                $found = self::isReadable($filespec);
                if ($found) {
                    break;
                }
            }
        }

        /**
         * Throw an exception if the file could not be located
         */
        if (!$found) {
            throw new Pft_Exception("File \"$filespec\" was not found.");
        }

        /**
         * Attempt to include() the file.
         *
         * include() is not prefixed with the @ operator because if
         * the file is loaded and contains a parse error, execution
         * will halt silently and this is difficult to debug.
         *
         * Always set display_errors = Off on production servers!
         */
        if ($once) {
            include_once($filespec);
        } else {
            include($filespec);
        }
    }


    /**
     * Returns TRUE if the $filename is readable, or FALSE otherwise.  This
     * function uses the PHP include_path, where PHP's is_readable() does not.
     *
     * @param string $filename
     * @return boolean
     */
    static public function isReadable($filename)
    {
    	//不想用打开方式，目前仅确定是否存在即可
    	//几乎没什么区别..所以放心用吧
    	//echo "isReadable:".$filename."\n";
    	$f = @fopen($filename, 'r', true);
        $readable = is_resource($f);
        if ($readable) {
            fclose($f);
        }
        return $readable;

    	//这里是为了测试一下时间 fopen 和 file_exists 差多少时间
    	//几乎没什么区别..所以放心用吧
    	/*
    	//var_dump($filename );
    	if( strpos( $filename, "Pft" ) !== false )
    	{
    		$filename = "D:\\www\\v2.tpm.com\\lib\\".$filename;
    		return file_exists( $filename );
    	}
    	else
    	{
    		return true;
    	}
        //Pft_Debug::addInfoToDefault( "Pft::isReadable()", "Filename [$filename]");
        //var_dump($filename );
        //var_dump(is_readable($filename));
		*/
    }


    /**
     * Debug helper function.  This is a wrapper for var_dump() that adds
     * the <pre /> tags, cleans up newlines and indents, and runs
     * htmlentities() before output.
     *
     * @param  mixed  $var The variable to dump.
     * @param  string $label An optional label.
     * @return string
     */
    static public function dump($var, $label=null, $echo=true)
    {
        // format the label
        $label = ($label===null) ? '' : rtrim($label) . ' ';

        // var_dump the variable into a buffer and keep the output
        ob_start();
        var_dump($var);
        $output = ob_get_clean();

        // neaten the newlines and indents
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
        if (PHP_SAPI == 'cli') {
            $output = PHP_EOL . $label
                    . PHP_EOL . $output 
                    . PHP_EOL;
        } else {
            $output = '<pre>'
                    . $label
                    . htmlentities($output, ENT_QUOTES)
                    . '</pre>';
        }

        if ($echo) {
            echo($output);
        }
        return $output;
    }


    /**
     * Registers a shared object.
     *
     * @todo use SplObjectStorage if ZF minimum PHP requirement moves up to at least PHP 5.1.0
     *
     * @param   string      $name The name for the object.
     * @param   object      $obj  The object to register.
     * @throws  Pft_Exception
     * @return  void
     */
    static public function register($name, $obj)
    {
        if (!is_string($name)) {
            throw new Pft_Exception('First argument $name must be a string.');
        }

        // don't register the same name twice
        if (array_key_exists($name, self::$_registry)) {
           throw new Pft_Exception("Object named '$name' already registered.  Did you mean to call registry()?");
        }

        // only objects may be stored in the registry
        if (!is_object($obj)) {
           throw new Pft_Exception("Only objects may be stored in the registry.");
        }

        $e = '';
        // an object can only be stored in the registry once
        foreach (self::$_registry as $dup=>$registeredObject) {
            if ($obj === $registeredObject) {
                $e = "Duplicate object handle already exists in the registry as \"$dup\".";
                break;
            }
        }

        /**
         * @todo throwing exceptions inside foreach could cause leaks, use a workaround
         *       like this until a fix is available
         *
         * @link http://bugs.php.net/bug.php?id=34065
         */
        if ($e) {
            throw new Pft_Exception($e);
        }

        self::$_registry[$name] = $obj;
    }


    /**
     * Retrieves a registered shared object, where $name is the
     * registered name of the object to retrieve.
     *
     * If the $name argument is NULL, an array will be returned where 
	 * the keys to the array are the names of the objects in the registry 
	 * and the values are the class names of those objects.
     *
     * @see     register()
     * @param   string      $name The name for the object.
     * @throws  Pft_Exception
     * @return  object      The registered object.
     */
    static public function registry($name=null)
    {
        if ($name === null) {
            $registry = array();
            foreach (self::$_registry as $name=>$obj) {
                $registry[$name] = get_class($obj);
            }
            return $registry;
        }

        if (!is_string($name)) {
            throw new Pft_Exception('First argument $name must be a string, or null to list registry.');
        }

        if (!array_key_exists($name, self::$_registry)) {
           throw new Pft_Exception("No object named \"$name\" is registered.");
        }

        return self::$_registry[$name];
    }

    
    /**
     * Returns TRUE if the $name is a named object in the
     * registry, or FALSE if $name was not found in the registry.
     *
     * @param  string $name
     * @return boolean
     */
    static public function isRegistered($name)
    {
        return isset(self::$_registry[$name]);
    }

	/**
	 * 载入 symfony 形式的类定义
	 *
	 * @param string $className
	 */
	public static function loadclass2( $className, $dirs = null )
	{
		if( !class_exists( $className ) )
		{
			/*
			exit($className);
			//此处载入 symfony 形式的 class 文件
			$fileName = Pft_Config::getLibPath().$className.DIRECTORY_SEPARATOR.$className.".php";
			echo $fileName;
			if( is_file( $fileName ) ){
				include_once( $fileName );
			}
			*/
		}
	}
	
	/**
	 * 预载入propel 及 相关的类
	 */
	public static function preLoadPropelClasses()
	{
		//单系统路径模式
		//self::loadFile( "PropelException.php", Pft_Config::getLibPath()."propel", true );
		//多系统路径模式
		$pathfilename = Pft_Config::getAbsPathFilename( "PATH_LIB", "propel/PropelException.php" );
		self::loadFile( $pathfilename, null, true );
		
		//单系统路径模式
		//self::loadFile( "Propel.php", Pft_Config::getLibPath()."propel", true );
		
		//多系统路径模式
		$pathfilename = Pft_Config::getAbsPathFilename( "PATH_LIB", "propel/Propel.php" );
		self::loadFile( $pathfilename, null, true );

		Propel::init( Pft_Config::getPropelConfFilename() );
		
		//单系统路径模式
		//self::loadFile( "Criteria.php", Pft_Config::getLibPath()."propel/util", true );
		
		//多系统路径模式
		$pathfilename = Pft_Config::getAbsPathFilename( "PATH_LIB", "propel/util/Criteria.php" );
		self::loadFile( $pathfilename, null, true );
		
		/**
		 * 在 XxxxPeer 中自动 load Propel时，
		 * 在此处 throw 的 exception 不能在 index 中捕获，
		 * 怀疑在 Propel 的 某些Class 中 catch 了异常，然后没有再抛出
		 */
		//throw new Exception("in preLoadPropelClasses");
	}
}

/**
 * 将用户输入中的 ' => '' 防SQL注入
 * sorry 写在这里了 暂时没想好放到哪儿..因为这个比较常用。写到类里太长..
 * @author terry
 */
function chks( $v ){
	//return str_replace( "'", "''", $v );
	return addslashes( $v );
}
/**
 * htmlspecialchars 的简版
 * @param string $v
 * @return string
 */
function h( $v ){
	return htmlspecialchars( $v );
}

/**
 * 获得 $_REQUEST 变量
 * 如果传入多个参数，则返回数组
 */
function r( $fieldList, $returnNullVal=true ){
	if( $fieldList == "" )
	{
		return null;
	}
	elseif ( is_array( $fieldList ) )
	{
		//因为输入的 $fieldList 是在数组的 value 中
		//且后面需要在 key 中
		//所以要 flip 到数组 key中
		$fields = array_flip( $fieldList );
	}
	else
	{
		//因为输入的 $fieldList 是在数组的 value 中
		//且后面需要在 key 中
		//所以要 flip 到数组 key中
		$fields = array_flip( explode ( ",", $fieldList ) );
	}

	$rev = array();
	foreach( $fields as $key => $field )
	{
		if( key_exists( $key, $_REQUEST ) ){
			$rev[$key] = $_REQUEST[$key];
		}else{
			if( $returnNullVal ){
				$rev[$key] = null;
			}
		}
	}
	if( count( $rev ) > 1 ){
		return $rev;
	}else{
		return current( $rev );
	}
}
?>