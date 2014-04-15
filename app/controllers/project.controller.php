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
	 * 
	 */
	public function index()
	{
		$projIdx = APIController::getProjects();
		$this->render('projects/index',array(
						'projects' => $projIdx
				));
	}
	
	/**
	 * 
	 * @param string $name
	 * @see APIController::getProjects
	 */
	public function project($name)
	{
		$projIdx = APIController::getProjects();
		
		$img = array();
		foreach (glob("assets/projects/".$name."/*.png") as $filename) {
			$img[]=$filename;
		}
		
		if (array_key_exists($name, $projIdx))
		{
			try {
				$this->render('projects/'.$name,array(
						'tmpl_base' => 'template.html.twig',
						'project' => $projIdx[$name],
						'images' => $img
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
	
	public function publications()
	{
		$this->render('publications/showall');
	}

	
	public function getPublication($name)
	{
		global $apiCtrl;
		$item = $apiCtrl->readZoteroCache($name);
		if (!isset($item))
		{
			throw new Exception("fggffgfgfg", 500);
		}
		$item = json_decode($item['csljson']);	
		
		
		
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
			if ($item->DOI) $meta[] = array('citation_doi',$item->DOI);
		}
		var_dump($item,$meta);die();
			
		$this->render('publications/show');
	}
	
	public function exportPublication($name)
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
}