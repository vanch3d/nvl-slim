<?php

/**
 * Controller for all the API routes and other services
 * 
 * @author Nicolas Van Labeke (https://github.com/vanch3d)
 *
 */
class APIController extends Controller
{
	/**
	 * Return a short list of all projects
	 * @param boolean $keys
	 * @return array	An array of project IDs, if $keys true, 
	 * 					the complete descriptors otherwise 
	 * @todo	Need to find a better way of handling this (database, JSON, ...)
	 */
	public static function getProjects($keys=false)
	{
		$projIdx = array(
			'ilp' => array(
				'id' 	 =>  'ilp',
				'name' =>  'Student Model',
				'date' => 1995,
			),
			'calques3d' => array(
				'id' 	 =>  'calques3d',
				'name' =>  'Calques 3D',
				'date' => 1996,
			),
			'demist' => array(
				'id' 	 =>  'demist',
				'name' =>  'DEMIST',
				'date' => 2000,
			),
			'leactivemath' => array(
				'id' 	 =>  'leactivemath',
				'name' =>  'LeActiveMath',
				'date' => 2004,
			),
			'myplan' => array(
				'id' 	 =>  'myplan',
				'name' =>  'MyPlan',
				'date' => 2007,
			),	
			'makingstuff' => array(
				'id' 	 =>  'makingstuff',
				'name' =>  'Making Stuff',
				'date' => 2008,
			),
			'auditorygames' => array(
				'id' 	 =>  'auditorygames',
				'name' =>  'Auditory Games',
				'date' => 2008,
			),
			'safesea' => array(
				'id' 	 =>  'safesea',
				'name' =>  'SAFESeA',
				'date' => 2012,
			),
		);
        uasort($projIdx, function ($item1, $item2) {
            return $item2['date'] - $item1['date'];
        });

		if ($keys==true)
			return array_keys($projIdx);
		else
		{
			return $projIdx;
		}
	}

    /**
     *  Return a detailled list (JSON) of all projects
     */
    public function getAllProjectJSON()
    {
        $projIdx = APIController::getProjects();
        $json = array();
        foreach ($projIdx as $project)
        {
            try {
                $renderedTemplate = $this->app->view()->fetch('projects/' . $project['id'] . ".twig", array(
                    'tmpl_base' => 'template.json.twig',
                    'project' => $project,
                    'images' => array()
                ));
                $data = json_decode($renderedTemplate);
                $json[] = $data;
            } catch (Exception $e) {
            }
        }
        $this->outputJSON(array('projects'=>$json));
    }

    /**
	 * Return the details (JSON) of a specific project
	 * @param string $name
	 */
	public function getProjectJSON($name)
	{
		$error = null;
		$projIdx = APIController::getProjects();
		
		if (!array_key_exists($name, $projIdx))
		{
			$error = array(
					"msg" 	=> "API cannot generate `$name` because the project does not exist",
					"code"	=> 404);
		}
		else 
		{
			$templatePathname = $this->app->view()->getTemplatePathname('projects/'.$name.'.twig');
			if (!is_file($templatePathname))
			{
				$error = array(
					"msg" 	=> "View cannot render `$name` because the template does not exist",
					"code"	=> 500);
			}
		}
		if (isset($error))
		{
			$this->outputJSON($error,$error['code']);
			return;
		}		
		
		$img = array();
		foreach (glob("assets/projects/".$name."/*.png") as $filename) {
			$img[]=$filename;
		}

		$response = $this->app->response();
		$response['Content-Type'] = 'application/json;charset=UTF-8';
		$response['X-Powered-By'] = APPLICATION . '/' . VERSION;
		$this->render('projects/'.$name,array(
			'tmpl_base' => 'template.json.twig',
			'project' => $projIdx[$name],
			'images' => $img
		));
	}

