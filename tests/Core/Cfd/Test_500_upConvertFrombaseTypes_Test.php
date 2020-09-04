<?php

declare(strict_types=1);

namespace testworld;

use PHPUnit\Framework\TestCase;




class ALuckyNumber extends \ElegantTechnologies\Cfd\Core\Cfd
{
    public function __construct(public int $Value) {
        if (!in_array($Value, [1,3,5,11,88])) {
            throw new \ElegantTechnologies\Cfd\Core\CfdErrorValidation ("$Value is very not-lucky.");
        }
        parent::__construct(...func_get_args());
    }
}

class ALuckyNumberConvertable extends ALuckyNumber
{
    // Now automatic....
    //    public static function upConvertType_elseNull(string $propertyName, $dangerousValue) : ?\ElegantTechnologies\Validations\Returns\DtoValid
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
class ClassHolder {}

class Profile extends \ElegantTechnologies\Cfd\Core\Cfd
{
    public function __construct(
        public ALuckyNumber $LuckyNum,
        public \testworld\ALuckyNumberConvertable $ConvertableLuck,
        public int $Age,
        public ClassHolder $theClass,
    ) {
        parent::__construct(...func_get_args());}
}


final class Test_500_upConvertFrombaseTypes_Test extends TestCase
{
    function testTypeDetection () {
         $this->assertTrue( Profile::isArgumentType_sameAsPropertyType_onlyUseForTesting('Age', 1));
         $this->assertFalse( Profile::isArgumentType_sameAsPropertyType_onlyUseForTesting('Age', '1'));

         $this->assertFalse( Profile::isArgumentType_sameAsPropertyType_onlyUseForTesting('theClass', 1));
         $c = new ClassHolder();
         $this->assertTrue( Profile::isArgumentType_sameAsPropertyType_onlyUseForTesting('theClass', $c));

         $c =  new ALuckyNumber(5);
         $this->assertTrue( Profile::isArgumentType_sameAsPropertyType_onlyUseForTesting('LuckyNum', $c));

    }
        function testGood()
    {

         $asrData = [
            'LuckyNum' => new ALuckyNumber(5),
            'Age' => 48,
             'ConvertableLuck' => new ALuckyNumberConvertable(88),
             'theClass' => new ClassHolder()
        ];
        $obj =  Profile::newViaAsr($asrData);
        $this->assertTrue($obj->LuckyNum->Value == 5, '');
        $this->assertTrue($obj->Age == 48, '');
        $this->assertTrue($obj->ConvertableLuck->Value == 88, '');

    }

        function testGoodSimpleConverstion()
    {

         $asrData = [
            'LuckyNum' => 5,
            'Age' => 48,
             'ConvertableLuck' => new ALuckyNumberConvertable(88),
             'theClass' => new ClassHolder()
        ];
        $obj =  Profile::newViaAsr($asrData);
        $this->assertTrue($obj->LuckyNum->Value == 5, '');
        $this->assertTrue($obj->Age == 48, '');
        $this->assertTrue($obj->ConvertableLuck->Value == 88, '');

    }
//
//
    function testHW()
    {

        $asrData = [
            'LuckyNum' => new ALuckyNumber(5),
            'ConvertableLuck' => new ALuckyNumberConvertable(88),
            'Age'=>49,
            'theClass' => new ClassHolder()
        ];
        $obj = Profile::newViaAsr($asrData);
        $this->assertTrue($obj->ConvertableLuck->Value == 88, '');
        $this->assertTrue($obj->LuckyNum->Value == 5, '');
        $this->assertTrue($obj->Age == 49, '');
        $this->assertTrue(is_array($obj->toShallowArray()), 'ok');
    }
//
//
//
//     function testBad()
//    {
//        $asrData = [
//            'LuckyNum' => '3', // error: we don't upconvert from string (while strict)
//            'ConvertableLuck' =>  new ALuckyNumberConvertable(88),
//            'Age'=>49,
//        ];
//
//        try {
//            $obj =  Profile::newViaAsr($asrData);
//            $this->assertTrue(false, '');
//        } catch (\TypeError $ed) {
//               $this->assertTrue(true, '');
//        }
//
//        $asrData = [
//            'LuckyNum' => new ALuckyNumber(5),
//            'Age' => 49,
//             'ConvertableLuck' => '88', // fails because we don't have a mechanism to convert from string
//
//
//        ];
//        try {
//            $obj =Profile::newViaAsr($asrData);
//            $this->assertTrue(false, '');
//        } catch (\TypeError $ed) {
//               $this->assertTrue(true, '');
//        }
//
//
//        $asrData = [
//            'LuckyNum' => 5,
//            'Age' => 49,
//             'ConvertableLuck' => 89, // fails because 89 is not lucky
//        ];
//        try {
//            $obj =Profile::newViaAsr($asrData);
//            $this->assertTrue(false, '');
//        } catch (\ElegantTechnologies\Cfd\Core\CfdErrorValidation $ed) {
//               $this->assertTrue(true, '');
//        }
//    }

    // Future: set up to test for multiple conversatons


}
