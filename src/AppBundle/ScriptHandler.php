<?php
namespace AppBundle;

use Composer\Script\Event;
use Symfony\Component\Process\Process;

/**
 * @author Petru Szemereczki
 * Forked from SensioDistributionBundle with more features.
 * @author Jordi Boggiano <j.boggiano@seld.be>
 *
 * We wrapped a console (shell script) around console (the php file) in order to use dev environment with
 * Application console, so I had to take the original ScriptHandler from SensioDistributionBundle and
 * alter it accordingly.
 */
class ScriptHandler
{
    /**
     * Composer variables are declared static so that an event could update
     * a composer.json and set new options, making them immediately available
     * to forthcoming listeners.
     *
     * @deprecated Originally this was made for upgrading Symfony 2 to Symfony 3, so it's not needed anymore.
     */
    protected static $options = array(
        'symfony-app-dir'        => 'app',
        'symfony-web-dir'        => 'web',
        'symfony-assets-install' => 'hard',
        'symfony-cache-warmup'   => false,
    );

    /**
     * Clears the Symfony cache.
     *
     * @param Event $event
     */
    public static function clearCache(Event $event)
    {
        $options = static::getOptions($event);
        $consoleDir = static::getConsoleDir($event, 'clear the cache');

        if (null === $consoleDir) {
            return null;
        }

        static::executeCommand($event, $consoleDir, 'cache:clear --no-warmup', $options['process-timeout']);
    }

    /**
     * Warms up the Symfony cache.
     *
     * @param Event $event
     */
    public static function warmupCache(Event $event)
    {
        $options = static::getOptions($event);
        $consoleDir = static::getConsoleDir($event, 'warm up the cache');

        if (null === $consoleDir) {
            return null;
        }

        static::executeCommand($event, $consoleDir, 'cache:warmup', $options['process-timeout']);
    }

    /**
     * Gets options from composer.json extra field and merge with default options from this class.
     *
     * @param Event $event
     * @return array
     *
     * @TODO refactor code to remove static::$options usage.
     */
    protected static function getOptions(Event $event): array
    {
        $options = array_merge(static::$options, $event->getComposer()->getPackage()->getExtra());

        $options['symfony-assets-install'] = getenv('SYMFONY_ASSETS_INSTALL') ?: $options['symfony-assets-install'];
        $options['symfony-cache-warmup'] = getenv('SYMFONY_CACHE_WARMUP') ?: $options['symfony-cache-warmup'];

        $options['process-timeout'] = $event->getComposer()->getConfig()->get('process-timeout');
        $options['vendor-dir'] = $event->getComposer()->getConfig()->get('vendor-dir');

        return $options;
    }

    /**
     * Returns a relative path to the directory that contains the `console` command.
     *
     * @param Event  $event      The command event
     * @param string $actionName The name of the action
     *
     * @return string|null The path to the console directory, null if not found
     */
    protected static function getConsoleDir(Event $event, $actionName): ?string
    {
        $options = static::getOptions($event);

        if (!static::hasDirectory($event, 'symfony-bin-dir', $options['symfony-bin-dir'], $actionName)) {
            return null;
        }

        return $options['symfony-bin-dir'];
    }

    /**
     * Checks if given directory exists per composer.json configuration
     *
     * @param Event $event
     * @param $configName
     * @param $path
     * @param $actionName
     * @return bool
     *
     * @deprecated
     */
    protected static function hasDirectory(Event $event, $configName, $path, $actionName): bool
    {
        @trigger_error(sprintf('%s is deprecated since version 0.x and will be removed in 1.0.', __METHOD__), E_USER_DEPRECATED);

        if (!is_dir($path)) {
            $event->getIO()->write(sprintf('The %s (%s) specified in composer.json was not found in %s, can not %s.',
                    $configName, $path, getcwd(), $actionName));

            return false;
        }

        return true;
    }

    /**
     * Executes an Application console command.
     *
     * @param Event $event
     * @param string $consoleDir
     * @param string $cmd
     * @param int $timeout
     */
    protected static function executeCommand(Event $event, string $consoleDir, string $cmd, int $timeout = 300)
    {
        $console = escapeshellarg($consoleDir.'/console');
        if ($event->getIO()->isDecorated()) {
            $console .= ' --ansi';
        }

        $process = new Process($console.' '.$cmd, null, null, null, $timeout);
        $process->run(function ($type, $buffer) use ($event) {
            $event->getIO()->write($buffer, false);
        });
        if (!$process->isSuccessful()) {
            throw new \RuntimeException(sprintf("An error occurred when executing the \"%s\" command:\n\n%s\n\n%s",
                escapeshellarg($cmd), self::removeDecoration($process->getOutput()),
                self::removeDecoration($process->getErrorOutput())));
        }
    }

    /**
     * Removes any output decorations.
     *
     * @param string $string
     * @return string|null
     */
    private static function removeDecoration(string $string): ?string
    {
        return preg_replace("/\033\[[^m]*m/", '', $string);
    }

    /**
     * Installs the assets under the web root directory.
     *
     * For better interoperability, assets are copied instead of symlinked by default.
     * Even if symlinks work on Windows, this is only true on Windows Vista and later,
     * but then, only when running the console with admin rights or when disabling the
     * strict user permission checks (which can be done on Windows 7 but not on Windows
     * Vista).
     *
     * @param Event $event
     * @TODO refactor deprecated code
     */
    public static function installAssets(Event $event)
    {
        $options = static::getOptions($event);
        $consoleDir = static::getConsoleDir($event, 'install assets');

        if (null === $consoleDir) {
            return null;
        }

        $webDir = $options['symfony-web-dir'];

        $symlink = '';
        if ('symlink' == $options['symfony-assets-install']) {
            $symlink = '--symlink ';
        } elseif ('relative' == $options['symfony-assets-install']) {
            $symlink = '--symlink --relative ';
        }

        if (!static::hasDirectory($event, 'symfony-web-dir', $webDir, 'install assets')) {
            return null;
        }

        static::executeCommand($event, $consoleDir, 'assets:install '.$symlink.escapeshellarg($webDir),
            $options['process-timeout']);
    }
}
