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

    public function isPublicationDefined($id)
    {
        if (empty($id)) return false;

        try {
            $items = $this->getCachedZotero("all", 200);
            $item=array_filter($items['publications'],function($v) use($id){
                return($v['archive_location']===$id);
            });
            if (!isset($item) || empty($item))
                return false;
            return reset($item);

        } catch (Exception $e) {
            return false;
        }
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
    protected function writeZoteroCache($name,$data,$loc = null)
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

    /**
     * @param string $project_id    The id of the project to retrieve publications from (or 'all')
     * @param int $limit            The maximum number of publications to retrieve
     * @param string $csl_file      The CSL file to use for formatting the publications
     * @return array                A list of publications
     * @throws Exception
     *
     * @todo[vanch3d] Make sure all URLs are fully qualified (eg PDF, URL, ...)
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
            'content'=> 'csljson,rdf_bibliontology,bibtex,json'		// content formats to retrieve
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
            foreach ($fetchedItems as $item)
            {
                $pubitem = json_decode($item->subContents['csljson'],true);
                $pubitem['id']=$item->itemKey;

                $loc = $pubitem['archive_location'];
                if (!in_array($pubitem['type'],array("software") )) {
                    $pubitem['PDF'] = $this->app->urlFor('publications.named.pdf', array('name' => $loc));
                }

                $templatePathname = $this->app->view()->getTemplatePathname('publications/papers/'.$loc.'.twig');
                if (is_file($templatePathname))
                {
                    $pubitem['PubReader'] = $this->app->urlFor('publications.named.pubreader',array('name'=>$loc));
                }

                $pubitem['output']['bibtex'] = htmlspecialchars_decode($item->subContents['bibtex']);
                $pubitem['output']['rdf_bibliontology'] = htmlspecialchars_decode($item->subContents['rdf_bibliontology']);

                // some attributes might be saved in notes/extra if not supported by schema
                if (isset($pubitem['note']))
                {
                    $noteStr = $pubitem['note'];
                    $notes = explode("\n",$noteStr);
                    foreach ($notes as $note)
                    {
                        // try if item is KEY: VALUE, with KEY = DOI|fig
                        $match = null;
                        if (preg_match("/(doi|fig):\s*(.*)/i", $note,$match))
                        {
                            // make sure it does not overwrite something
                            if (!isset($pubitem[$match[1]])) {
                                $pubitem[$match[1]] = $match[2];
                            }
                        }

                    }

                }


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

                // format reference and citation
                $temObj= json_decode(json_encode($pubitem));
                $ref = $citeproc->render($temObj);
                $cite = $citeproc->render($temObj,'citation');
                $pubitem['output']['cite'] = $cite;
                $pubitem['output']['ref'] = $ref;

                //$item->subContents['csljson']=json_encode($pubitem);
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
     * @param array $files
     * @return array
     */
    protected function getFrequencyDistribution(array $files)
    {
        $stopwords = array_merge(
            //array("#","-","...",">","=","&","'s",",","(",")",":",";","'",".","%","a","”","''","“","[","]","|"),
            array("pp.","i.e.","e.g.","j","j.","p","amp","quot","etc."),
            file("../app/utils/nlp/stopwords.data", FILE_IGNORE_NEW_LINES)
        );


        /** @var string $text */
        $text = null;
        $errors = [];

        foreach ($files as $file) {

            try {
                /** @var \Slim\View $view */
                $view = $this->app->view();
                $renderedTemplate = $view->fetch('publications/papers/' . $file . '.twig', array(
                    'template_base' => 'publications/template.txt.twig'
                ));
                $text .= $renderedTemplate;

            } catch (Exception $e) {
                // @todo[vanch3d] need to build an helper function for that
                $filename = "../docs/$file.pdf";
                if (file_exists($filename)) {

                    $parser = new \Smalot\PdfParser\Parser();
                    $pdf    = $parser->parseFile($filename);
                    $text .= $pdf->getText() . "\n";
                }
                else
                    $errors[] = $e->getMessage();
            }

            /*
            if (preg_match('/\.pdf$/i', $file)) {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf    = $parser->parseFile($file);
                $text .= $pdf->getText() . "\n";
            } else {

                try {
                    // @var \Slim\View $view
                    $view = $this->app->view();
                    $renderedTemplate = $view->fetch('publications/papers/' . $file . '.twig', array(
                        'template_base' => 'publications/template.txt.twig'
                    ));
                    $text .= $renderedTemplate;

                } catch (Exception $e) {
                    //var_dump($e->getMessage());
                    $errors[] = $e->getMessage();
                }


            }*/
        }

        if (!isset($text))
        {
            return array("error" => $errors);
        }
        $arr = array();

        try {

            // @todo[vanch3d] Define a classifier-based tokeniser to deal with n-grams such as "learner model"
            $tok = new NlpTools\Tokenizers\PennTreeBankTokenizer();
            $norm = new NVLEnglish();
            $stop = new NVLStopWords($stopwords);
            $stemmer = new NVLDummyLemmatizer();

            // normalise the raw text
            $d1 = new NlpTools\Documents\RawDocument(json_encode($text));
            $d1->applyTransformation($norm);

            // tokenise the text
            $d = new NlpTools\Documents\TokensDocument($tok->tokenize($d1->getDocumentData()));
            $d->applyTransformation($stop);
            $d->applyTransformation($stemmer);

            // compute the frequency distribution
            $freqDist = new NlpTools\Analysis\FreqDist($d->getDocumentData());

            foreach ($freqDist->getKeyValues() as $key => $val) {
                $arr[] = array('key' => $key, 'value' => $val);
            }
        } catch (Exception $e)
        {
            //var_dump($e->getMessage());
            $errors[] = $e->getMessage();
        }

        return $arr;
    }

    public function getPublicationAnalytics($name)
    {
        /** @var array $files */
        $files = [];
        $files[] = $name;
        $tags = $this->getFrequencyDistribution($files);

        return array(
            "files" => $name,
            "tags" => $tags);

    }

    /**
     * @param $name
     * @return array|mixed
     */
    protected function getProjectAnalytics($name)
    {
        $ret = array("files" => [], "tags" => []);

        try {
            $pubs = $this->getCachedZotero("$name");
            foreach ($pubs['publications'] as $pub) {
                $ret['files'][] = $pub['archive_location'];
            }
            $tags = $this->getFrequencyDistribution($ret['files']);
            $ret['tags'] = $tags;

        } catch (Exception $e) {
        }
        return $ret;

    }

}

