<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;






class Vto8_whereWeForgetToCallConstructorClass extends \ElegantTechnologies\Cfd\Core\Vto
{
    public function __construct(
        public string $value2,
    ) {} // not ok, no value is set (so we'll error out as soon as we do gets or sets
}


class _008_VtoEnsureConstructorIsCalled_Test extends TestCase
{

    function test_Num()
    {


        $c = new Vto8_whereWeForgetToCallConstructorClass('bob');
        #$this->assertTrue(false, 'I don't know how to ');

    }

}
