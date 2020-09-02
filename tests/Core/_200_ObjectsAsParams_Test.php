<?php

declare(strict_types=1);

namespace testworld;

use PHPUnit\Framework\TestCase;


class Greeting
{
    public function talk(string $salution, string $name)
    {
        return "$salution $name";
    }
}

class SubGreeting extends Greeting
{

}

class DtoDummyDum extends \ElegantTechnologies\Cfd\Core\Vto
{
    public function __construct(public \testworld\Greeting $value)  {parent::__construct();}
}

class SmallPrimes extends \ElegantTechnologies\Cfd\Core\Vto
{
    public function __construct(public int $value) {

        if (!in_array($value, [1, 3, 5, 7])) {
            throw new \ElegantTechnologies\Cfd\Core\ErrorFromCfd("$value is not prime");
                }

        parent::__construct();
    }
}

class FunnyNumbers extends SmallPrimes
{
  public function __construct(public int $value) { parent::__construct($value);}

}


final class _200_ObjectsAsParams_Test extends TestCase
{
    function testMissingProperty_standardObjs()
    {
        $obj = new DtoDummyDum(new Greeting());
        $this->assertTrue($obj->value->talk("Hello", "JJ") == 'Hello JJ', '');
    }

     function testInheritedValidation()
    {

        try {
            $obj = new FunnyNumbers(2);
            $this->assertTrue(false, 'ElegantTechnologies\Cfd\Core\ErrorFromCfd: 2 is not prime');
        } catch (\ElegantTechnologies\Cfd\Core\ErrorFromCfd $e) {
               $this->assertTrue(true, 'ok');
        }

    }

}