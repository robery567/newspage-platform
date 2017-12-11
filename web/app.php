<?php
/**
 * This file bootstraps the entire Symfony application for web usage.
 *
 * It does some environment checks and automatically puts the website under immediate maintenance if anything fails, and
 * logs to PHP's error_log why it did that, in order to see why the code did that.
 *
 * Then initiates the AppKernel, hooks the request, does the other workflow, sends the response, then it
 * terminates the request.
 */

use Symfony\Component\HttpFoundation\Request;

/**
 * Displays the maintenance mode page.
 * This function is written in legacy mode because there won't be any Response class if vendor is missing.
 *
 * @param string $message
 */
function show_maintenance($message)
{
    $response =<<<EOR
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Mentenanță</title>
    </head>
    <body>
        <h1>Mentenanță</h1>
        <p>Momentan, site-ul se află în mentenanță. Vă rugăm reveniți mai târziu!</p>
    </body>
</html>
EOR;

    header('HTTP/1.1 500 Internal Server Error', true, 500);
    header('Content-Type: text/html');

    error_log($message);
    echo $response;
    exit;
}

/** @var string $autoload */
$autoload = dirname(__DIR__) . '/vendor/autoload.php';

/** No autoloader found, 99.9% chances that Composer packages are not installed, meaning that Maintenance is thrown. */
if (!file_exists($autoload)) {
    show_maintenance('missing_vendor_directory');
}

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require $autoload;

/** @var \Dotenv\Dotenv $env Reads and parse the .env file */
$env = new \Dotenv\Dotenv(dirname(__DIR__));
$env->load();

/** @TODO use $_SERVER in favor of getenv() */
/** @var string $environment The environment defined in .env file */
$environment = getenv('APP_ENV');
/** @var int $maintenance The maintenance mode defined in .env file. Must be integer because getenv() fucks with this. */
$maintenance = getenv('APP_MAINTENANCE');

/** Maintenance mode is defined in .env, so it's pretty urgent. */
if ($maintenance == 1) {
    show_maintenance('envvar_app_maintenance');
}

/** If we're in development environment, enable Debugging */
if ($environment == 'dev') {
    \Symfony\Component\Debug\Debug::enable();
}

/** @var AppKernel $kernel Instantiates the Application's Kernel */
$kernel = new AppKernel($environment, true);

/** We enable cache only for production environment, so it won't affect the development phase. */
if ($kernel->getEnvironment() == 'prod') {
    $kernel = new AppCache($kernel);
    Request::enableHttpMethodParameterOverride();
}

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
