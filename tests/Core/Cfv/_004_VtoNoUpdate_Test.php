<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;



class Cfv4 extends \ElegantTechnologies\Cfd\Core\Cfv
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
    $c = new Cfv4('jj');
    $this->assertTrue(true, "ok");


    $c = new Cfv4(value:'jj');
    $this->assertTrue($c->value == 'jj', "ok cuz numNoses has a default even though we didn't specify numNoses");

         try {
            $c = new Cfv4(value:'jj');
            $c->value = 'bob';
            $this->assertTrue(false, 'ElegantTechnologies\Cfd\Core\CfdError: Vto4 is a Vto, so you can not update Vto4::value');
        } catch (\ElegantTechnologies\Cfd\Core\CfdError $e) {
            $this->assertTrue(
                true,
                "This should have failed hard (and gotten here) .  " . $this::class. "  " . __LINE__
            );
        }


    }

}
