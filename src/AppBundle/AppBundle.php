<?php
namespace AppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class AppBundle
 * @package AppBundle
 *
 * Prior to Symfony 4, everything in Symfony is a bundle (personally, I'm glad that they got rid of "bundle this, bundle that" philosophy),
 * even the business logic.
 *
 * So, this class declares that everything that is in this directory is part of AppBundle and Symfony automatically
 * registers everything under AppBundle namespace (in DI, Cache, Config, Templates, ...)
 *
 * This class is useful in a way that it permits to us to define constants that can be used in every service that we defiine.
 */
class AppBundle extends Bundle
{
    const NEWS_ONE_COLS = 1;
    const NEWS_TWO_COLS = 2;
}
