<?php

namespace AppBundle\Twig\Extension;

class CategoryExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'category_position';
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_Function('get_category_position', [$this, 'getPosition']),
        ];
    }

    public function getPosition($position): string
    {
        $pos = $this->getPositions();
        $ret = 'Necunoscut';

        if (($position > 0) && ($position < 15)) {
            $ret = 'Printre primii';
        } else {
            if (($position > 15) && ($position < 30)) {
                $ret = 'Pe la început';
            } else {
                if (($position > 30) && ($position < 45)) {
                    $ret = 'Pe la mijlog';
                } else {
                    if (($position > 45) && ($position < 60)) {
                        $ret = 'Printre ultimii';
                    } else {
                        if ($position > 60) {
                            $ret = 'Ultimul';
                        }
                    }
                }
            }
        }

        foreach ($pos as $key => $value) {
            if ($position === $key) {
                $ret = $value;
            }
        }

        return $ret;
    }

    private function getPositions(): array
    {
        return [
            0  => 'Primul',
            15 => 'Pe la început',
            30 => 'La mijloc',
            45 => 'Pe la final',
            60 => 'Ultimul',
        ];
    }
}