class NVLEnglish extends NlpTools\Utils\Normalizers\Normalizer
{
    protected static $dirty = array(
        "&amp;cup;","&#039;","•",'“','-\n','\u0002','\u0003','\u2013','\u2014',' \u00b4\ne','ύ','ώ','ς'
    );
    protected static $clean = array(
        "<=","'","-",'','','fi','fl','-','-','é','υ','ω','σ'
    );

    public function normalize($w)
    {
        return json_decode(str_replace(self::$dirty, self::$clean, mb_strtolower($w, "utf-8")));
    }
}

class NVLStopWords extends NlpTools\Utils\StopWords
{

    /**
     * Remove stop words, defined by
     * - the array given in the constructor
     * - numerical symbols
     * - less than 3 characters
     *
     * @param string $token
     * @return string|null
     */
    public function transform($token)
    {
        return (isset($this->stopwords[$token]) || is_numeric($token) || strlen($token)<3) ? null : $token;
    }
}

class NVLDummyLemmatizer extends NlpTools\Stemmers\Stemmer
{
    // @todo[vanch3d] Not a very good way of doing lemmas, will do for the time being. Try to find a better source
    private static $baseList = array(
        "learners" => "learner",
        "students" => "learner",
        "systems" => "system",
        "student" => "learner",
        "participants" => "participant",
        "lemmas" => "lemma",
        "words" => "word",
        "sentences" => "sentence",
        "essays" => "essay",
        "coordinates" => "coordinate",
        "timelines" => "timeline",
        "episodes" => "episode",
        "occupations" => "occupation",
        "managers" => "manager",
        "representations" => "representation",
        "beliefs" => "belief"
    );

    /**
     * Remove the suffix from $word
     *
     * @return string
     */
    public function stem($word)
    {
        $stem = $word;
        if (isset(self::$baseList[$word]))
            $stem = self::$baseList[$word];

        return $stem;
    }
}