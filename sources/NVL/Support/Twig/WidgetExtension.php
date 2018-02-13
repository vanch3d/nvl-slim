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
                }),
            'pubreader_cite' =>
                new \Twig_SimpleFunction('cite', function (string $ref,string $id) {
                    $txt = htmlspecialchars($ref);
                    return "<a class='bibr tag_bib' rid='$id' href='#$id' co-class='co-refbox' co-rid='$id'>$txt</a>";
                },array('is_safe' => array('html'))),
            'pubreader_note' =>
                new \Twig_SimpleFunction('note', function (string $ref,string $id) {
                    return "<a class='bibr tag_note' rid='$id' href='#$id' co-class='co-refbox' co-rid='$id'>$ref</a>";
                },array('is_safe' => array('html')))
        ];
    }

}