    /**
     * Return a detailed list (JSON) of all slides associated with the given project
     * @param $name The ID of the project
     */
    public function getSlidesJSON($name)
    {
        // set the cache for the request
        $this->app->etag('nvl-slim.slideshare'.$name);
        $this->app->expires('+1 week');

        try {
            $var = $this->app->config("nvl-slim.slideshare");
            if (!isset($var))
                throw new Exception("SlideShare API authentication not configured",0);

            $ts = time();
            $data = array(
                'ts'            =>  $ts,				        // timestamp
                'api_key'       =>  $var['api_key'],            // SlideShare API key
                'hash'          =>  sha1($var['secret'].$ts),   // Secret hash
                'username_for'  =>  'nicolasVL',                // Slides owner
                'detailed'      =>  '1'
            );

            // build the URL for Slideshare API
            $param = http_build_query($data);
                $url2 = 'https://www.slideshare.net/api/2/get_slideshows_by_user?'.$param;

            $request = Requests::get($url2);
            if (!$request->success)
                throw new Exception('Cannot access the SlideShare API.',$request->status_code);

            $xml = new SimpleXMLElement($request->body);
            if ("SlideShareServiceError" == $xml->getName())
            {
                $node = $xml->children();
                $attr = $node->attributes();
                throw new Exception("SlideShareServiceError : ".(string)$node,(string)$attr->ID);
            }

            $text = json_encode($xml);
            $json = json_decode($text,true);
            $slides = array();
            foreach($json['Slideshow'] as $slideshow)
            {
                if (in_array("@".$name,$slideshow['Tags']['Tag']))
                    $slides[] = $slideshow;
            }
            $json['Count'] = count($slides);
            $json['Slideshow'] = $slides;
            $this->outputJSON($json);

        } catch (Exception $e)
        {
            $error = array(
                'code'      =>    $e->getCode(),
                'message'   =>    $e->getMessage());

            $this->outputJSON($error,500);
        }
    }
	
	/**
	 * 
	 * @param string $id	The unique identifier of the Zotero item
	 * @param string $format	The output format
	 * @return mixed:
	 * @deprecated individual items are retrieved from the collection request
	 * @see APIController::retrieveZotero
	 * 
	 */
    private function retrieveZoteroItem($id,$format)
	{
		$libraryType = 'user'; //user or group
		$libraryID = 6249;
		$librarySlug = 'all_things_zotero';
		$apiKey = 'XKAHGTAwUZXN1qJUH7fTSuFM';
		$collectionKey = 'BWPDQJUN';
		
		//create a library object to interact with the zotero API
		$library = new Zotero_Library($libraryType, $libraryID, $librarySlug, $apiKey);
		
		$user = 			$libraryID;					// Zotero user ID
		$collection = 		$collectionKey; 			// Collection key (my own stuff)
		$data = array(
				'key'=>		$apiKey,					// Zotero API key
				'format'=>	'atom',						// output format
				'content'=> $format						// content formats to retrieve
		);
		
		// build the URL for Zotero API
		$param = http_build_query($data);
		$url2 = 'https://api.zotero.org/users/'.$user.'/items/'.$id.'?'.$param;
		
		// Send the request to the server (bypassing Zotero_Library)
		$request = Requests::get($url2,array('Zotero-API-Version' => ZOTERO_API_VERSION));
		// retrieve the feed and parse it

		$entry = Zotero_Lib_Utils::getFirstEntryNode($request->body);
		
		$item = new Zotero_Item($entry, $library);
		$library->items->addItem($item);

		return $item->subContents[$format];	
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
	private function writeZoteroCache($name,$data)
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
	}
	
	public function readZoteroCache($name)
	{
		$file = $this->getZoteroCacheFilename($name);
		return json_decode(@file_get_contents($file),true);
	}
	
