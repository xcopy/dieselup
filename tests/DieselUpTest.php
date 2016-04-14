<?php

class DieselUpTest extends \PHPUnit_Framework_TestCase
{
    public function testDotEnvFileExists()
    {
        $this->assertFileExists('.env');
    }

    public function testRequest()
    {
        /** @var $response Unirest\Response */
        $response = Unirest\Request::get(DieselUp::getUrl());

        $this->assertEquals(200, $response->code);
    }

    /**
     * @expectedException ErrorException
     */
    public function testRequestException()
    {
        $dieselUp = new DieselUp;

        $method = new ReflectionMethod($dieselUp, 'request');

        $method->setAccessible(true);

        $method->invoke($dieselUp, $dieselUp::getUrl(['showtopic' => 1234567890]));
    }
}
