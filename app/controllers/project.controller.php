<?php

/**
 * Controller for the project-related pages
 * 
 * @author Nicolas Van Labeke (https://github.com/vanch3d)
 *
 */
class ProjectController extends Controller
{
	/**
	 * route for 'all projects'
	 */
	public function allProjects()
	{
        $projIdx = $this->getProjectDescriptors();
        $this->render('projects/index',array(
            'projects' => $projIdx
        ));
	}

    /**
     * route for the 'story map' visualisation
     */
    public function storyMap()
    {
        $projIdx = $this->getProjectDescriptors();
        $this->render('projects/storymap',array(
            'projects' => $projIdx
        ));
    }

	/**
	 * route for individual projects
	 * @param string $name  The id of an existing project
	 */
	public function project($name)
	{
        $projIdx = $this->isProjectDefined($name);
        if ($projIdx)
		{
			try {
				$this->render('projects/content/'.$name,array(
						'tmpl_base' => 'template.html.twig',
						'project' => $projIdx
				));

			} catch (Twig_Error_Loader $e) {
    			$this->app->flash('error', 'The project you are looking for does not exist. <br>Try one below.');
	    		$this->redirect('project.all');
			}
		}
		else
		{
			$this->app->flash('error', 'The project you are looking for does not exist. <br>Try one below.');
			$this->redirect('project.all');
		}
	}

    /**
     * Route for the project's wordcloud
     * @param $name The id of an existing project
     */
    public function wordCloud($name)
    {
        //$projIdx = $this->getProjectDescriptors();
        $projIdx = $this->isProjectDefined($name);
        if ($projIdx)
            $this->render('projects/template.cloud',array(
                'project' => $projIdx
            ));
    }

    /**
     * route for all publications
     */
    public function allPublications()
	{
        $publications = [];
        try {
            $pubs = $this->getCachedZotero("all", 100);
            $publications = $pubs['publications'];
        } catch (Exception $e) {
        }

        //$uniqueYears = array_unique(array_map(function ($i) {
        //    return $i['issued']['date-parts'];
        //    }, $$publications));
        $uniqueYears = array_unique(array_map(function($v){
            return $v['issued']['date-parts'][0][0];
        },$publications));
        $uniqueTypes = array_unique(array_map(function($v){
            return $v['type'];
        },$publications));

        $this->render('publications/showall',array(
            'publications' => $publications,
            'years' => $uniqueYears,
            'types' => $uniqueTypes
        ));
	}

    public function pubGraph()
    {
        $this->render('publications/graph');
    }

    public function pubMap()
    {
        $this->render('publications/map');
    }

    public function pubReader($name)
    {
        //global $apiCtrl;
        //$idx = $apiCtrl->readZoteroFileIndex();
        //$cache = $idx[$name];

        $cache = $this->isPublicationDefined($name);

        if (!isset($cache))
        {
            throw new Exception("Cannot find the index of this reference", 500);
        }
        //$fullitem = $apiCtrl->readZoteroCache($cache);
        $fullitem = $cache;
        if (!$fullitem)
        {
            throw new Exception("Cannot find this reference", 500);
        }

        //$item = json_decode($fullitem['csljson']);
        $item = json_decode(json_encode($fullitem));
        //$item = $fullitem;


        // build the Highwire Press tags
        $meta = array();
        $meta[] = array('citation_title',$item->title);
        $meta[] = array('citation_publication_date',$item->issued->{'date-parts'}[0][0]);
        foreach ($item->author as $author)
        {
            $meta[] = array('citation_author', $author->family . ", " . $author->given);
        }
        if ($item->type=='paper-conference' || $item->type=='article-journal')
        {
            if ($item->type=='paper-conference')
                $meta[] = array('citation_conference_title',$item->{'container-title'});
            else
            {
                $meta[] = array('citation_journal_title',$item->title);
                $meta[] = array('citation_volume',$item->volume);
                $meta[] = array('citation_issue',$item->issue);
            }
            if ($item->title)
            {
                $pages = explode("-", $item->page);
                if ($pages[0]) $meta[] = array('citation_firstpage',$pages[0]);
                if ($pages[1]) $meta[] = array('citation_lastpage',$pages[1]);
            }
            if (isset($item->DOI)) $meta[] = array('citation_doi',$item->DOI);
        }
        //$item = json_decode($fullitem['csljson'],true);

        try {
            $this->render('publications/papers/'.$name,array(
                'meta' => $meta,
                'item'=> $fullitem
            ));

        } catch (Twig_Error_Loader $e)
        {
            $this->render('publications/papers/default',array(
                'meta' => $meta,
                'item'=> $item
            ));
        }
    }

    public function pubAssets($name,$fig)
    {
        // set the cache for the request
        $this->app->etag($name.'assets'.$fig);
        $this->app->expires('+1 week');

        $cache = $this->isPublicationDefined($name);

        if (!isset($cache))
        {
            throw new Exception("Cannot find the index of this reference", 500);
        }
        $filename = "../docs/$name/".$fig;
        if (!file_exists($filename))
        {
            throw new Exception("file not there", 500);
        }

        //echo mime_content_type($filename);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filename);
        finfo_close($finfo);

        $response = $this->app->response();
        $response['Content-Type'] = $mime;
        $response['X-Powered-By'] = APPLICATION . '/' . VERSION;
        //$response->setStatus(200);
        //$response->setBody(json_encode($body));
        readfile($filename);

    }

