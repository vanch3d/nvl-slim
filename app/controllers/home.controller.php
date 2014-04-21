<?php

/**
 * Controller for the main pages
 *  
 * @author Nicolas Van Labeke (https://github.com/vanch3d)
 *
 */
class HomeController extends Controller
{
	/**
	 * route for home
	 */
	public function index()
	{
        $projIdx = APIController::getProjects(true);
        $this->render('pages/home',array(
            'projects' => $projIdx
        ));
	}
	
	/**
	 * route for "about me"
	 */
	public function about()
	{
		$this->render('pages/about');
	}

	/**
	 * route for "about this"
	 */
	public function aboutSite()
	{
		$this->render('pages/about.site');
	}
	
	public function showError()
	{
		$this->render('pages/notfound');
	}

	public function showNotFound()
	{
		$this->render('pages/notfound');
	}
	
}