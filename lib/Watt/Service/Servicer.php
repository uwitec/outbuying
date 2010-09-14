<?
/**
 * 服务提供者
 * 目前使用 WebService
 * 
 * 这个类
 * 1.简化Soap的构造
 * 2.为了将来使用其他的 Soap 解决方案的变化而准备
 *
 * @author Yan
 * @package defaultPackage
 */

abstract class Watt_Service_Servicer
{
	/**
	 * SoapServer
	 *
	 * @var SoapServer
	 */
//	private $_soapServer;
	
	/**
	 * 子类不要重载此类的构造函数！！
	 * 构造函数参数请参考 SoapServer
	 *
	 * 说明！已废弃！
	 * 
	 * @param mixed $wsdl
	 * @param array $options
	 */
//	function __construct( $wsdl, array $options = array() ){
//		$this->_soapServer = new SoapServer( $wsdl, $options );
//		$this->_soapServer->setClass( get_class( $this ) );
//		//$server->handle();
//		//exit();		
//		//print "<pre>";var_dump(  );print "</pre>";exit;
//	}
//	public function handle(){
//		$this->_soapServer->handle();
//		exit();
//	}
	
	/**
	 * handle 一个 SoapServer
	 *
	 * Uses:
	 * <code>
	 * Watt_Service_Servicer::handleSoapServer( "Your_Class_Servicer", "demo.wsdl" );
	 * </code>
	 * 
	 * @param string $className
	 * @param mixed $wsdl
	 * @param array $options
	 */
	public static function handleSoapServer( $className, $wsdl, array $options = array() ){
		if( !class_exists( $className ) ) Watt::loadClass($className);
		//$server = new SoapServer( null, array('uri' => "http://test-uri/") );
		$server = new SoapServer( Watt_Config::getConfigPath()."wsdl/".$wsdl );
		$server->setClass( $className );
		$server->handle();
		exit();
	}
	
	/**
	 * 根据一个 WSDL，获得一个 SoapClient 对象
	 *
	 * Uses:
	 * <code>
	 * $client = Watt_Service_Servicer::getSoapClient( "demo.wsdl", array('trace' => false
     *                                                            ,'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP
     *                                                            ));
	 * </code>
	 * 
	 * @param mixed $wsdl
	 * @param array $options
	 * @return SoapClient
	 */
	public static function getSoapClient( $wsdl, array $options = array() ){
        return new SoapClient( Watt_Config::getConfigPath()."wsdl/".$wsdl
                             , array('trace' => false
//                                    ,'uri' => "http://test-uri/"
                                    ,'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP
                                    )
                             );
	}
}