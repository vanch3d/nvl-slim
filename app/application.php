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


    /**
     * Retrieve a project descriptors array based on twig templates in the projects/content directory
     * @param bool $short   false (default) to retrieve the whole description, true
     *                      to extract only the basic identifiers (id, name, url, start date)
     * @return array        An array containing the projects' description
     * @todo                caching (see PHPSocialNetwork/phpFastCache)
     * @todo                project name needs to be redefined within each twig template
     */
    public function getProjectDescriptors($short = false)
    {
        /** @var \Slim\View $view */
        $view = $this->app->view();
        $json = array();

        foreach(glob($view->getTemplatePathname('/projects/content/*.twig')) as $file) {
            $projid = basename($file,".twig");
            try {
                $renderedTemplate = $view->fetch("/projects/content/".basename($file), array(
                    'tmpl_base' => 'template.json.twig',
                    'project' => array(
                        "id" => $projid,
                        "name"=>$projid)
                ));

                $data = json_decode($renderedTemplate,true);
                if ($short)
                {
                    $filter = array("id","name","url","start");
                    $data = array_intersect_key($data, array_flip($filter));
                }
                //$json[] = $data;
                $json[$projid] = $data;


            } catch (Exception $e) {
                #TODO Define output when exception generated (ignored at the moment)
            }

        }

        // sort projects by reverse chronological order (based on start date)
        usort($json,function($a,$b){
            return strcmp($b["start"], $a["start"]);
        });

        return $json;
    }

    /**
     *
     * @param string $id  The id of the project to check
     * @return mixed        False if the project does not exist, its descriptor if it does
     */
    function isProjectDefined($id)
    {
        if (empty($id)) return false;
        $projIdx = $this->getProjectDescriptors();
        $found = current(array_filter($projIdx, function($item) use($id) {
            return isset($item['id']) && $id == $item['id'];
        }));

        return $found;
    }
}