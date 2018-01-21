<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 10/01/2018
 * Time: 12:49
 */

namespace NVL\Data;

use citeproc;
use Interop\Container\ContainerInterface;
use Monolog\Logger;
use NlpTools\Analysis\FreqDist;
use NlpTools\Documents\RawDocument;
use NlpTools\Documents\TokensDocument;
use NlpTools\Tokenizers\PennTreeBankTokenizer;
use NVL\Support\NLP\DummyLemmatizer;
use NVL\Support\NLP\SimpleNormaliser;
use NVL\Support\NLP\SlimStopWords;
use Requests;
use Slim\Http\Response;
use Slim\Views\Twig;
use Smalot\PdfParser\Parser;
use Tracy\Debugger;
use Zotero_Feed;
use Zotero_Library;

/**
 * Class ZoteroManager
 * @package NVL\Data
 *
 * @todo[vanch3d] Create an interface in case of different publication manager (eg Mendeley)
 */
class ZoteroManager
{
    protected $container = null;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getLogger() : Logger
    {
        return $this->container->get("logger");
    }

    /**
     * @param string $project_id    The id of the project to retrieve publications from (or 'all')
     * @param int $limit            The maximum number of publications to retrieve
     * @param string $csl_file      The CSL file to use for formatting the publications
     * @return array                A list of publications
     * @throws \Exception
     *
     * @todo[vanch3d] Make sure all URLs are fully qualified (eg PDF, URL, ...)
     */
    public function getData($project_id="all", $limit = 20, $csl_file="umuai-nvl.csl")
    {
        $ff = $this->getZoteroRecord($project_id,$limit);
        return $ff;
    }

