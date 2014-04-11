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

        $img = array();
        foreach (glob("assets/projects/*/*.png") as $filename) {
            $img[]=$filename;
        }

        $this->render('pages/home',array(
            'projects' => $projIdx,
            'test' =>$this->app->config("nvl-slim.slideshare"),
            'cover' => $img[array_rand($img)]
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