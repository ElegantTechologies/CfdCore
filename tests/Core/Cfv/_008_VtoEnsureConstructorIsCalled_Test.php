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
        #$this->assertTrue($c->value, 'I dont know how to '); this won't catch the error - mainly a php limitation
        try {
            $this->assertTrue($c->getPropertyNames(), "ElegantTechnologies\Cfd\Core\ErrorFromCfd: Vto8_whereWeForgetToCallConstructorClass did not get it's construction called.");
        } catch (\ElegantTechnologies\Cfd\Core\ErrorFromCfd $e) {
            $this->assertTrue(true, 'good');
        }

    }

}
