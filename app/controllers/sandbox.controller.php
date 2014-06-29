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
            '1998_Influence_of_Didactic_and_Computational_Constraints_on_ILE_Design_(ITS)'=>'1998.ITS.Influence',
            '1999_Developpement_logiciel_(PNF)'=>'1999.PNF.Calques3D',
            '1999_Prise_en_compte_usager_enseignant_(THESIS)'=>'1999.Thesis.Calques3D',
            '2001_Applying_DeFT_Framework_(AIED)'=>'2001.AIED.Applying',
            '2001_MERs_in_Dynamic_Geometry_(AIEDWS)'=>'2001.ERAIED.DynGeom',
            '2002_Representational_Decisions_(ITS)'=>'2002.ITS.Decisions',
            '2002_Using_Multi_Representational_Design_Framework_(DIVWS)'=>'2002.DynVis.Framework',
            '2004_Multiple_forms_of_dynamic_representation_(LI)'=>'2004.LI.Multiple',
            '2006_Approximate_Modelling_(ITS)'=>'2006.ITS.Approximate',
            '2006_Contingency_Analysis_LM_(MICAI)'=>'2006.MICAI.Contingency',
            '2006_Towards_LM_Engine_(SWEL)'=>'2006.SWEL.Towards',
            '2007_Opening_up_the_Interpretation_Process_(IJAIED)'=>'2007.IJAIED.Interpretation',
            '2008_Kinaesthetic_and_Collaborative_Activities_(Shareit)'=>'2008.Shareit.Kinaesthetic',
            '2008_L4All_Web_Service_Based_System_(LGH)'=>'2008.LGH.L4all',
            '2008_Open_Learner_Modelling_(IUI)'=>'2008.IUI.Keystone',
            '2008_Using_Similarity_Metrics_(ITS)'=>'2008.ITS.Similarity',
            '2009_Connecting_C3D_Maple_(MCS)'=>'2010.MatCom.Connecting',
            '2009_Intrinsic_Integration_(ITeG)'=>'2009.ITAG.Intrinsic',
            '2009_Searching_people_like_me_(ECTEL)'=>'2009.ECTEL.Searching'
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