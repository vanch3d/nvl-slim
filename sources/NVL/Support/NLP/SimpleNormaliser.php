<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 20/01/2018
 * Time: 20:33
 */

namespace NVL\Support\NLP;


use NlpTools\Utils\Normalizers\Normalizer;

class SimpleNormaliser extends Normalizer
{
    protected static $dirty = array(
        "&amp;cup;","&#039;","•",'“','-\n','\u0002','\u0003','\u2013','\u2014',' \u00b4\ne','ύ','ώ','ς'
    );
    protected static $clean = array(
        "<=","'","-",'','','fi','fl','-','-','é','υ','ω','σ'
    );

    /**
     * Transform the word according to the class description
     *
     * @param  string $w The word to normalize
     * @return string
     */
    public function normalize($w)
    {
        return json_decode(str_replace(self::$dirty, self::$clean, mb_strtolower($w, "utf-8")));
    }
}