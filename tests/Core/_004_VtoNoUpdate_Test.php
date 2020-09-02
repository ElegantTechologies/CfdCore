<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;



class Vto4 extends \ElegantTechnologies\Cfd\Core\Vto
{
    public function __construct(
        public string $value,
    ) {parent::__construct();
    } // ok

}


class _004_VtoNoUpdate_Test extends TestCase
{

    function test_Num()
    {


    // Named Vars - with default limbs.
    $c = new Vto4('jj');
    $this->assertTrue(true, "ok");


    $c = new Vto4(value:'jj');
    $this->assertTrue($c->value == 'jj', "ok cuz numNoses has a default even though we didn't specify numNoses");

         try {
            $c = new Vto4(value:'jj');
            $c->value = 'bob';
            $this->assertTrue(false, 'ElegantTechnologies\Cfd\Core\ErrorFromCfd: Vto4 is a Vto, so you can not update Vto4::value');
        } catch (\ElegantTechnologies\Cfd\Core\ErrorFromCfd $e) {
            $this->assertTrue(
                true,
                "This should have failed hard (and gotten here) .  " . $this::class. "  " . __LINE__
            );
        }


    }

}
