<?php

declare(strict_types=1);

namespace testworld;

use PHPUnit\Framework\TestCase;


class FunnyNumbers32 extends \ElegantTechnologies\Cfd\Core\Cfd
{

    static private $version = 4;


    public function __construct(public int $LuckyNum, public int $Age)
    {

        parent::__construct(...func_get_args());
    }
}




final class _200_InitFromArray extends TestCase
{
    function testHW()
    {

        $obj = new FunnyNumbers32(LuckyNum:5, Age:48);
        #
        $this->assertTrue($obj->LuckyNum == 5, '');
        $asrData = [
            'LuckyNum' => 57,
            'Age' => 48
        ];
        $obj = FunnyNumbers32::newViaAsr($asrData);
        $this->assertTrue($obj->LuckyNum == 57, '');
        #$this->assertTrue(is_array($obj->toDeepArray()), 'ok');

    }

      function testGood()
    {

        $asrData = [
            'LuckyNum' => 5,
            'Age' => 48
        ];
        $obj =  FunnyNumbers32::newViaAsr($asrData);
        $asrOutput = $obj->toShallowArray();
        $this->assertTrue($asrOutput['LuckyNum'] == 5, '');
        $this->assertTrue($asrOutput['Age'] == 48, '');

        $objReloaded = FunnyNumbers32::newViaAsr($asrData);
        $asrOutputAgain = $objReloaded->toShallowArray();

        $this->assertTrue($asrOutputAgain['LuckyNum'] == 5, '');
        $this->assertTrue($asrOutputAgain['Age'] == 48, '');

    }
//
}
