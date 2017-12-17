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

	public function getLog()
    {
        return $this->app->getLog();
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
    public function isProjectDefined($id)
    {
        if (empty($id)) return false;
        $projIdx = $this->getProjectDescriptors();
        $found = current(array_filter($projIdx, function($item) use($id) {
            return isset($item['id']) && $id == $item['id'];
        }));

        return $found;
    }

    /**
     * Return the path of the cache file for the given bib item
     * @param string $name	the ID of the bib to cache
     * @return string
     */
    private function getZoteroCacheFilename($name)
    {
        $twig = $this->app->view()->getInstance();
        return $twig->getCache().'/zotero/'.$name;
    }

    /**
     * Write the content of the bib item into the given cache file
     * @param string 	$name
     * @param stdClass 	$data
     * @throws RuntimeException
     */
    private function writeZoteroCache($name,$data,$loc)
    {
        $file = $this->getZoteroCacheFilename($name);
        $dir = dirname($file);
        if (!is_dir($dir)) {
            if (false === @mkdir($dir, 0777, true) && !is_dir($dir)) {
                throw new RuntimeException(sprintf("Unable to create the cache directory (%s).", $dir));
            }
        } elseif (!is_writable($dir)) {
            throw new RuntimeException(sprintf("Unable to write in the cache directory (%s).", $dir));
        }

        @file_put_contents($file, json_encode($data));

        if (isset($loc))
        {
            $file = $this->getZoteroCacheFilename("fileindex");
            $idx = @file_get_contents($file);
            $data = array();
            if ($idx)
                $data = json_decode($idx,true);
            $data[$loc] = $name;
            @file_put_contents($file, json_encode($data));
        }
    }

    protected function readZoteroCache($name)
    {
        $file = $this->getZoteroCacheFilename($name);
        return json_decode(@file_get_contents($file),true);
    }

    private function readZoteroFileIndex()
    {
        $file = $this->getZoteroCacheFilename('fileindex');
        return json_decode(@file_get_contents($file),true);
    }

    /**
     * @param string $project_id    The id of the project to retrieve publications from (or 'all')
     * @param int $limit            The maximum number of publications to retrieve
     * @param string $csl_file      The CSL file to use for formatting the publications
     * @return array                A list of publications
     * @throws Exception
     */
    public function getCachedZotero($project_id="all", $limit = 20, $csl_file="umuai-nvl.csl")
    {
        $cfg = $this->app->config("nvl-slim.zotero");
        if (!isset($cfg))
            throw new Exception("Zotero API authentication not configured",500);

        // storage for publications data
        $pubs = array();

        // define Zotero parser
        $libraryType = 'user'; //user or group
        $libraryID = $cfg['userID'];
        $librarySlug = 'all_things_zotero';
        $apiKey = $cfg['api_key'];
        $collectionKey = $cfg['collectID'];

        //create a library object to interact with the zotero API
        $library = new Zotero_Library($libraryType, $libraryID, $librarySlug, $apiKey);

        $data = array(
            'key'=>		$apiKey,					// Zotero API key
            'limit'=>	$limit,						// max number of items
            'order'=>	'date',						// sort by field
            'sort'=>	'desc',						// sort order
            'format'=>	'atom',						// output format
            'content'=> 'csljson,rdf_bibliontology,bibtex,coins,json'		// content formats to retrieve
        );
        if ($project_id!='all')
            $data['tag'] = 'nvl.'.$project_id;				// tags (project) to look for

        $this->getLog()->notice("Retrieving publications",array(
            "target" => (isset($data['tag']) ? $data['tag'] : "ALL"),
            "limit" => $limit
        ));

        // build the request for Zotero API
        $param = http_build_query($data);
        $url = 'https://api.zotero.org/users/'.$libraryID.'/collections/'.$collectionKey.'/items/top?'.$param;

        $headers = array(
            'Zotero-API-Version' => ZOTERO_API_VERSION
        );

        // get cache and 'last-modified-version' if exist
        $cache = $this->readZoteroCache("zotero".(isset($data['tag']) ? $data['tag'] : "ALL").$limit);

        $isCached = isset($cache) && isset($cache['last-modified-version']);
        if ($isCached)
        {
            $this->getLog()->notice("Cached version exists",array(
                "version" => $cache['last-modified-version']
            ));
            $headers['If-Modified-Since-Version'] = $cache['last-modified-version'];
        }
        else
        {
            $this->getLog()->notice("No cached version");
        }
        $this->getLog()->notice("Generate request",$headers);


        // Send the request to the server (bypassing Zotero_Library)
        $request = Requests::get($url,$headers);
        $this->getLog()->notice("Request sent & received",array(
            "status" => $request->status_code
        ));

        $pubs['publications'] = [];
        if ($request->status_code == 200)
        {
            // retrieve the feed and parse it
            $feed = new Zotero_Feed($request->body);
            $fetchedItems = $library->items->addItemsFromFeed($feed);

            $pubs['last-modified-version'] = $request->headers['last-modified-version'];//->getAll();
            $pubs['count'] = count($fetchedItems);

            // Initialise the CSL generator
            $csl_data ='../app/utils/styles/'.$csl_file;
            $csl_data = file_get_contents($csl_data);
            $citeproc = new citeproc($csl_data);

            // extract the CSLJSON object as the basis for the publications
            // add the COINS object in the output
            foreach ($fetchedItems as $item)
            {
                $pubitem = json_decode($item->subContents['csljson'],true);
                $pubitem['id']=$item->itemKey;
                // fix the clsjson date
                $pubitem['issued']['date-parts'] = array(array($item->year));

                $loc = $pubitem['archive_location'];
                $pubitem['PDF'] = $this->app->urlFor('publications.named.pdf',array('name'=>$loc));

                $templatePathname = $this->app->view()->getTemplatePathname('publications/papers/'.$loc.'.twig');

                if (is_file($templatePathname))
                {
                    $pubitem['PubReader'] = $this->app->urlFor('publications.named.pubreader',array('name'=>$loc));
                }

                // extract the coins for outputs
                $pubitem['output']['coins'] = htmlspecialchars_decode($item->subContents['coins']);
                $pubitem['output']['bib'] = htmlspecialchars_decode($item->subContents['bibtex']);

                // extract keywords and project
                $tags = $item->apiObject['tags'];
                foreach ($tags as  $tag){
                    if (false === strpos($tag['tag'], 'nvl.'))
                        $pubitem['keyword'][] = $tag['tag'];
                    else
                    {
                        $name2= strtolower(substr($tag['tag'], 4));
                        $projIdx = $this->isProjectDefined($name2);

                        if ($projIdx) {
                            $pubitem['project'] = $projIdx;
                            $pubitem['project']['url'] = $this->app->urlFor('project.named', array('name' => $name2));
                        }
                    }
                }

                $kk= json_decode(json_encode($pubitem));
                $ref = $citeproc->render($kk);
                $cite = $citeproc->render($kk,'citation');
                $pubitem['output']['cite'] = $cite;
                $pubitem['output']['ref'] = $ref;

                $item->subContents['csljson']=json_encode($pubitem);
                $pubs['publications'][] = $pubitem;
                //$this->writeZoteroCache($item->itemKey, $item->subContents,$loc);
            };

            $this->writeZoteroCache("zotero".(isset($data['tag']) ? $data['tag'] : "ALL").$limit,$pubs,null);

        }
        else if ($request->status_code == 304)
        {
            $this->getLog()->notice("no change since last request, cache used",array(
            ));
            $pubs = $cache;

        }
        else
        {
            $this->getLog()->notice("ERROR",array(
                "status" => $request->status_code
            ));

            throw new Exception($request->body,$request->status_code);
        }

        return $pubs;
    }

    /**
     * @param string $name      The id of the project
     * @param string $cslfile   The style to format publications
     * @return array
     * @throws Exception
     * @deprecated  No longer in use
     */
    protected function retrieveFromZotero($name, $cslfile)
    {
        $cfg = $this->app->config("nvl-slim.zotero");
        if (!isset($cfg))
            throw new Exception("Zotero API authentication not configured",500);

        $libraryType = 'user'; //user or group
        $libraryID = $cfg['userID'];
        $librarySlug = 'all_things_zotero';
        $apiKey = $cfg['api_key'];
        $collectionKey = $cfg['collectID'];

        //create a library object to interact with the zotero API
        $library = new Zotero_Library($libraryType, $libraryID, $librarySlug, $apiKey);

        $data = array(
            'key'=>		$apiKey,					// Zotero API key
            'limit'=>	'50',						// max number of items
            'order'=>	'date',						// sort by field
            'sort'=>	'desc',						// sort order
            'format'=>	'atom',						// output format
            'content'=> 'csljson,rdf_bibliontology,bibtex,coins,json'		// content formats to retrieve
        );
        if ($name!='all')
            $data['tag'] = 'nvl.'.$name;				// tags to look  for

        // build the URL for Zotero API
        $param = http_build_query($data);
        $url2 = 'https://api.zotero.org/users/'.$libraryID.'/collections/'.$collectionKey.'/items/top?'.$param;

        // Send the request to the server (bypassing Zotero_Library)
        $request = Requests::get($url2,array('Zotero-API-Version' => ZOTERO_API_VERSION));

        // retrieve the feed and parse it
        $feed = new Zotero_Feed($request->body);
        $fetchedItems = $library->items->addItemsFromFeed($feed);

        // combine the data into a better JSON structure
        $pubs = array();
        if (count($fetchedItems))
        {
            // Initialise the CSL generator
            $csl_data ='../app/utils/styles/'.$cslfile;
            $csl_data = file_get_contents($csl_data);
            $citeproc = new citeproc($csl_data);

            // extract the CSLJSON object as the basis for the publications
            // add the COINS object in the output
            foreach ($fetchedItems as $item)
            {
                $tt = json_decode($item->subContents['csljson'],true);
                $tt['id']=$item->itemKey;
                // fix the clsjson date
                $tt['issued']['date-parts'] = array(array($item->year));



                $loc = $tt['archive_location'];
                $tt['PDF'] = $this->app->urlFor('publications.named.pdf',array('name'=>$loc));

                $templatePathname = $this->app->view()->getTemplatePathname('publications/papers/'.$loc.'.twig');

                if (is_file($templatePathname))
                {
                    $tt['PubReader'] = $this->app->urlFor('publications.named.pubreader',array('name'=>$loc));
                }

                // extract the coins for outputs
                $tt['output']['coins'] = htmlspecialchars_decode($item->subContents['coins']);
                $tt['output']['bib'] = htmlspecialchars_decode($item->subContents['bibtex']);
                // extract keywords and project
                $tags = $item->apiObject['tags'];
                foreach ($tags as  $tag){
                    if (false === strpos($tag['tag'], 'nvl.'))
                        $tt['keyword'][] = $tag['tag'];
                    else
                    {
                        //$projIdx = APIController::getProjects();

                        $name2= strtolower(substr($tag['tag'], 4));
                        $projIdx = $this->isProjectDefined($name2);

                        if ($projIdx) {
                            $tt['project'] = $projIdx;
                            $tt['project']['url'] = $this->app->urlFor('project.named', array('name' => $name2));
                        }
                    }
                }


                $kk= json_decode(json_encode($tt));
                $ref = $citeproc->render($kk);
                $cite = $citeproc->render($kk,'citation');
                $tt['output']['cite'] = $cite;
                $tt['output']['ref'] = $ref;

                $item->subContents['csljson']=json_encode($tt);
                $pubs[$item->itemKey] = $tt;
                $this->writeZoteroCache($item->itemKey, $item->subContents,$loc);
            };


            // Format each publication and citation
            foreach($pubs as &$data) {
                $kk= json_decode(json_encode($data));

            }
        }

        return $pubs;
    }

}