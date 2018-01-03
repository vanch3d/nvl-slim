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
        if ($projIdx === false) {
            $this->app->notFound();
            return;
        }

        try {
            $pubs = $this->getCachedZotero($name);
            $publications = $pubs['publications'];
            $this->render('projects/content/'.$name,array(
                    'tmpl_base' => 'template.html.twig',
                    'project' => $projIdx,
                    'publications' => $publications
            ));

        } catch (Exception $e) {
            $this->app->notFound();
        }
	}

    /**
     * Route for the project's wordcloud
     * @param string $name The id of an existing project
     */
    public function wordCloud($name)
    {
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
            $this->app->notFound();
            return;
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


    /**
     * Route for the full text reader
     * @param string $name  The identifier of the publication to show
     */
    public function pubReader($name)
    {
        $cache = $this->isPublicationDefined($name);
        if ($cache === false)
        {
            $this->app->notFound();
            return;
        }

        $item = json_decode(json_encode($cache));

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
            if (isset($item->pagr))
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
                'item'=> $item
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
			if (isset($item->page))
			{
				$pages = explode("-", $item->page);
				if ($pages[0]) $meta[] = array('citation_firstpage',$pages[0]);
				if ($pages[1]) $meta[] = array('citation_lastpage',$pages[1]);
			}
			if (isset($item->DOI)) $meta[] = array('citation_doi',$item->DOI);
		}


        try {
		    //$this->render('publications/show', array(
            $this->render('publications/papers/' . $name, array(
                'meta' => $meta,
                'publication' => $item,
                'template_base' => 'publications/show.twig'
            ));
        } catch (Exception $e) {
            $this->render('publications/papers/default', array(
                'meta' => $meta,
                'publication' => $item,
                'template_base' => 'publications/show.twig'
            ));

        }
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


    public function pubExportTXT($name)
    {

        $status = null;
        $response = $this->app->response();
        $response['Content-Type'] =  'text/plain';
        $response['X-Powered-By'] = APPLICATION . '/' . VERSION;


        $this->render('publications/papers/' . $name, array(
            'template_base' => 'publications/template.txt.twig'
        ));

    }



    public function pubDistrib($name)
    {
        $arr = $this->getPublicationAnalytics($name);
        //$arr = $this->getProjectAnalytics("auditorygames");

        $this->render('sandbox/pub.cloud',array(
            'text'=>$arr['files'],
            'data'=> array_slice($arr['tags'],0,250)
        ));

    }
}