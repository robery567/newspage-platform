<?php
namespace AppBundle\Util;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Zend\Json\Json;

class BnrExchangeRate
{
    /** @var \DOMDocument */
    protected $xml;
    /** @var Client */
    protected $client;
    /** @var ResponseInterface */
    protected $response;
    /** @var array */
    protected $data;
    /** @var array */
    protected $currencies;

    protected $hostname = 'http://www.bnro.ro';
    protected $document = 'nbrfxrates.xml';

    public function __construct()
    {
        $this->client = new Client();
        $this->currencies = [
            'eur', 'usd', 'chf'
        ];

        $this
            ->fetchData()
            ->parseXml()
        ;
    }

    private function fetchData()
    {
        $this->response = $this->client->get("{$this->hostname}/{$this->document}");
        $this->xml = new \DOMDocument();
        $this->xml->loadXML($this->response->getBody());
        return $this;
    }

    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    private function parseXml()
    {
        $this->data['publish-date'] = $this->xml->getElementsByTagName('PublishingDate')[0]->nodeValue;
        $rates = $this->xml->getElementsByTagName('Rate');

        /** @var \DOMElement $rate */
        foreach ($rates as $rate) {
            $currency = $rate->getAttribute('currency');
            $multiplier = $rate->hasAttribute('multiplier') ? $rate->getAttribute('multiplier') : 1;
            $value = $rate->nodeValue;

            if (in_array(strtolower($currency), $this->currencies)) {
                $this->data['currencies'][$currency] = [
                    'value' => $value,
                    'multiplier' => $multiplier,
                ];
            }
        }

        return $this;
    }

    public function getResponse()
    {
        return $this->data;
    }

    public function getJsonResponse()
    {
        return Json::encode($this->data, true);
    }
}