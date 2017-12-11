<?php
namespace AppBundle\Util;


use Dotenv\Loader;

class DotenvLoader extends Loader
{
    public function sanitize($name, $value = null)
    {
        return $this->normaliseEnvironmentVariable($name, $value);
    }

    public function isComment($line)
    {
        return $this->isComment($line);
    }

    public function isSetter($line)
    {
        return $this->looksLikeSetter($line);
    }
}