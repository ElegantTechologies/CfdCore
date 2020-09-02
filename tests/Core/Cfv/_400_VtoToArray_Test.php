<?php

declare(strict_types=1);

namespace testworld;

use PHPUnit\Framework\TestCase;


class FunnyNumber400 extends \ElegantTechnologies\Cfd\Core\Vto
{
    public function __construct(public int $value)
    {
        parent::__construct();
    }

}




final class _400_VtoToArray_Test extends TestCase
{
    function testHW()
    {
        $obj = new FunnyNumber400(5);
        $this->assertTrue($obj->toShallowArray() != [5], '');
        $this->assertTrue($obj->toShallowArray() == ['value'=>5], '');
        $this->assertTrue($obj->toShallowArray() != ['value'=>3], '');
        #$this->assertTrue(is_array($obj->toDeepArray()), 'ok');

    }
//
//
//
//     function testBad()
//    {
//        $asrData = [
//            'LuckyNum' => 3,
//            'Age' => 48
//        ];
//        $obj = new FunnyNumbers2($asrData);
//        $asr = $obj->toDeepArray();
//        $this->assertFalse(isset($asr['version']), 'ok');
//
//
//    }
//
//      function testGood()
//    {
//
//        $asrData = [
//            'LuckyNum' => 5,
//            'Age' => 48
//        ];
//        $obj = new FunnyNumbers2($asrData);
//        $asrOutput = $obj->toDeepArray();
//        $this->assertTrue($asrOutput['LuckyNum'] == 5, '');
//        $this->assertTrue($asrOutput['Age'] == 48, '');
//
//        $objReloaded = new FunnyNumbers2($asrOutput);
//        $asrOutputAgain = $objReloaded->toDeepArray();
//
//        $this->assertTrue($asrOutputAgain['LuckyNum'] == 5, '');
//        $this->assertTrue($asrOutputAgain['Age'] == 48, '');
//
//    }
//
}
