<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;



class SmateTime
{

}

final class TestDtoDateTim2e extends TestCase {


    function testMake() {
        try {
            $badDbt = ';lakjsdf;';
            $obj = new \ElegantTechnologies\Cfd\Lib\CfvDateTime(['Value'=>$badDbt]);
            $this->assertTrue(0, "1Should not get this far ut: ".strtotime($badDbt));
        } catch (\ElegantTechnologies\Cfd\Core\CfdError $e) {
            $this->assertTrue(true, "1Good - that faiiled as expected");
        }

        try {
            $obj = new \ElegantTechnologies\Cfd\Lib\CfvDateTime(['Value'=>0]);
            $this->assertTrue(0, "2Should not get this far");
        } catch (\ElegantTechnologies\Cfd\Core\CfdError $e) {
            $this->assertTrue(true, "2Good - that faiiled as expected");
        }


        try {
            $obj = new \ElegantTechnologies\Cfd\Lib\CfvDateTime(['Value'=>1970]);
            $this->assertTrue(0, "3Should not get this far");
        } catch (\ElegantTechnologies\Cfd\Core\CfdError $e) {
            $this->assertTrue(true, "3Good - 1970 iis not a string, plus it is a vague date");
        }

        try {
            $obj = new \ElegantTechnologies\Cfd\Lib\CfvDateTime(['Value'=>'tomorrow']);
            $this->assertTrue(0, "4Should not get this far");
        } catch (\ElegantTechnologies\Cfd\Core\CfdError $e) {
            $this->assertTrue(true, "4Good - that faiiled as expected");
        }

        try {
            $obj = new \ElegantTechnologies\Cfd\Lib\CfvDateTime(['Value'=>'1970-11-04']);
            $this->assertTrue(0, "4Should not get this far");
        } catch (\ElegantTechnologies\Cfd\Core\CfdError $e) {
            $this->assertTrue(true, "4Good - that faiiled as expected. Needs a time after it.");
        }

        $obj = new \ElegantTechnologies\Cfd\Lib\CfvDateTime(['Value'=>'1970-11-04 13:11:25']);
        $this->assertTrue(isset($obj), "");

        $obj = new \ElegantTechnologies\Cfd\Lib\CfvDateTime(['Value'=>\ElegantTechnologies\Cfd\Lib\CfvDateTime::now_asString()]);
        $this->assertTrue(isset($obj), "");

    }


     function doSomethingWithDecscentant(\ElegantTechnologies\Cfd\Core\Cfd $descentOfCfdBase)
    {
    }


     function testBadDescendants()
    {
         try {
             $obj = new SmateTime();$this->doSomethingWithDecscentant($obj);
             $this->assertTrue(true, "");
         } catch (Throwable $e) {
             $this->assertTrue(true, "");
         }
    }
    function testDescendants()
    {
        $obj = new \ElegantTechnologies\Cfd\Lib\CfvDateTime(['Value'=>'1970-11-04 13:11:25']);
        $this->doSomethingWithDecscentant($obj);
        $this->assertTrue(true, "");
    }


}