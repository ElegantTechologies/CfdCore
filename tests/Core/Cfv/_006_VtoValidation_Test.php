<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class BeEven extends \ElegantTechnologies\Cfd\Core\Cfv {
    public function __construct(public int $value) {
        $isEven = ($value % 2) === 0;
        if (!$isEven) {
            throw new \ElegantTechnologies\Cfd\Core\CfdErrorValidation("$value is not an even number");
        }
        parent::__construct();
    }
}



class _006_VtoValidation_Test extends TestCase
{

    function test_Num()
    {
         $obj = new BeEven(0);
        $this->assertTrue($obj->value == 0, "Good");



        // Note: This will behave differently depending upon if using declare(strict_types=1), or not.
        try {
           $obj = new BeEven('2');
            $this->assertTrue(0, "TypeError: BeEven::__construct(): Argument #1 (\$value) must be of type int, string given");
        } catch (\TypeError $e) {
            $this->assertTrue(true, "Good - if using if using declare(strict_types=1)");
        }



        try {
            $obj = new BeEven(1);
            $this->assertTrue(0, "Should not get this far");
        } catch (\ElegantTechnologies\Cfd\Core\CfdErrorValidation $e) {
            $this->assertTrue(true, "Good - that faiiled as expected");
        }

        $obj = new BeEven(2);
        $this->assertTrue($obj->value == 2, "Good");

        $obj = new BeEven(-4);
        $this->assertTrue($obj->value == -4, "Good");

        try {
            $obj = new BeEven(-3);
            $this->assertTrue(0, "Should not get this far");
        } catch (\ElegantTechnologies\Cfd\Core\CfdErrorValidation $e) {
            $this->assertTrue(true, "Good - that faiiled as expected");
        }


        try {
            $obj = new BeEven(-1);
            $this->assertTrue(0, "Should not get this far");
        } catch (\ElegantTechnologies\Cfd\Core\CfdErrorValidation $e) {
            $this->assertTrue(true, "Good - that faiiled as expected");
        }

        try {
            $obj = new BeEven(3);
            $this->assertTrue(0, "Should not get this far");
        } catch (\ElegantTechnologies\Cfd\Core\CfdErrorValidation $e) {
            $this->assertTrue(true, "Good - that faiiled as expected");
        }


    }

}
