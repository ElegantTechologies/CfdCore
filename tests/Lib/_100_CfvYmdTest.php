<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;



#require_once(__DIR__ . '/../../../vendor/autoload.php');


final class _100_CfvYmdTest extends TestCase {


    function testMake() {
        try {
            $badDbt = ';lakjsdf;';
            $obj = new \ElegantTechnologies\Cfd\Lib\CfvYmd($badDbt);

            $this->assertTrue(false, "1Should not get this far ut: ".strtotime($badDbt));
        } catch (\ElegantTechnologies\Cfd\Core\CfdError $e) {
            $this->assertTrue(true, "1Good - that faiiled as expected");
        }

        try {
            $obj = new \ElegantTechnologies\Cfd\Lib\CfvYmd(0);
            $this->assertTrue(0, 'TypeError: ElegantTechnologies\Cfd\Lib\CfvYmd::__construct(): Argument #1 ($value) must be of type string, int given');
        } catch (\TypeError $e) {
            $this->assertTrue(true, "2Good - that faiiled as expected");
        }

         try {
            $obj = new \ElegantTechnologies\Cfd\Lib\CfvYmd('0');
            $this->assertTrue(0, 'ElegantTechnologies\Cfd\Core\CfdError: 1970-01-01 !=0');
        } catch (\ElegantTechnologies\Cfd\Core\CfdError $e) {
            $this->assertTrue(true, "2Good - that faiiled as expected");
        }

        try {
            $obj = new \ElegantTechnologies\Cfd\Lib\CfvYmd(1970);
            $this->assertTrue(0, 'TypeError: ElegantTechnologies\Cfd\Lib\CfvYmd::__construct(): Argument #1 ($value) must be of type string, int given');
        } catch (\TypeError $e) {
            $this->assertTrue(true, "3Good - 1970 is not a string, plus it is a vague date");
        }

        try {
            $obj = new \ElegantTechnologies\Cfd\Lib\CfvYmd('1970');
            $this->assertTrue(0, 'ElegantTechnologies\Cfd\Core\CfdError: 1970-09-01 !=1970');
        } catch (\ElegantTechnologies\Cfd\Core\CfdError $e) {
            $this->assertTrue(true, "3Good - 1970 is not a string, plus it is a vague date");
        }

        try {
            $obj = new \ElegantTechnologies\Cfd\Lib\CfvYmd('tomorrow');
            $this->assertTrue(0, "4Should not get this far");
        } catch (\ElegantTechnologies\Cfd\Core\CfdError $e) {
            $this->assertTrue(true, "4Good - that failed as expected");
        }


        $obj = new \ElegantTechnologies\Cfd\Lib\CfvYmd('1970-11-04');
        $this->assertTrue(isset($obj), "");
        $this->assertTrue($obj->value == '1970-11-04', "");


    }


}