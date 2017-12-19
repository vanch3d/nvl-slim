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
	public function home()
	{
        $projIdx = $this->getProjectDescriptors(true);
        $publications = [];
        try {
            $pubs = $this->getCachedZotero("all", 4);
            $publications = $pubs['publications'];
        } catch (Exception $e) {
        }

        $this->render('pages/home',array(
            'projects' => $projIdx,
            'publications' => $publications
        ));
	}
	
	/**
	 * route for "about me"
	 */
	public function aboutMe()
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

    /**
     * 500
     */
    public function showError()
	{
		$this->render('pages/notfound');
	}

    /**
     * 404
     */
    public function showNotFound()
	{
		$this->render('pages/notfound');
	}

    /**
     * route for 'search'
     */
    public function search()
    {
        $projIdx = $this->getProjectDescriptors(true);
        $this->render('pages/search',array(
            'projects' => $projIdx
        ));
    }
	
}