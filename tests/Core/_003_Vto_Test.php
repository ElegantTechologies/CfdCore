<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;


class Vto0 extends \ElegantTechnologies\Cfd\Core\Vto
{
    public function __construct()
    {
    }
}


class Vto1 extends \ElegantTechnologies\Cfd\Core\Vto
{
    public function __construct(
        public string $name,
    ) {parent::__construct();}
}

class Vto2 extends \ElegantTechnologies\Cfd\Core\Vto
{
    public function __construct(
        public string $value,
        public int $value2,
    ) {parent::__construct();}
}

class Vto3 extends \ElegantTechnologies\Cfd\Core\Vto
{
    public function __construct(
        public string $value,
    ) {parent::__construct();
    } // ok

}


class _003_Vto_Test extends TestCase
{

    function test_Num()
    {
        // Simple
        $c = new Vto0();


        // Missing var
        try {
            $c = new Vto1();
            $this->assertTrue(false, "Should not get here: Not passed any vars");
        } catch (ArgumentCountError $e) {
            $this->assertTrue(
                true,
                "This should have failed hard " . $this::class . "  " . __LINE__
            );
        }

        // Right num vars, but still wrong name

        try {
            $c = new Vto1('jj');
            $this->assertTrue(false, "is not ok, must be 'value");
        }  catch (\ElegantTechnologies\Cfd\Core\ErrorFromCfd $e) {
            $this->assertTrue(
                true,
                "This should have failed hard (and gotten here) .  " . $this::class. "  " . __LINE__
            );
        }



        // Named Vars - with default limbs.
        $c = new Vto3('jj');
        $this->assertTrue(true, "ok");


        $c = new Vto3(value:'jj');
        $this->assertTrue($c->value == 'jj', "ok cuz numNoses has a default even though we didn't specify numNoses");

      try {
            $c = new Vto3(name:'jj');
            $this->assertTrue(false, 'Error: Unknown named parameter $name');
        } catch (\Error $e) {
            $this->assertTrue(
                true,
                "This should have failed hard (and gotten here) .  " . $this::class. "  " . __LINE__
            );
        }


    }

}
