<?php
namespace AppBundle\Util;

use AppBundle\Entity\Article;

class ArticleChoiceConverter
{
    public static $types = [
        'Normal' => Article::TYPE_NORM,
        'Video' => Article::TYPE_VIDEO,
        'Fierbinte' => Article::TYPE_HOT,
        'Recomandat' => Article::TYPE_REC,
        'Reclamă' => Article::TYPE_AD,
        'Featured' => Article::TYPE_FEAT,
    ];

    public function getValidTypes()
    {
        $types = [];
        foreach (self::$types as $key => $type) {
            $types[] = $type;
        }

        return $types;
    }
}