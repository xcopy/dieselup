<?php

class DieselUpTest extends \PHPUnit_Framework_TestCase
{
    public function testUrls()
    {
        $this->assertEquals('https://diesel.elcat.kg/index.php?', DieselUp::getUrl());
        $this->assertEquals('https://diesel.elcat.kg/index.php?foo=bar', DieselUp::getUrl(['foo' => 'bar']));
    }

    public function testSuccessfulRequest()
    {
        $response = Unirest\Request::get(DieselUp::getUrl());

        $this->assertEquals(200, $response->code);
    }

    public function testFailedRequest()
    {
        $response = Unirest\Request::get(DieselUp::getUrl(['showtopic' => 1]));

        $this->assertEquals(404, $response->code);
    }
}
