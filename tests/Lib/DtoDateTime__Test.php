<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;





final class DtoDateTime__Test extends TestCase {


    function testMakeBad()
    {
        try {
            $badDbt = ';lakjsdf;';
            $obj = new \ElegantTechnologies\Cfd\Lib\CfvDateTime($badDbt);
            $this->assertTrue(0, "1Should not get this far ut: " . strtotime($badDbt));
        } catch (\ElegantTechnologies\Cfd\Core\CfdError $e) {
            $this->assertTrue(true, "1Good - that faiiled as expected");
        }

        try {
            $obj = new \ElegantTechnologies\Cfd\Lib\CfvDateTime(0);
            $this->assertTrue(0, "2Should not get this far");
        } catch (\TypeError $e) {
            $this->assertTrue(true, "2Good - that faiiled as expected");
        }


        try {
            $obj = new \ElegantTechnologies\Cfd\Lib\CfvDateTime(1970);
            $this->assertTrue(0, "3Should not get this far");
        } catch (\TypeError $e) {
            $this->assertTrue(true, "3Good - 1970 iis not a string, plus it is a vague date");
        }

        try {
            $obj = new \ElegantTechnologies\Cfd\Lib\CfvDateTime('tomorrow');
            $this->assertTrue(0, "4Should not get this far");
        } catch (\ElegantTechnologies\Cfd\Core\CfdErrorValidation $e) {
            $this->assertTrue(true, "4Good - that faiiled as expected");
        }

        try {
            $obj = new \ElegantTechnologies\Cfd\Lib\CfvDateTime('1970-11-04');
            $this->assertTrue(0, "4Should not get this far");
        } catch (\TypeError $e) {
            $this->assertTrue(true, "4Good - that faiiled as expected. Needs a time after it.");
        }
    }

    function test_makeGood() {

        $obj = new \ElegantTechnologies\Cfd\Lib\CfvDateTime('1970-11-04 13:11:25');
        $this->assertTrue(isset($obj), "");

        $obj = new \ElegantTechnologies\Cfd\Lib\CfvDateTime(\ElegantTechnologies\Cfd\Lib\CfvDateTime::now_asString());
        $this->assertTrue(isset($obj), "");

    }



}