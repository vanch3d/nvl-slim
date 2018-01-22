<?php
/**
 * Created by PhpStorm.
 * User: vanch3d <nicolas.github@calques3d.org>
 * Date: 20/01/2018
 * Time: 20:40
 */

namespace NVL\Support\NLP;


use NlpTools\Stemmers\Stemmer;

class DummyLemmatizer extends Stemmer
{
    // @todo[vanch3d] Not very good, will do for the time being. Try to find a better source
    private static $baseList = array(
        "learners" => "learner",
        "students" => "learner",
        "student" => "learner",
        "systems" => "system",
        "participants" => "participant",
        "lemmas" => "lemma",
        "words" => "word",
        "sentences" => "sentence",
        "essays" => "essay",
        "coordinates" => "coordinate",
        "timelines" => "timeline",
        "episodes" => "episode",
        "occupations" => "occupation",
        "managers" => "manager",
        "representations" => "representation",
        "beliefs" => "belief"
    );

    /**
     * Remove the suffix from $word
     *
     * @return string
     */
    public function stem($word)
    {
        $stem = $word;
        if (isset(self::$baseList[$word]))
            $stem = self::$baseList[$word];

        return $stem;
    }
}