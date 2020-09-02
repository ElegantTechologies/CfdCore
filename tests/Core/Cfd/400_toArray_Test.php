<?php

declare(strict_types=1);

namespace testworld;

use PHPUnit\Framework\TestCase;


class FunnyNumbers32 extends \ElegantTechnologies\Cfd\Core\CfdoBase
{

    static $version = 4;

    /** @var integer */
    public $LuckyNum;

    /** @var integer */
    public $Age;
}




final class Test_toArray extends TestCase
{
    function testHW()
    {

        $asrData = [
            'LuckyNum' => 5,
            'Age' => 48
        ];
        $obj = new FunnyNumbers32($asrData);
        $this->assertTrue($obj->LuckyNum == 5, '');
        $this->assertTrue(is_array($obj->toDeepArray()), 'ok');

    }



     function testBad()
    {
        $asrData = [
            'LuckyNum' => 3,
            'Age' => 48
        ];
        $obj = new FunnyNumbers32($asrData);
        $asr = $obj->toDeepArray();
        $this->assertFalse(isset($asr['version']), 'ok');


    }

      function testGood()
    {

        $asrData = [
            'LuckyNum' => 5,
            'Age' => 48
        ];
        $obj = new FunnyNumbers2($asrData);
        $asrOutput = $obj->toDeepArray();
        $this->assertTrue($asrOutput['LuckyNum'] == 5, '');
        $this->assertTrue($asrOutput['Age'] == 48, '');

        $objReloaded = new FunnyNumbers2($asrOutput);
        $asrOutputAgain = $objReloaded->toDeepArray();

        $this->assertTrue($asrOutputAgain['LuckyNum'] == 5, '');
        $this->assertTrue($asrOutputAgain['Age'] == 48, '');

    }
//
}
