<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 10/01/2018
 * Time: 00:43
 */

namespace NVL\Controllers;
use DOMDocument;
use League\HTMLToMarkdown\HtmlConverter;
use PHPUnit\Runner\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RunTracy\Helpers\Profiler\Profiler;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;
use Symfony\Component\VarDumper\VarDumper;
use Tracy\Debugger;


class PublicationController extends Controller
{

    /**
     * Handler for rendering all publications
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function allPublications(Request $request, Response $response, array $args)
    {
        try {
            $pubs = $this->getPublicationManager()->getData("all", 100);
            $publications = $pubs['publications'] ?? [];
            $uniqueYears = array_unique(array_map(function($v){
                return $v['issued']['date-parts'][0][0];
            },$publications));
            $uniqueTypes = array_unique(array_map(function($v){
                return $v['type'];
            },$publications));

        } catch (\Exception $e) {
        }

        return $this->getView()->render($response, 'publications/showall.twig',array(
            'publications' => $publications ?? [],
            'years' => $uniqueYears ?? [],
            'types' => $uniqueTypes ?? []
        ));
    }

    /**
     * Handler for the co-authorship network
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function pubNetwork(Request $request, Response $response, array $args)
    {
        return $this->getView()->render($response, 'publications/pub.network.twig');
    }

    /**
     * Handler for the research narrative graph
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function pubNarrative(Request $request, Response $response, array $args)
    {
        // set the cache for the request
        //$response = $this->getCache()->withEtag($response,'nvl-slim.narrative');
        //$response = $this->getCache()->withExpires($response,'+2 week');

        set_time_limit(100);
        // @var array $narrative
        $narrative = array(
            "characters" => [],
            "scenes" => [],
            "errors" => []
        );

        Profiler::start();
        Profiler::start("getData");
        $pubs = $this->getPublicationManager()->getData("all", 100);
        Profiler::finish("getData");
        while ($pub = array_pop($pubs['publications'])) {

            // Do not incorporate non-english publications
            if (isset($pub['language']) && strcasecmp($pub['language'],'English')) {
                $narrative['errors'][]= "Unable to process ". $pub['archive_location'] . ", not an English source";
                continue;
            }

            Profiler::start("Publication " . count($pubs['publications']));
            // compute Freq Dist
            $tags = $this->getPublicationManager()->getPublicationAnalytics($pub['archive_location']);

            if (!empty($tags->errors)) {
                $narrative['errors'][]=$tags->errors;
                continue;
            }

            // only take the top X n-grams
            $shortList = array_slice($tags->freq,0,10);
            foreach ($shortList as $tag)
            {
                $count = $tag['value'];
                $enum = 1;
                $affiliation = "other";
                if (isset($narrative['characters'][$tag['key']]['count']))
                {
                    $count += $narrative['characters'][$tag['key']]['count'];
                    $enum += $narrative['characters'][$tag['key']]['enum'];
                }
                if ($enum >=2 ) $affiliation = "light";
                if ($enum >=5 ) $affiliation = "dark";
                // reformat the output to match the narrative plugin
                // @todo[vanch3d] the plugin also wrangles the data - check for redundancy
                $narrative['characters'][$tag['key']] = array(
                    "id" => $tag['key'],
                    "name" => $tag['key'],
                    "affiliation" => $affiliation,
                    "count" => $count,
                    "enum" => $enum
                );
                $narrative['scenes'][$pub['archive_location']][] = $tag['key'];
            }

            Profiler::finish("Publication " . count($pubs['publications']));


        }

        // remove the keys
        //$narrative['scenes'] = array_values($narrative['scenes']);
        $narrative['characters'] = array_values($narrative['characters']);

        Profiler::finish();

        // @todo[vanch3d] Build the proper response
        return $this->getView()->render($response, 'publications/pub.narrative.twig',array(
            'narrative'=> json_encode($narrative)
        ));
    }

    /**
     * Handler for the PubReader version of a given publication
     * @param Request  $request
     * @param Response $response
     * @param array    $args    The attribute "name" contains the id of the publication
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     *
     * @todo[vanch3d] Check the Highwire meta tags for bugs and completion
     */
    public function pubReader(Request $request, Response $response, array $args)
    {
        $publications = $this->getPublicationManager()->isDefined($args["name"]);
        If (false === $publications)
            return $this->notFound($request,$response,new \Exception("The publication does not exist"));

        $item = json_decode(json_encode($publications));

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

        return $this->getView()->render($response,'publications/papers/'.$args['name'].'.twig',array(
                'meta' => $meta,
                'item'=> $item
            ));

    }

