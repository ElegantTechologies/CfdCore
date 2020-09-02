<?php

declare(strict_types=1);

namespace testworld;

use PHPUnit\Framework\TestCase;



final class CfeQuick_Test extends TestCase
{

    function testBasics()
    {
        $cfe = new \ElegantTechnologies\Cfd\Lib\Cfez(['active', 'inactive'], value:'active',);
        $this->assertTrue($cfe->value == 'active', "Good");

    }

    function testBasics2()
    {

        try {
            $e = new \ElegantTechnologies\Cfd\Lib\Cfez(['active', 'inactive'], value:'done', );
            $this->assertTrue(false, "ElegantTechnologies\Cfd\Core\ErrorFromCfd: 'done' is not a valid enumeration for ElegantTechnologies\Cfd\Lib\CfeQuick::valuePossibilities([active, inactive])");
        } catch (\ElegantTechnologies\Cfd\Core\ErrorFromCfd $exception) {
           $this->assertTrue(true, "Good");
        }
    }

}
