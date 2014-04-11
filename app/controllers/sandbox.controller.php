<?php

class SandboxController extends Controller
{

	public function getIsotope()
	{
		$this->render('sandbox/isotope');
	}

	public function redirectLegacy($name)
	{
		$oldarchive=array(
			'1998_Calques_3D,_a_microworld_for_spatial_geometry_learning_(ITS_WS)'=>'1998.ITS.Calques3D',
		);
		if (isset($oldarchive[$name]))
		{
			$url = $this->app->urlFor(
					'publications.named.pdf',
					array('name'=>$oldarchive[$name])
			);
			$this->app->redirect($url);
		}
		else
			$this->app->notFound();
	}
	
}