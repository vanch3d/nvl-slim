<?php

/**
 * 
 * @author Nicolas Van Labeke (https://github.com/vanch3d)
 *
 */
class Application {

	/**
	 *
	 * @var \Slim\Slim
	 */
	public $app;
	
	public function __construct(\Slim\Slim $slim = null)
	{
		$this->app = !empty($slim) ? $slim : \Slim\Slim::getInstance();
	}
	
	/**
	 * 
	 */
	public function run()
	{
		$this->app->run();
	}
	
	
}



/**
 * Abstract class for all controllers
 * @author Nicolas Van Labeke (https://github.com/vanch3d)
 *
 */
abstract class Controller extends Application {


	/**
	 * Default constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 
	 * @param string $template
	 * @param array $data
	 * @param string $status
	 */	
	public function render($template, $data = array(), $status = null)
	{
		//$this->app->view()->appendData(array('auth' => $this->auth));
		$this->app->render($template . EXT, $data, $status);
	}
	
	public function redirect($name, $routeName = true)
	{
		$url = $routeName ? $this->app->urlFor($name) : $name;
		$this->app->redirect($url);
	}
	
	public function outputJSON($body, $status = null)
	{
		$response = $this->app->response();
		$response['Content-Type'] = 'application/json;charset=UTF-8';
		$response['X-Powered-By'] = APPLICATION . '/' . VERSION;
		$response->setStatus(!is_null($status) ? $status: 200);
		$response->setBody(json_encode($body));
	}
	
	/**
	 * 
	 * @param SimpleXMLElement $xml
	 * @param string $status
	 */
	public function outputXML($xml, $status = null)
	{
		$response = $this->app->response();
		$response['Content-Type'] = 'application/xml;charset=UTF-8';
		$response['X-Powered-By'] = APPLICATION . '/' . VERSION;
		$response->setStatus(!is_null($status) ? $status: 200);
		$response->setBody($xml->asXML());
	}
	
}