<?php

declare(strict_types=1);

namespace testworld;

use PHPUnit\Framework\TestCase;




class ALuckyNumber extends \ElegantTechnologies\Cfd\Core\CfdoBase
{

    public int $Value;

    public static function Value_Validates($val) : \SchoolTwist\Validations\Returns\DtoValid {
        return new \SchoolTwist\Validations\Returns\DtoValid(['isValid'=>(in_array($val, [1,3,5,11,88]))]);
    }
}

class ALuckyNumberConvertable extends ALuckyNumber
{
    public static function upConvertType_elseNull(string $propertyName, $dangerousValue) : ?\SchoolTwist\Validations\Returns\DtoValid
    {
        $incomingType = gettype($dangerousValue);
        if ($incomingType == 'integer') {
            $childType = get_called_class();
            $dtoValid = $childType::preValidateProperty('Value',$dangerousValue, null);
            if (!$dtoValid->isValid) {
                return $dtoValid;
            } else {
                $newCfd = new $childType(['Value' => $dangerousValue]);
                $dtoValid =  new \SchoolTwist\Validations\Returns\DtoValid(['isValid'=>true,'enumReason'=>'itPassed', 'newValue'=>$newCfd]);
                return $dtoValid;
            }
        } else {
            return null;
        }
    }
}

class Profile extends \ElegantTechnologies\Cfd\Core\CfdoBase
{
    public ALuckyNumber $LuckyNum;

    public \testworld\ALuckyNumberConvertable $ConvertableLuck;

    public int $Age;
}


final class Test_500_upConvertFrombaseTypes_Test extends TestCase
{

        function testGood()
    {

         $asrData = [
            'LuckyNum' => new ALuckyNumber(['Value'=>5]),
            'Age' => 48,
             'ConvertableLuck' => 88,
        ];
        $obj = new Profile($asrData);
//        print_r($obj);
//        exit;
        $this->assertTrue($obj->ConvertableLuck->Value == 88, '');

    }


    function testHW()
    {

        $asrData = [
            'LuckyNum' => new ALuckyNumber(['Value'=>5]),
            'ConvertableLuck' => new ALuckyNumberConvertable(['Value'=>88]),
            'Age'=>49,
        ];
        $obj = new Profile($asrData);
        $this->assertTrue($obj->ConvertableLuck->Value == 88, '');
        $this->assertTrue($obj->LuckyNum->Value == 5, '');
        $this->assertTrue($obj->Age == 49, '');
        $this->assertTrue(is_array($obj->toDeepArray()), 'ok');
    }



     function testBad()
    {
        $asrData = [
            'LuckyNum' => '3',
            'ConvertableLuck' => new ALuckyNumberConvertable(['Value'=>88]),
            'Age'=>49,
        ];

        try {
            $obj = new Profile($asrData);
            $this->assertTrue(false, '');
        } catch (\Throwable $ed) {
               $this->assertTrue(true, '');
        }

        $asrData = [
            'LuckyNum' => new ALuckyNumber(['Value'=>5]),
            'Age' => 49,
             'ConvertableLuck' => '88', // fails because we don't have a mechanism to convert from string


        ];
        try {
            $obj = new Profile($asrData);
            $this->assertTrue(false, '');
        } catch (\Throwable $ed) {
               $this->assertTrue(true, '');
        }


    }

    // Future: set up to test for multiple conversatons


}
