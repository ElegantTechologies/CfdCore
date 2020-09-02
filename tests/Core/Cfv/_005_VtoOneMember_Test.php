<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;



class Vto5 extends \ElegantTechnologies\Cfd\Core\Vto
{
    public function __construct(
        public string $value,
        public string $value2, #which is one too many
    ) {parent::__construct();
    } // ok
}

class Vto5_wrongValueName_evenThoughThereIsOnlyOne extends \ElegantTechnologies\Cfd\Core\Vto
{
    public function __construct(
        public string $val #misnamed: Must be value
    ) {parent::__construct();
    } // ok

}


class _005_VtoOneMember_Test extends TestCase
{

    function test_Num()
    {
    try {
        $c = new Vto5('jj');
        $this->assertTrue(true, "ok");
         $this->assertTrue(false, 'ArgumentCountError: Too few arguments to function Vto5::__construct(), 1 passed in');
    } catch (\ArgumentCountError $e) {

    }

    try {
        $c = new Vto5(value:'jj', value2:'rohrer');
        $this->assertTrue(false, "ElegantTechnologies\Cfd\Core\ErrorFromCfd: There are '2' public properties.  Vto can only have one and it must be named 'value'");
    } catch (\ElegantTechnologies\Cfd\Core\ErrorFromCfd $e) {
            $this->assertTrue(
                    true,
                    "This should have failed hard (and gotten here) .  " . $this::class. "  " . __LINE__
                );
    }


         try {
            $c = new Vto5_wrongValueName_evenThoughThereIsOnlyOne(val:'jj');
            $this->assertTrue(false, "ElegantTechnologies\Cfd\Core\ErrorFromCfd: This is a Vto, so 'Vto5_wrongValueName_evenThoughThereIsOnlyOne::val' must be renamed to 'Vto5_wrongValueName_evenThoughThereIsOnlyOne::value'.  Vto can only have one and it must be named 'value'");
        } catch (\ElegantTechnologies\Cfd\Core\ErrorFromCfd $e) {
            $this->assertTrue(
                true,
                "This should have failed hard (and gotten here) .  " . $this::class. "  " . __LINE__
            );
        }


    }

}
