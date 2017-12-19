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
     * @deprecated      replaced by a self-defined handler
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
            'explabs' => array(
                'id' 	 =>  'explabs',
                'name' =>  'Experience Labs',
                'date' => 2014,
            ),
            'mypal' => array(
                'id' 	 =>  'mypal',
                'name' =>  'myPAL',
                'date' => 2016,
            )

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
     *  Return a detailed list (JSON) of all projects
     */
    public function getAllProjectJSON()
    {
        //$projIdx = APIController::getProjects();
        $projIdx = $this->getProjectDescriptors();

        $json = array();
        foreach ($projIdx as $project)
        {
            try {
                $renderedTemplate = $this->app->view()->fetch('projects/content/' . $project['id'] . ".twig", array(
                    'tmpl_base' => 'template.json.twig',
                    'project' => $project
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
		//$projIdx = APIController::getProjects();
        //$found = current(array_filter($projIdx, function($item) use($name) {
        //    return isset($item['id']) && $name == $item['id'];
        //}));

        $found = $this->isProjectDefined($name);

        //if (!array_key_exists($name, $projIdx))
        if (!$found)
		{
			$error = array(
					"msg" 	=> "API cannot generate `$name` because the project does not exist",
					"code"	=> 404);
            $this->outputJSON($error,$error['code']);
            return;
		}
		/*else
		{
			$templatePathname = $this->app->view()->getTemplatePathname('projects/content/'.$name.'.twig');
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
		}*/

		$response = $this->app->response();
		$response['Content-Type'] = 'application/json;charset=UTF-8';
		$response['X-Powered-By'] = APPLICATION . '/' . VERSION;
		$this->render('projects/content/'.$name,array(
			'tmpl_base' => 'template.json.twig',
			'project' => $found//$projIdx[$name]
		));
	}

    /**
     * Return a detailed list (JSON) of all slides associated with the given project
     * @param string $name The ID of the project
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
	 * Retrieve the list of publications for the given project (tag).
	 * The tag is expected to be one of the projects (see ), or 'all' for the complete list.
	 *
	 * @param string $name	The project tag to retrieve publications for
	 */
	public function getPublicationsJSON($name)
	{
		// set the cache for the request
		$this->app->etag($name.'BWPDQJUN');
		$this->app->expires('+1 week');

        try {
            $pubs = $this->retrieveFromZotero($name, "umuai-nvl.csl");
            $this->outputJSON($pubs);
        } catch (Exception $e) {
            $error = array(
                'code'      =>    $e->getCode(),
                'message'   =>    $e->getMessage());

            $this->outputJSON($error,500);

        }
	}


    public function getImagesJSON($name)
    {
        // set the cache for the request
        $this->app->etag($name.'gallery');
        $this->app->expires('+1 week');


        $data = array(
            'format'=>		'json',					// Zotero API key
            'method'=>		'pwg.categories.getList',	// Zotero API key
        );

        // build the URL for galery API
        $param = http_build_query($data);
        $url2 = 'http://gallery.calques3d.org/ws.php?'.$param;

        try {

            $request = Requests::get($url2);
            $data = json_decode($request->body, true);

            if (isset($data['err'])) {
                throw new Exception($data['message'], $data['err']);
            }

            $cat = array_filter($data['result']['categories'], function ($var) use ($name) {
                return ($name == $var['permalink']);
            });
            $cat = reset($cat);

            if (!$cat) {
                throw new Exception('No images for this project', 501);
            }


            $data = array(
                'format' => 'json', // Zotero API key
                'method' => 'pwg.categories.getImages', // Zotero API key
                'cat_id' => $cat['id']
            );

            // build the URL for Zotero API
            $param = http_build_query($data);
            $url2 = 'http://gallery.calques3d.org/ws.php?' . $param;

            $request = Requests::get($url2);
            $data = json_decode($request->body, true);

            if (isset($data['err'])) {
                throw new Exception($data['message'], $data['err']);
            }


            $imgs = array();
            foreach ($data['result']['images'] as $img) {
                $tmp['url'] = $img['element_url'];
                $tmp['comment'] = $img['comment'];
                $tmp['title'] = $img['name'];
                $tmp['thumb'] = $img['derivatives']['thumb']['url'];
                $imgs[] = $tmp;
            }
            $this->outputJSON($imgs);
        }
        catch (Exception $e)
        {
            $error = array(
                'code'      =>    $e->getCode(),
                'message'   =>    $e->getMessage());

            $this->outputJSON($error,$e->getCode());
        }
    }

    /**
     * @return array    The list of all available UNAPI formats
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

        $this->getLog()->notice("unapi microservice called",array(
            'id' => $id,
            'format' => $format
        ));

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
				throw new Exception('Argument \'id\' is required', 400);

			$ftdata = null;
			if (isset($format))
			{
				$ftdata = $this->searchItem($this->getUnAPIFormats(),"name",$format);
				if ( !$ftdata )
					throw new Exception('Argument \'format\': format not acceptable', 405);
			}

			if (isset($id) && isset($format))
			{
                $items = $this->getCachedZotero("all",200);
                $item=array_filter($items['publications'],function($v) use($id){
                    return($v['id']===$id);
                });


                if (!isset($item) || empty($item))
					throw new Exception('Id Not Found', 404);

				$content = $item[0]['output'][$format];

				if (!isset($content))
					throw new Exception('Content Not Found', 404);

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
			$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><nvlAppError/>');
            $xml->addChild("method",$req->getPathInfo());
            $xml->addChild("errorString",$e->getMessage());
            $xml->addChild("errorCode",$e->getCode());
            $this->outputXML($xml,$e->getCode());
		}
	}
}