	/**
	 * 
	 * @param unknown $name
	 * @param unknown $cslfile
	 * @return multitype:string
	 */
	private function retrieveZotero($name,$cslfile)
	{
		$libraryType = 'user'; //user or group
		$libraryID = 6249;
		$librarySlug = 'all_things_zotero';
		$apiKey = 'XKAHGTAwUZXN1qJUH7fTSuFM';
		$collectionKey = 'BWPDQJUN';
		
		//create a library object to interact with the zotero API
		$library = new Zotero_Library($libraryType, $libraryID, $librarySlug, $apiKey);
		
		$user = 			$libraryID;					// Zotero user ID
		$collection = 		$collectionKey; 			// Collection key (my own stuff)
		$data = array(
				'key'=>		$apiKey,					// Zotero API key
				//'tag'=>		'nvl.'.$name,				// tags to look  for
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
		$url2 = 'https://api.zotero.org/users/'.$user.'/collections/'.$collection.'/items/top?'.$param;
		
		// Send the request to the server (bypassing Zotero_Library)	
		$request = Requests::get($url2,array('Zotero-API-Version' => ZOTERO_API_VERSION));
		
		// retrieve the feed and parse it
		$feed = new Zotero_Feed($request->body);
		$fetchedItems = $library->items->addItemsFromFeed($feed);
		
		// combine the data into a better JSON structure
		$pubs = array();
		if (count($fetchedItems))
		{
			// extract the CSLJSON object as the basis for the publications
			// add the COINS object in the output
			foreach ($fetchedItems as $item)
			{
				$tt = json_decode($item->subContents['csljson'],true);
				$tt['id']=$item->itemKey;
				// fix the clsjson date
				$tt['issued']['date-parts'] = array(array($item->year));
				$tt['PDF'] = $this->app->urlFor('publications.named.pdf',array('name'=>$tt['archive_location']));
				$item->subContents['csljson']=json_encode($tt);
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
						$projIdx = APIController::getProjects();
						$name2= strtolower(substr($tag['tag'], 4));
						$tt['project']= $projIdx[$name2];
						$tt['project']['url'] = $this->app->urlFor('project.named',array('name'=>$name2));
					}
				}
					
				$pubs[$item->itemKey] = $tt;
				$this->writeZoteroCache($item->itemKey, $item->subContents);
			};

			// Initialise the CSL generator
			$csl_data ='../app/utils/styles/'.$cslfile;
			$csl_data = file_get_contents($csl_data);
			$citeproc = new citeproc($csl_data);

			// Format each publication and citation
			foreach($pubs as &$data) {
				$kk= json_decode(json_encode($data));
				$ref = $citeproc->render($kk);
				$cite = $citeproc->render($kk,'citation');
				$data['output']['cite'] = $cite;
				$data['output']['ref'] = $ref;
					
			}
		}
		