    /**
     * Handler for the PDF version of a given publication
     * @param Request  $request
     * @param Response $response
     * @param array    $args    The attribute "name" contains the id of the publication
     * @return Response
     */
    public function pubExportPDF(Request $request, Response $response, array $args)
    {
        $filename = $args['name'];
        $file = DIR . "resources/docs/$filename.pdf";
        if (!file_exists($file))
        {
            throw new Exception("file not there", 500);

        }

        $response = $response
            ->withHeader("Content-Type","application/pdf");

        return $response->write(@file_get_contents($file));
    }

    /**
     * Handler for the plain text version of a given publication
     * @param Request  $request
     * @param Response $response
     * @param array    $args    The attribute "name" contains the id of the publication
     * @return Response
     */
    public function pubExportTXT(Request $request, Response $response, array $args)
    {
        return $this->getView()->render($response,'publications/papers/'.$args['name'].'.twig',array(
            'template_base' => 'publications/template.txt.twig'
        ))->withHeader('Content-Type', 'text/plain');
    }

    public function pubShow(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $this->getView()->render($response, 'site.twig');
    }

    public function pubDistribution(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $this->getView()->render($response, 'site.twig');
    }

    /**
     * Handler for the assets (figures) of a given publication
     * @param Request  $request
     * @param Response $response
     * @param array    $args    The attribute "name" contains the id of the paper
     *                          The attribute "fig" contains the full name (fig.ext) of the figure
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function pubAssets(Request $request, Response $response, array $args)
    {
        try {
            $publications = $this->getPublicationManager()->isDefined($args["name"]);
            if (false === $publications)
                throw new \Exception("not found");

            $name = $args['name'];
            $fig = $args['fig'];
            $filename = realpath(DIR . "resources/docs/$name/$fig");
            if (false === $filename)
                throw new \Exception("not found");

            $image = @file_get_contents($filename);
            if (false === $image)
                throw new \Exception("not found");

            $response->write($image);
            return $response->withHeader('Content-Type', FILEINFO_MIME_TYPE);
        } catch (\Exception $e) {
            $this->notFound($request,$response,$e);
        }
    }

    /**
     * Handler for the old version of the papers (redirect to Slim-based route)
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function redirectLegacy(Request $request, Response $response, array $args)
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



        if (!isset($oldarchive[$args['file']]))
            return $this->notFound($request,$response,new \Exception("Not found"));

        $path = $this->getRouter()->pathFor('publications.named.pdf',
                ['name' => $oldarchive[$args['file']]]);
        $uri = $request->getUri()->withPath($path);

        return $response->withHeader("Location",$path)
            ->withRedirect($uri,301);

    }

    public function pubExportHTML(Request $request, Response $response, array $args)
    {
        $file = $args["name"];
        $publications = $this->getPublicationManager()->isDefined($file);
        If (false === $publications)
            return $this->notFound($request,$response,new \Exception("The publication does not exist"));

        $item = json_decode(json_encode($publications));

        // Get the TWIG template rendered in basic HTML
        $renderedTemplate = $this->getView()->fetch("publications/papers/$file.twig", array(
            'template_base' => 'publications/template.basichtml.twig',
            'item'=> $item
            ));



        $dom = new DOMDocument;
        $dom->loadHTML($renderedTemplate);

        // clear all unnecessary spaces
        $elts = $dom->getElementsByTagName('p');
        foreach ($elts as $elt) {
            $text = $elt->nodeValue;
            $string = preg_replace('/\s+/', ' ', $text);
            $elt->nodeValue = htmlentities($string);

        }

        // remove all figures and tables
        // @todo[vanch3d] Find a good way of making them compatible with Markdown
        $elts = $dom->getElementsByTagName('div');
        for ($i = $elts->length; --$i >= 0; ) {
            $elt = $elts->item($i);
            $text = $elt->getAttribute("class");
            if (stripos($text, "fig iconblock") !== false) {
                //Debugger::barDump($text);
                $elt->parentNode->removeChild($elt);
            }
        }
        $html = $dom->saveHTML();

        // convert to Markdown
        $converter = new HtmlConverter();
        $converter->getConfig()->setOption('strip_tags', true);
        $converter->getConfig()->setOption('header_style', 'atx');

        $markdown = $converter->convert($html);
        return $response
            ->withHeader('Content-Type', 'text/markdown')
            ->write($markdown);

    }


}