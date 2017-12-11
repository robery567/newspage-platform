<?php
namespace AppBundle\Twig\Extension;


use AppBundle\Util\Text;

class TextExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('remove_accents', [Text::class, 'removeAccents']),
        ];
    }
}