    public function pubShow($name)
	{
        $cache = $this->isPublicationDefined($name);

        if (!isset($cache))
        {
            throw new Exception("Cannot find the index of this reference", 500);
        }
        //$fullitem = $apiCtrl->readZoteroCache($cache);
        $fullitem = $cache;
        if (!$fullitem)
        {
            throw new Exception("Cannot find this reference", 500);
        }
        //$item = json_decode($fullitem['csljson']);
        $item = json_decode(json_encode($fullitem));


		// build the Highwire Press tags
		$meta = array();
		$meta[] = array('citation_title',$item->title);
		$meta[] = array('citation_publication_date',$item->issued->{'date-parts'}[0][0]);
		foreach ($item->author as $author)
		{
			$meta[] = array('citation_author', $author->family . ", " . $author->given);
		}
		if ($item->type=='paper-conference' || $item->type=='article-journal')
		{
			if ($item->type=='paper-conference')
				$meta[] = array('citation_conference_title',$item->{'container-title'});
			else
			{
				$meta[] = array('citation_journal_title',$item->title);
				$meta[] = array('citation_volume',$item->volume);
				$meta[] = array('citation_issue',$item->issue);
			}	
			if ($item->title)
			{
				$pages = explode("-", $item->page);
				if ($pages[0]) $meta[] = array('citation_firstpage',$pages[0]);
				if ($pages[1]) $meta[] = array('citation_lastpage',$pages[1]);
			}
			if (isset($item->DOI)) $meta[] = array('citation_doi',$item->DOI);
		}

			
		//$this->render('publications/show', array(
        $this->render('publications/papers/'.$name,array(
            'meta' => $meta,
            'publication' => $item,
            'template_base' => 'publications/show.twig'
        ));
	}
	
	public function pubExportPDF($name)
	{
		$filename = "../docs/$name.pdf";
		if (!file_exists($filename))
		{
			 throw new Exception("file not there", 500);

		}
		
		$response = $this->app->response();
		
		$response['Pragma'] =  'public';
		$response['Expires'] =  '0';
		$response['Cache-Control'] =  'must-revalidate, post-check=0, pre-check=0';
		$response['Content-Type'] =  'application/pdf';
		$response['Content-Transfer-Encoding'] =  'binary';
		$response['Content-Length'] = filesize($filename);
		//$response['Content-Disposition'] = 'attachment; filename="doc01.pdf"';
		$response['X-Powered-By'] = APPLICATION . '/' . VERSION;

		readfile($filename);
	}



    public function pubDistrib($name)
    {
        $stopwords = array("'s",",","(",")",":",";","'",".","%","a", "about", "above", "above", "across", "after", "afterwards", "again", "against", "all", "almost", "alone", "along", "already", "also","although","always","am","among", "amongst", "amoungst", "amount",  "an", "and", "another", "any","anyhow","anyone","anything","anyway", "anywhere", "are", "around", "as",  "at", "back","be","became", "because","become","becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", "bottom","but", "by", "call", "can", "cannot", "cant", "co", "con", "could", "couldnt", "cry", "de", "describe", "detail", "do", "done", "down", "due", "during", "each", "eg", "eight", "either", "eleven","else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", "however", "hundred", "ie", "if", "in", "inc", "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter", "latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my", "myself", "name", "namely", "neither", "never", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "not", "nothing", "now", "nowhere", "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own","part", "per", "perhaps", "please", "put", "rather", "re", "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system", "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thickv", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "un", "under", "until", "up", "upon", "us", "very", "via", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "will", "with", "within", "without", "would", "yet", "you", "your", "yours", "yourself", "yourselves", "the");

        $filename = "../docs/$name.pdf";
        if (!file_exists($filename))
        {
            throw new Exception("file not there", 500);

        }
        $parser = new \Smalot\PdfParser\Parser();
        $pdf    = $parser->parseFile($filename);
        $text = $pdf->getText();

        $tok = new NlpTools\Tokenizers\PennTreeBankTokenizer();
        $norm = new NVLEnglish();
        $stop = new NVLStopWords($stopwords);
        //$stemmer = new NlpTools\Stemmers\LancasterStemmer();

        // normalise the raw text
        $d1 = new NlpTools\Documents\RawDocument(json_encode($text));
        $d1->applyTransformation($norm);

        // tokenise the text
        $d = new NlpTools\Documents\TokensDocument($arr = $tok->tokenize($d1->getDocumentData()));
        $d->applyTransformation($stop);
        //$d->applyTransformation($stemmer);

        // compute the frequency distribution
        $freqDist = new NlpTools\Analysis\FreqDist($d->getDocumentData());
        $arr = array();
        foreach($freqDist->getKeyValues() as $key=>$val)
        {
            $arr[] = array('text'=> $key, 'size'=> $val );
        }

        $this->render('sandbox/test',array(
            'text'=>$d1->getDocumentData(),
            'data'=> array_slice($arr,0,50)
        ));


    }
}

class NVLEnglish extends NlpTools\Utils\Normalizers\Normalizer
{
    protected static $dirty = array(
        '-\n','\u0002','\u0003','\u2013','\u2014',' \u00b4\ne','ύ','ώ','ς'
    );
    protected static $clean = array(
        '','fi','fl','-','-','é','υ','ω','σ'
    );

    public function normalize($w)
    {
        return json_decode(str_replace(self::$dirty, self::$clean, mb_strtolower($w, "utf-8")));
    }
}

class NVLStopWords extends NlpTools\Utils\StopWords
{
    public function transform($token)
    {
        return (isset($this->stopwords[$token]) || is_numeric($token)) ? null : $token;
    }
}