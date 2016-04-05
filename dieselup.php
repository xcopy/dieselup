<?php

namespace DieselUp;

require_once 'vendor/autoload.php';

use Dotenv\Dotenv;

libxml_use_internal_errors(true);

class DieselUp
{
    // const BASE_URL = 'https://diesel.elcat.kg/index.php';

    /**
     * Constructor
     */
    public function __construct()
    {
        $dotenv = new Dotenv(__DIR__);
        $dotenv->load();
    }

    /**
     * @return void
     */
    public function invoke()
    {
        // sleep(10);
        $this->login();
        $this->post();
    }

    /**
     * @return void
     */
    private function login()
    {
        if (file_exists('response.html')) {
            $response = file_get_contents('response.html');
        } else {
            $response = $this->request($this->getUrl());

            file_put_contents('response.html', $response);
        }

        $document = new \DOMDocument;
        $document->loadHTML($response);

        if (!$document->getElementById('userlinks')) {
            $this->request($this->getUrl(['act' => 'Login', 'CODE' => '01']), 'POST',[
                'referer' => $this->getUrl(['from_login' => 1]),
                'UserName' => getenv('USERNAME'),
                'PassWord' => getenv('PASSWORD')
            ]);
        }
    }

    /**
     * @return void
     */
    private function post()
    {
        $topicId = getopt('t:')['t'];

        $response = $this->request($this->getUrl(['showtopic' => $topicId]));

        $document = new \DOMDocument;
        $document->loadHTML($response);

        $xpath = new \DomXpath($document);

        $deleteLinks = $xpath->query('//a[contains(@href, "javascript:delete_post")]');

        if ($deleteLinks->length > 1) {
            /** @var $lastLink \DOMElement */
            $lastLink = $deleteLinks->item($deleteLinks->length - 1);

            preg_match('/https?:[^\']+/', $lastLink->getAttribute('href'), $matches);

            if (!empty($matches)) {
                $this->request($matches[0]);
            }
        }

        $replyParams = ['Post' => 'UP'];

        $replyForms = $xpath->query('//form[contains(@name, "REPLIER")]');

        $hiddenInputs = $xpath->query('.//input[contains(@type, "hidden")]', $replyForms->item(0));

        foreach ($hiddenInputs as $input) {
            /** @var $input \DOMElement */
            $replyParams[$input->getAttribute('name')] = $input->getAttribute('value');
        }

        $this->request($this->getUrl(), 'POST', $replyParams);
    }

    /**
     * @param array $query
     * @return string
     */
    private function getUrl($query = [])
    {
        return 'https://diesel.elcat.kg/index.php?'.http_build_query($query);
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $params
     * @return string
     */
    private function request($url, $method = 'GET', $params = [])
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);

        if (strtoupper($method) === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }

        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:45.0) Gecko/20100101 Firefox/45.0');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.dat');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.dat');
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $result = curl_exec($ch);

        curl_close($ch);

        // file_put_contents('response.html', $result);

        return (string) $result;
    }
}

$dieselup = new DieselUp();
$dieselup->invoke();