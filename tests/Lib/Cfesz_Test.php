<?php

declare(strict_types=1);

namespace testworld;

use PHPUnit\Framework\TestCase;



final class Cfesz_Test extends TestCase
{

    function testBasics()
    {
        $cfe = new \ElegantTechnologies\Cfd\Lib\Cfesz(['one', 'two','three'], value:['two'],);
        $this->assertTrue($cfe->value == ['two'], "Good");

    }

    function testBasics2()
    {

        try {
            $cfe = new \ElegantTechnologies\Cfd\Lib\Cfesz(['one', 'two','three'], value:['zero'],);
            $this->assertTrue(false, "ElegantTechnologies\Cfd\Core\ErrorFromCfd: 'zero' is not a valid enumeration for ElegantTechnologies\Cfd\Lib\Cfesz::valuePossibilities([one, two, three])");
        } catch (\ElegantTechnologies\Cfd\Core\ErrorFromCfd $exception) {
           $this->assertTrue(true, "Good");
        }


        $cfe = new \ElegantTechnologies\Cfd\Lib\Cfesz(['one', 'two','three'], value:['one','three'],);
        $this->assertTrue($cfe->value == ['one','three'], "Good");
        $this->assertTrue($cfe->value != ['three','one',], "bad"); // order matters
        $this->assertTrue($cfe->doesHaveThis('three'), "Good");
        $this->assertTrue($cfe->doesHaveThese(['three']), "Good");
        $this->assertTrue($cfe->doesHaveThese(['three','one']), "Good");
        $this->assertTrue($cfe->doesHaveThese(['three','one','eight']) == false, "Good");
        $this->assertTrue($cfe->doesHaveThese(['three','one','two']) == false, "Good");
        $this->assertTrue($cfe->doesHaveOnlyThis('three') == false, "Good - is holding 'three','one'");

    }

}
