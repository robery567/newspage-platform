<?php
namespace AppBundle\WebService;


use AppBundle\Util\Settings;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MmttWebService
{
    /** @var Settings */
    private $settings;

    use ContainerAwareTrait;

    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
        $this->settings = $this->container->get(Settings::class);
    }

    public function getThumbnail(string $contentHtml): string
    {
        $htmlData = $this->parseStringAsHtml($contentHtml);
        $mediaUrl = $this->getFirstImage($htmlData);
        $info = $this->getRequiredValues($mediaUrl);

        $response = $this->requestFileTransfer($info);
        $results = $this->parseResponseBody($response);

        return $results['location'];
    }

    private function requestFileTransfer(array $info): ResponseInterface
    {
        $client = new Client([
            'base_uri' => \GuzzleHttp\json_decode($this->settings->get('api.base_url')),
        ]);

        $uri = "/gallery/thumbnail/{$info['articleId']}/{$info['mediaFilename']}";

        try {
            $request = $client->get($uri);
            return $request;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function parseResponseBody(ResponseInterface $response): array
    {
        return \GuzzleHttp\json_decode($response->getBody(), true);
    }

    private function getRequiredValues(string $mediaUrl): array
    {
        $bits = explode('/', $mediaUrl);
        $mediaFilename = end($bits);
        $articleId = prev($bits);

        return [
            'articleId' => $articleId,
            'mediaFilename' => $mediaFilename,
        ];
    }

    private function parseStringAsHtml(string $bunchOfHtml): \DOMDocument
    {
        $doc = new \DOMDocument();
        $doc->loadHTML($bunchOfHtml);

        return $doc;
    }

    private function getFirstImage(\DOMDocument $document): string
    {
        $images = $document->getElementsByTagName('img');
        $firstImage = $images->item(0);

        return $firstImage->getAttribute('src');
    }
}