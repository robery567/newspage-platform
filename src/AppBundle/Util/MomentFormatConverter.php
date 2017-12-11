<?php
namespace AppBundle\Util;

class MomentFormatConverter
{
    private static $formatConvertRules = [
        // year
        'yyyy'   => 'YYYY',
        'yy'     => 'YY',
        'y'      => 'YYYY',
        // day
        'dd'     => 'DD',
        'd'      => 'D',
        // day of week
        'EE'     => 'ddd',
        'EEEEEE' => 'dd',
        // timezone
        'ZZZZZ'  => 'Z',
        'ZZZ'    => 'ZZ',
        // letter 'T'
        '\'T\''  => 'T',
    ];

    public function convert($format)
    {
        return strtr($format, self::$formatConvertRules);
    }
}