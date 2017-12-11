<?php
namespace AppBundle\Util;

use AppBundle\Entity\Ad;

class AdChoiceConverter
{

    public static $positions = [
        'Logo, stânga' => 'header_left_1',
        'Logo, dreapta' => 'header_right_1',
        'Meniu, dedesubt' => 'menu_bottom_1',
        'Articol, dreapta' => 'article_right_1',
        'Homepage - Articol Principal, dedesubt' => 'homepage-featured_bottom_1',
        'Homepage - Articol Video, deasupra' => 'homepage-video_top_1',
        'Homepage - Articol Video, dedesubt' => 'homepage-video_bottom_1',
    ];

    public function getValidPositions()
    {
        $positions = [];
        foreach (self::$positions as $key => $position) {
            $positions[] = $position;
        }

        return $positions;
    }

    public static function getDisplayCountFor($pos = null)
    {
        $return = [];

        if (is_null($pos)) {
            foreach (self::$positions as $key => $position) {
                $_const = strtoupper(str_replace('-', '_', $position));
                $_class = Ad::class;
                $return[$position] = @constant("{$_class}::COUNT_{$_const}") ?? 0;
            }
        } else {
            $_const = strtoupper(str_replace('-', '_', $pos));
            $_class = Ad::class;
            $return[$pos] = @constant("{$_class}::COUNT_{$_const}") ?? 0;
        }

        return $return;
    }
}