<?php

class DieselUpTest extends \PHPUnit_Framework_TestCase
{
    public function testDotEnvFileExists()
    {
        $this->assertFileExists('.env');
    }

    public function testRequestResultString()
    {
        $method = new ReflectionMethod('DieselUp', 'request');

        $method->setAccessible(true);

        $this->assertInternalType('string', $method->invoke(new DieselUp, 'https://diesel.elcat.kg/index.php'));
    }
}
