<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 20/01/2018
 * Time: 20:36
 */

namespace NVL\Support\NLP;

use \NlpTools\Utils\StopWords;

class SlimStopWords extends StopWords
{
    /**
     * Remove stop words, defined by
     * - the array given in the constructor
     * - numerical symbols
     * - less than 3 characters
     *
     * @param string $token
     * @return string|null
     */
    public function transform($token)
    {
        return (
            isset($this->stopwords[$token]) ||
            is_numeric($token) ||
            strlen($token)<3) ? null : $token;
    }
}