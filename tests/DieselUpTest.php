<?php

class DieselUpTest extends \PHPUnit_Framework_TestCase
{
    public function testUrls()
    {
        $this->assertEquals('https://diesel.elcat.kg/index.php?', DieselUp::getUrl());
        $this->assertEquals('https://diesel.elcat.kg/index.php?foo=bar', DieselUp::getUrl(['foo' => 'bar']));
    }

    /*
    public function testRequest()
    {
        $response = Unirest\Request::get(DieselUp::getUrl());

        $this->assertEquals(200, $response->code);
    }
    */

    /**
     * @expectedException ErrorException
     */
    /*
    public function testRequestException()
    {
        $dieselUp = new DieselUp;

        $method = new ReflectionMethod($dieselUp, 'request');

        $method->setAccessible(true);

        $method->invoke($dieselUp, $dieselUp::getUrl(['showtopic' => 1234567890]));
    }
    */
}