		return $pubs;
	}

	
	/**
	 * Retrieve the list of publications for the given project (tag).
	 * The tag is expected to be one of the projects (see ), or 'all' for the complete list.
	 * 
	 * @param string $name	The project tag to retrieve publications for
	 * @return	A JSON object containing the requested publications
	 */
	public function getPublicationsJSON($name)
	{
		// set the cache for the request 
		$this->app->etag($name.'BWPDQJUN');
		$this->app->expires('+1 week');
		
		$pubs = $this->retrieveZotero($name, "umuai-NVL.csl");

		$this->outputJSON($pubs);
	}


    public function getImagesJSON($name)
    {
        // set the cache for the request
        //$this->app->etag($name.'gallery');
        //$this->app->expires('+1 week');


        $data = array(
            'format'=>		'json',					// Zotero API key
            'method'=>		'pwg.categories.getList',	// Zotero API key
        );

        // build the URL for Zotero API
        $param = http_build_query($data);
        $url2 = 'http://gallery.calques3d.org/ws.php?'.$param;

        $request = Requests::get($url2);
        $data = json_decode($request->body,true);
        if ($data['err'])
        {
            throw new Exception($data['message'],$data['err']);
        }

        $cat = reset(array_filter($data['result']['categories'], function($var) use($name){
            return ($name==$var['permalink']);
        }));

        if (!$cat)
        {
            throw new Exception('no images for this',501);
        }


        $data = array(
            'format'=>		'json',					// Zotero API key
            'method'=>		'pwg.categories.getImages',	// Zotero API key
            'cat_id'=>      $cat['id']
        );

        // build the URL for Zotero API
        $param = http_build_query($data);
        $url2 = 'http://gallery.calques3d.org/ws.php?'.$param;

        $request = Requests::get($url2);
        $data = json_decode($request->body,true);
        if ($data['err'])
        {
            throw new Exception($data['message'],$data['err']);
        }

        $imgs = array();
        foreach ($data['result']['images'] as $img)
        {
            $tmp['url'] = $img['element_url'];
            $tmp['comment'] = $img['comment'];
            $imgs[]=$tmp;
        }
        $this->outputJSON($imgs);
    }

	/**
	 * 
	 * @return multitype:multitype:string
	 */
	private function getUnAPIFormats()
	{
		
		
		$formatsList = array(
			array(
					'name' => 'rdf_bibliontology',
					'type' => 'application/xml',
					'doc' => 'http://bibliontology.com/'
			),
			array(
					'name' => 'bibtex',
					'type' => 'text/plain',
					'doc' => 'http://www.bibtex.org/'
			)
			/*array(
					'name' => 'mods',
					'type' => 'application/xml',
					'doc' => 'http://www.loc.gov/standards/mods/'
			)
			)*/
		);
		return $formatsList;
	}
	
	private function searchItem( $myarray, $key, $val) {
		foreach ($myarray as $item) {
			if (isset($item[$key]) && $item[$key] == $val)
				return $item;
		}
		return null;
	}
	
	/**
	 * Return the list of available formats for the given publication or collection
	 * @param string $id	The ID of a publication, null for the overall collection
	 * @return SimpleXMLElement
	 */
	private function outputUnAPIFormats($id=null)
	{
		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><formats/>');
		if (isset($id))
				$xml->addAttribute('id', $id);
		foreach ($this->getUnAPIFormats() as $item){
			$format = $xml->addChild('format');
			foreach ($item as $key=>$attr)
				$format->addAttribute($key, $attr);
		}
		return $xml;
	}
	
	/**
	 * Implementation of a basic UnAPI service
	 * @throws Exception
	 * 
	 */
	public function unAPI()
	{
		$req = $this->app->request();
		$id = $req->get('id');
		$format = $req->get('format');
		
		// set the cache for the request
		$this->app->etag('UnAPI/'.$id.$format);
		$this->app->expires('+1 week');
		
		
		$xml = null;
	
		if (!isset($id) && !isset($format))
		{
			$xml = $this->outputUnAPIFormats();
			$this->outputXML($xml);
			return;
		}
		try {

			if (!isset($id))
				throw new Exception('Bad Request - id is needed', 400);
				
			$ftdata = null;
			if (isset($format))
			{
				$ftdata = $this->searchItem($this->getUnAPIFormats(),"name",$format);
				if ( !$ftdata )
					throw new Exception('Format Not Acceptable', 406);
			}
				
			if (isset($id) && isset($format))
			{
				$item = $this->readZoteroCache($id);
				
				
				if (!isset($item))
					throw new Exception('Id Not Found', 404);
				$content = $item[$format];
				
				if (!isset($content))
					throw new Exception('Format Not Acceptable', 406);
				
				//var_dump($item);die();
				$response = $this->app->response();
				$response['Content-Type'] = $ftdata['type'];
				$response['X-Powered-By'] = APPLICATION . '/' . VERSION;
				//if ($ftdata['type'] == 'application/xml')
				$response->setBody($content);
			}
			else
			{
				$xml = $this->outputUnAPIFormats($id);
				$this->outputXML($xml);
			}
		
		} 
		catch (\Exception  $e) {
			$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><error/>');
			$this->outputXML($xml,$e->getCode());
		}
	}
}