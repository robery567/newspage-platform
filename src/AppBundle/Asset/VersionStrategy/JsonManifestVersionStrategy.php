<?php
namespace AppBundle\Asset\VersionStrategy;

use AppBundle\Exception\MissingAssetManifestException;
use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

/**
 * Reads the versioned path of an asset from a JSON manifest file from remote URL.
 * For example, the manifest file might look like this:
 *     {
 *         "main.js": "main.abc123.js",
 *         "css/styles.css": "css/styles.555abc.css"
 *     }
 * You could then ask for the version of "main.js" or "css/styles.css".
 */
class JsonManifestVersionStrategy implements VersionStrategyInterface
{
    private $manifestPath;
    private $manifestData;

    /**
     * @param string $manifestPath Absolute path to the manifest file
     */
    public function __construct($manifestPath)
    {
        $this->manifestPath = $manifestPath;
    }

    /**
     * With a manifest, we don't really know or care about what
     * the version is. Instead, this returns the path to the
     * versioned file.
     */
    public function getVersion($path): string
    {
        return $this->applyVersion($path);
    }

    public function applyVersion($path): string
    {
        return $this->getManifestPath($path) ?: $path;
    }

    /**
     * This method validates if given path is an URL.
     *
     * @param string $path
     * @return bool
     */
    private function detectRemoteUri(string $path): bool
    {
        return filter_var($path, FILTER_VALIDATE_URL);
    }

    /**
     * This method reads manifest.json from remote. If file is not found (e.g.: webpack is compiling new assets), then
     * the legacy maintenance page is displayed.
     *
     * @param string $path
     * @return string
     */
    private function getManifestPath(string $path): string
    {
        if (null === $this->manifestData) {
            if (!$this->detectRemoteUri($this->manifestPath)) {
                throw new \RuntimeException(sprintf('Asset manifest URL "%s" is invalid.', $this->manifestPath));
            }

            $this->manifestData = @file_get_contents($this->manifestPath);
            if ($this->manifestData === false) {
                show_maintenance('missing_webpack_manifest');
            }

            if (0 < json_last_error()) {
                throw new \RuntimeException(sprintf('Error parsing JSON from asset manifest file "%s" - %s',
                    $this->manifestPath, json_last_error_msg()));
            }
        }

        return isset($this->manifestData[$path]) ? $this->manifestData[$path] : false;
    }
}