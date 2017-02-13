<?php

use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Unirest\Method;
use Unirest\Response;
use Unirest\Request;
use Unirest\Request\Body;

class DieselUp
{
    const BASE_URL = 'https://diesel.elcat.kg/index.php';

    /**
     * @var ConsoleOutputInterface
     */
    private $output;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->output = new ConsoleOutput;

        Request::verifyPeer(false);
        Request::verifyHost(false);
        Request::cookieFile('/tmp/cookie.dat');
    }

    /**
     * @param array $query
     * @return string
     */
    public static function getUrl(array $query = [])
    {
        return static::BASE_URL.'?'.http_build_query($query);
    }

    /**
     * @return void
     */
    public function invoke()
    {
        try {
            $this->login();
            $this->post();
        } catch (\Exception $e) {
            $app = new Symfony\Component\Console\Application;
            $app->renderException($e, $this->output);
        }
    }

    /**
     * @return void
     */
    protected function login()
    {
        $this->output->writeln('<comment>Checking access</comment>');

        // request homepage
        $response = $this->request(static::getUrl());

        $document = new \DOMDocument;
        $document->loadHTML($response);

        // if not logged in
        if (!$document->getElementById('user_link')) {
            $this->output->writeln('<comment>Logging in</comment>');

            $urlParams = [
                'app' => 'core',
                'module' => 'global',
                'section' => 'login'
            ];

            $loginParams = [
                'ips_username' => getenv('USERNAME'),
                'ips_password' => getenv('PASSWORD'),
                'rememberMe' => 1
            ];

            // request login page
            $response = $this->request(static::getUrl($urlParams));

            $document->loadHTML($response);

            $xpath = new \DomXpath($document);

            $loginForm = $xpath->query('//form[@id="login"]')->item(0);

            // find hidden inputs
            $hiddenInputs = $xpath->query('.//input[contains(@type, "hidden")]', $loginForm);

            foreach ($hiddenInputs as $input) {
                /** @var $input \DOMElement */
                $loginParams[$input->getAttribute('name')] = $input->getAttribute('value');
            }

            $urlParams['do'] = 'process';

            // request login
            $this->request(static::getUrl($urlParams), Method::POST, Body::Form($loginParams));
        }

        $this->output->writeln('<info>Logged in</info>');
    }

    /**
     * @throws ErrorException
     * @throws LengthException
     */
    protected function post()
    {
        // get arguments
        $argv = $_SERVER['argv'];

        // remove first argument (i.e. path)
        array_shift($argv);

        if (!count($argv)) {
            // topic ID is required argument
            throw new \LengthException('Not enough arguments');
        }

        // request topic page
        $response = $this->request(static::getUrl(['showtopic' => $argv[0]]));

        $document = new \DOMDocument;
        $document->loadHTML($response);

        $xpath = new \DomXpath($document);

        // find latest post(s)
        $deleteLinks = $xpath->query('//a[contains(@class, "delete_post")]');

        // any posts?
        // todo: delete ONLY "UP" post
        if ($deleteLinks->length) {
            /** @var $lastLink \DOMElement */
            $lastLink = $deleteLinks->item($deleteLinks->length - 1);

            preg_match('/https?:[^\']+/', $lastLink->getAttribute('href'), $matches);

            // delete last UP post
            if (!empty($matches)) {
                $this->output->writeln('<comment>Deleting last UP</comment>');
                $this->request($matches[0]);
            }
        }

        $replyParams = ['Post' => 'UP'];

        $replyForm = $xpath->query('//form[@id="ips_fastReplyForm"]')->item(0);

        $hiddenInputs = $xpath->query('.//input[contains(@type, "hidden")]', $replyForm);

        foreach ($hiddenInputs as $input) {
            /** @var $input \DOMElement */
            $replyParams[$input->getAttribute('name')] = $input->getAttribute('value');
        }

        $this->output->writeln('<info>Posting new UP</info>');

        // and reply new UP post
        $this->request(static::getUrl(), Method::POST, Body::Form($replyParams));
    }

    /**
     * @param string $url
     * @param string $method
     * @param string $body
     * @return string
     * @throws Exception
     */
    protected function request($url, $method = Method::GET, $body = null)
    {
        $this->output->writeln(sprintf('<fg=white>%s %s</>', $method, $url));

        /** @var $response Response */
        $response = (strtoupper($method) === Method::POST)
            ? Request::post($url, [], $body)
            : Request::get($url);

        $result = (string) $response->body;

        /*
        $document = new \DOMDocument;
        $document->loadHTML($result);

        $xpath = new \DomXpath($document);

        $errors = $xpath->query('//h1[contains(@class, "ipsType_pagetitle")]');

        if ($errors->length) {
            throw new \ErrorException($errors->item(0)->textContent);
        }
        */

        if ($response->code !== HttpResponse::HTTP_OK) {
            throw new \Exception(HttpResponse::$statusTexts[$response->code], $response->code);
        }

        return $result;
    }
}
