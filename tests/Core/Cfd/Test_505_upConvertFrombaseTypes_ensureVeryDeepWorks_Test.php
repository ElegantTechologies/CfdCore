<?php

declare(strict_types=1);

namespace testworld;

use PHPUnit\Framework\TestCase;




class ALuckyNumber2 extends \ElegantTechnologies\Cfd\Core\Cfd
{
    public function __construct(public int $Value) {
        if (!in_array($Value, [1,3,5,11,88])) {
            throw new \ElegantTechnologies\Cfd\Core\CfdError("$Value is very not-lucky.");
        }
        parent::__construct(...func_get_args());
    }
}

class ALuckyNumber2Convertable extends ALuckyNumber2
{

//
//    public static function upConvertType_elseNull(string $propertyName, $dangerousValue) : ?\SchoolTwist\Validations\Returns\DtoValid
//    {
//        $incomingType = gettype($dangerousValue);
//        if ($incomingType == 'integer') {
//            $childType = get_called_class();
//            $dtoValid = $childType::preValidateProperty('Value',$dangerousValue, null);
//            if (!$dtoValid->isValid) {
//                return $dtoValid;
//            } else {
//                $newCfd = new $childType(['Value' => $dangerousValue]);
//                $dtoValid =  new \SchoolTwist\Validations\Returns\DtoValid(['isValid'=>true,'enumReason'=>'itPassed', 'newValue'=>$newCfd]);
//                return $dtoValid;
//            }
//        } else {
//            return null;
//        }
//    }
}

class EvenLuckierNumber extends ALuckyNumber2Convertable
{
}

class Profile2 extends \ElegantTechnologies\Cfd\Core\Cfd
{
    public function __construct(
        public \testworld\ALuckyNumber2 $LuckyNum,
        public \testworld\EvenLuckierNumber $ConvertableLuck,
        public int $Age,
    ) {
        parent::__construct(...func_get_args());
    }
}


final class Test_505_upConvertFrombaseTypes_ensureVeryDeepWorks_Test extends TestCase
{




    function testHW()
    {

        $asrData = [
            'LuckyNum' => new ALuckyNumber2(5),
            'ConvertableLuck' => new EvenLuckierNumber(88),
            'Age'=>49,
        ];
        $obj =  Profile2::newViaAsr($asrData);
        $this->assertTrue($obj->ConvertableLuck->Value == 88, '');
        $this->assertTrue($obj->LuckyNum->Value == 5, '');
        $this->assertTrue($obj->Age == 49, '');
        $this->assertTrue(is_array($obj->toShallowArray()), 'ok');
    }





     function testBad()
    {
        $asrData = [
            'LuckyNum' => '3',
            'ConvertableLuck' => new ALuckyNumber2Convertable(88),
            'Age'=>49,
        ];

        try {
            $obj = Profile2::newViaAsr($asrData);
            $this->assertTrue(false, '');
        } catch (\Throwable $ed) {
               $this->assertTrue(true, '');
        }

        $asrData = [
            'LuckyNum' => new ALuckyNumber2(5),
            'Age' => 49,
             'ConvertableLuck' => '88', // fails because we don't have a mechanism to convert from string


        ];
        try {
            $obj = Profile2::newViaAsr($asrData);
            $this->assertTrue(false, '');
        } catch (\Throwable $ed) {
               $this->assertTrue(true, '');
        }


    }

           function testGood()
    {

         $asrData = [
            'LuckyNum' => new ALuckyNumber2(5),
            'Age' => 48,
             'ConvertableLuck' => 88,
        ];
        $obj = Profile2::newViaAsr($asrData);
//        print_r($obj);
//        exit;
        $this->assertTrue($obj->ConvertableLuck->Value == 88, '');

    }

    // Future: set up to test for multiple conversatons


}