    /**
     * @param string $id
     * @return array|bool
     * @throws \Exception
     */
    public function isDefined(string $id)
    {
        if (empty($id)) return false;
        try {
            $items = $this->getData("all", 100);
            $pubs = $items['publications'];

            $item=array_filter($pubs,function($v) use($id){
                return($v['archive_location']===$id);
            });


            if (!isset($item) || empty($item))
                return false;
            return reset($item);

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
    /**
     * Return the path of the cache file for the given bib item
     * @param string $name	the ID of the bib to cache
     * @return string
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    private function getZoteroCacheFilename($name)
    {
        $settings = $this->container->get("settings");
        return $settings['view']['twig']['cache']. './zotero/'.$name.".json";
    }

    /**
     * Write the content of the bib item into the given cache file
     * @param string 	$name
     * @param string 	$data
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

    private function readZoteroCache($name)
    {
        $file = $this->getZoteroCacheFilename($name);
        return json_decode(@file_get_contents($file),true);
    }

    private function getZoteroConfig()
    {
        $settings = $this->container->get("settings");
        return $settings['nvl-slim']['zotero'];
    }

    private function getZoteroRecord($project_id="all", $limit = 20, $csl_file="umuai-nvl.csl")
    {
        $cfg = $this->getZoteroConfig();
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

        $this->getLogger()->notice("Retrieving publications",array(
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
            $this->getLogger()->notice("Cached version exists",array(
                "version" => $cache['last-modified-version']
            ));
            $headers['If-Modified-Since-Version'] = $cache['last-modified-version'];
        }
        else
        {
            $this->getLogger()->notice("No cached version");
        }
        $this->getLogger()->notice("Generate request",$headers);


        // Send the request to the server (bypassing Zotero_Library)
        $request = Requests::get($url,$headers);
        $this->getLogger()->notice("Request sent & received",array(
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
            $csl_data ='../sources/Zotero/styles/'.$csl_file;
            $csl_data = file_get_contents($csl_data);
            $citeproc = new citeproc($csl_data);

            // extract the CSLJSON object as the basis for the publications
            foreach ($fetchedItems as $item)
            {
                $pubitem = json_decode($item->subContents['csljson'],true);
                $pubitem['id']=$item->itemKey;

                $loc = $pubitem['archive_location'];
                if (!in_array($pubitem['type'],array("software") )) {
                    $pubitem['PDF'] = $this->container->get("router")->pathFor('publications.named.pdf', array('name' => $loc));
                }

                $path = $this->container->get('settings')['view']['template_path'];
                $templatePathname = $path . 'publications/papers/'.$loc.'.twig';
                if (is_file($templatePathname))
                {
                    $pubitem['PubReader'] = $this->container->get("router")->pathFor('publications.named.pubReader',array('name'=>$loc));
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
                        //$projIdx = $this->isProjectDefined($name2);
                       if ($name2) {
                            $pubitem['project']['id'] = $name2;
                            $pubitem['project']['url'] = $this->container->get("router")->pathFor('project.named', array('name' => $name2));
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

            $this->writeZoteroCache("zotero".(isset($data['tag']) ? $data['tag'] : "ALL").$limit,$pubs);

        }
        else if ($request->status_code == 304)
        {
            $this->getLogger()->notice("no change since last request, cache used",array(
            ));
            $pubs = $cache;

        }
        else
        {
            $this->getLogger()->notice("ERROR",array(
                "status" => $request->status_code
            ));

            throw new \Exception($request->body,$request->status_code);
        }

        return $pubs;
    }

    /**
     * @param array $files
     * @return object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    private function getFrequencyDistribution(array $files)
    {
        $stopwords = array_merge(
            //array("#","-","...",">","=","&","'s",",","(",")",":",";","'",".","%","a","”","''","“","[","]","|"),
            array("pp.","i.e.","e.g.","j","j.","p","amp","quot","etc."),
            file(DIR . "sources/NVL/Support/NLP/stopwords.data", FILE_IGNORE_NEW_LINES)
        );

        $data= (object)[
            'text' => null,
            'freq' => [],
            'errors' => [],
            'files' => []
        ];

        /** @var Twig $view */
        $view = $this->container->get("view");

        foreach ($files as $file) {

            try {
                // first, try to get the TWIG template rendered in TXT
                $renderedTemplate = $view->fetch("publications/papers/$file.twig", array(
                    'template_base' => 'publications/template.txt.twig'
                ));
                $data->text .= $renderedTemplate;
                $data->files[] = "$file.twig";

            } catch (\Exception $e) {

                // If not, try to get the PDF
                $filename = DIR . "resources/docs/$file.pdf";
                if (file_exists($filename)) {

                    $parser = new Parser();
                    $pdf    = $parser->parseFile($filename);

                    $txt = $pdf->getText();
                    $json = json_encode($txt);
                    if (false === $json)
                    {
                        //Debugger::barDump(json_last_error_msg());
                        $data->errors[] = $e->getMessage();
                        continue;
                    }

                    $data->text .= $txt;
                    $data->files[] = "$file.pdf";

                }
                else {
                    // to do, get the online publication (curl)
                    $data->errors[] = $e->getMessage();
                }
            }
        }
        if (!isset($data->text))
        {
            return $data;
        }

        try {
            // @todo[vanch3d] Define a classifier-based tokeniser to deal with n-grams such as "learner model"
            $tok = new PennTreeBankTokenizer();
            $norm = new SimpleNormaliser();
            $stop = new SlimStopWords($stopwords);
            $stemmer = new DummyLemmatizer();

            // normalise the raw text
            $d1 = new RawDocument(json_encode($data->text));
            $d1->applyTransformation($norm);

            // tokenise the text
            $d = new TokensDocument($tok->tokenize($d1->getDocumentData()));
            $d->applyTransformation($stop);
            $d->applyTransformation($stemmer);

            // compute the frequency distribution
            $freqDist = new FreqDist($d->getDocumentData());

            // reformat output as paired key/value attributes
            foreach ($freqDist->getKeyValues() as $key => $val) {
                $data->freq[] = array('key' => $key, 'value' => $val);
            }

        } catch (\Exception $e) {
            $data->errors[] = $e->getMessage();
        }
        unset($data->text);
        return $data;
    }

    public function getPublicationAnalytics($name)
    {
        return $this->getFrequencyDistribution([$name]);
    }

    public function getProjectAnalytics($name)
    {
        $pubs = $this->getZoteroRecord($name);
        $files = [];
        foreach ($pubs['publications'] as $pub) {

            // Do not incorporate non-english publications
            if (isset($pub['language']) && strcasecmp($pub['language'],'English')) {
                continue;
            }

            $files[] = $pub['archive_location'];
        }

        return $this->getFrequencyDistribution($files);
    }



}