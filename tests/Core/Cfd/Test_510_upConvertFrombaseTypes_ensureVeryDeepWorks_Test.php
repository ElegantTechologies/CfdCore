<?php

declare(strict_types=1);

namespace testworld510;

use PHPUnit\Framework\TestCase;




class ALuckyNumber2 extends \ElegantTechnologies\Cfd\Core\Cfd
{
    public int $Value;

    public static function Value_Validates($val) : \SchoolTwist\Validations\Returns\DtoValid {
        $isValid = (in_array($val, [1,3,5,11,88]));
        if ($isValid) {
            return new \SchoolTwist\Validations\Returns\DtoValid(['isValid' => true]);
        } else {
            return new \SchoolTwist\Validations\Returns\DtoValid(['isValid' => false, 'enumReason'=>'unlucky']);
        }
    }
}

class ALuckyNumber2Convertable extends ALuckyNumber2
{

}

class EvenLuckierNumber extends ALuckyNumber2Convertable
{
}

class Profile2 extends \ElegantTechnologies\Cfd\Core\Cfd
{
    public \testworld510\EvenLuckierNumber $ConvertableLuck;

}


final class Test_510_upConvertFrombaseTypes_ensureVeryDeepWorks_Test extends TestCase
{


    function testHW()
    {

        // base
        $dtoValid = EvenLuckierNumber::preValidateProperty('Value', 11, null);
        $this->assertTrue($dtoValid->isValid == true, '');


        $dtoValid = EvenLuckierNumber::preValidateProperty('Value', 12, null);
        $this->assertTrue($dtoValid->isValid == false, '');
        $this->assertTrue($dtoValid->enumReason == 'unlucky', " dtoValid->enumReason({$dtoValid->enumReason})");

        // composite
        $asrData['ConvertableLuck'] = new EvenLuckierNumber(['Value'=>11]);
        $dtoValid = Profile2::preValidateSubmission($asrData);
        $this->assertTrue($dtoValid->isValid == true, '');


        $asrData['ConvertableLuck'] = 11;
        $dtoValid = Profile2::preValidateSubmission($asrData);

        $this->assertTrue($dtoValid->isValid == true, '');


        $asrData['ConvertableLuck'] = 12;
        $dtoValid = Profile2::preValidateSubmission($asrData);
        $this->assertTrue($dtoValid->isValid == false, '');
        $this->assertTrue($dtoValid->enumReason == 'unlucky', " dtoValid->enumReason({$dtoValid->enumReason})");

    }



//     function testBad()
//    {
//         $asrData = [
//             'ConvertableLuck' => 89,
//        ];
//        $dtoValid = Profile2::preValidateSubmission($asrData);
//        $this->assertTrue(!$dtoValid->isValid, '');
//        $this->assertTrue(isset($dtoValid->enumReason), '');
//        $this->assertTrue($dtoValid->enumReason == 'unlucky', " dtoValid->enumReason({$dtoValid->enumReason})" );
//        print_r($dtoValid);
//        exit;
//
//        $asrData['ConvertableLuck'] = 88;
//        $dtoValid = Profile2::preValidateSubmission($asrData);
//        $this->assertTrue($dtoValid->isValid, '');
//
//        $asrData['ConvertableLuck'] = 1;
//        $dtoValid = Profile2::preValidateSubmission($asrData);
//        $this->assertTrue($dtoValid->isValid, 'someting');
//
//    }


}
