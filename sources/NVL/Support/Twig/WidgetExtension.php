<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 12/01/2018
 * Time: 20:10
 */

namespace NVL\Support\Twig;


class WidgetExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'NVL/Widget';
    }

    public function getFunctions()
    {
        return [
            'get_slideShare' =>
                new \Twig_SimpleFunction('slideShare', function (string $url, string $format="raw") {
                    return $url;
                }),
            'get_doi' =>
                new \Twig_SimpleFunction('doi', function (string $url) {
                    return "<a href='//dx.doi.org/$url' title=\"Go to article through CrossRef's DOI\">DOI</a>";
                })
        ];
    }

}