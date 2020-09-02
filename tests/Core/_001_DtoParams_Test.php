<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;


class Dto0 extends \ElegantTechnologies\Cfd\Core\Dto
{
    public function __construct()
    {
    }
}


class Dto1 extends \ElegantTechnologies\Cfd\Core\Dto
{
    public function __construct(
        public string $name,
    ) {}
}

class Dto2 extends \ElegantTechnologies\Cfd\Core\Dto
{
    public function __construct(
        public string $name,
        public int $age,
    ) {}
}

class Dto3 extends \ElegantTechnologies\Cfd\Core\Dto
{
    public function __construct(
        public string $name,
        public ?int $age,
        public int $numNoses = 1,
    ) {}
}


class _001_DtoParams_Test extends TestCase
{

    function test_Num()
    {
        // Simple
        $c = new Dto0();


        // Missing var
        try {
            $c = new Dto1();
            $this->assertTrue(false, "Should not get here: Not passed any vars");
        } catch (ArgumentCountError $e) {
            $this->assertTrue(
                true,
                "This should have failed hard " . $this::class . "  " . __LINE__
            );
        }

        // Right num vars
        $c = new Dto1('jj');
        $this->assertTrue($c->name == 'jj', "is ok");


        // Missing var
        try {
            $c = new Dto2('jj');
            $this->assertTrue(false, "Should not get here: missing age");
        } catch (ArgumentCountError $e) {
            $this->assertTrue(
                true,
                "This should have failed hard (and gotten here) .  " . $this::class. "  " . __LINE__
            );
        }

        // Right num vars
        $c = new Dto2('jj', 49);
        $this->assertTrue($c->name == 'jj', "is ok");


        // Wrong Type
        try {
            $c = new Dto2(49, 'jj');
            $this->assertTrue(false, "Should not get here: missing age");
        } catch (TypeError $e) {
            $this->assertTrue(
                true,
                "This should have failed hard (and gotten here) .  " . $this::class. "  " . __LINE__
            );
        }

        // Named Vars - the way god intended.
        $c = new Dto2(age:49, name:'jj');
        $this->assertTrue(true, "ok");

        // Named Vars - with default limbs.
        $c = new Dto3(age:49, name:'jj', numNoses:1);
        $this->assertTrue(true, "ok");


        $c = new Dto3(age:49, name:'jj');
        $this->assertTrue(true, "ok cuz numNoses has a default even though we didn't specify numNoses");

      try {
            $c = new Dto3(name:'jj');
            $this->assertTrue(false, "Should not get here cuz we didn't specify age.  One might think it defaults to null.  It doesn't");
        } catch (TypeError $e) {
            $this->assertTrue(
                true,
                "This should have failed hard (and gotten here) .  " . $this::class. "  " . __LINE__
            );
        }

              $c = new Dto3(name:'jj', age:null);
        $this->assertTrue($c->name == 'jj', "ok cuz numNoses has a default even though we didn't specify numNoses");
        $this->assertTrue(is_null($c->age), "ok cuz numNoses has a default even though we didn't specify numNoses");


    }

}
