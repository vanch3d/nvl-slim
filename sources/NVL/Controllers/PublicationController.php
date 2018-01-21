<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 10/01/2018
 * Time: 00:43
 */

namespace NVL\Controllers;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;
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
        set_time_limit(100);
        // @var array $narrative
        $narrative = array(
            "characters" => [],
            "scenes" => [],
            "errors" => []
        );

        $pubs = $this->getPublicationManager()->getData("all", 100);
        while ($pub = array_pop($pubs['publications'])) {

            // Do not incorporate non-english publications
            if (isset($pub['language']) && strcasecmp($pub['language'],'English')) {
                $narrative['errors'][]= "Unable to process ". $pub['archive_location'] . ", not an English source";
                continue;
            }

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

        }

        // remove the keys
        //$narrative['scenes'] = array_values($narrative['scenes']);
        $narrative['characters'] = array_values($narrative['characters']);

        // @todo[vanch3d] Build the proper response
        return $this->getView()->render($response, 'publications/pub.narrative.twig',array(
            'narrative'=> json_encode($narrative)
        ));
    }

    /**
     * Handler for the PubReader version of a publication
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
     * Handler for the plain text version of the publication
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return static
     */
    public function pubExportTXT(Request $request, Response $response, array $args)
    {
        return $this->getView()->render($response,'publications/papers/'.$args['name'].'.twig',array(
            'template_base' => 'publications/template.txt.twig'
        ))->withHeader('Content-Type', 'text/plain');
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
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

    public function redirectLegacy(Request $request, Response $response, array $args)
    {
        // @todo[vanch3d] Build the proper response
        return $this->getView()->render($response, 'site.twig');
    }

}