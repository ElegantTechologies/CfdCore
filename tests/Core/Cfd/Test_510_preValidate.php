<?php

declare(strict_types=1);

namespace testworld510;

use PHPUnit\Framework\TestCase;




class ALuckyNumber2 extends \ElegantTechnologies\Cfd\Core\Cfd
{
     public function __construct(public int $Value) {
        if (!in_array($Value, [1,3,5,11,88])) {
            throw new \ElegantTechnologies\Cfd\Core\CfdErrorValidation("$Value is very not-lucky.");
        }
        parent::__construct(...func_get_args());
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
    public function __construct(public \testworld510\EvenLuckierNumber $ConvertableLuck) {
        parent::__construct(...func_get_args());
    }

}


final class Test_510_preValidate extends TestCase
{

#JJ - you left off expecting to re-implement preValidatePropertyValue & preValidateSubmission/preValidateProperties from CfdBase into Cfd
    function testHW()
    {

        // base
        $dtoValid = EvenLuckierNumber::preValidatePropertyValue('Value', 11, null);
        $this->assertTrue($dtoValid->isValid == true, $dtoValid->message);


        $dtoValid = EvenLuckierNumber::preValidatePropertyValue('Value', 12, null);
        #print_r($dtoValid);
        $this->assertTrue($dtoValid->isValid == false, '');
        $this->assertTrue($dtoValid->enumReason == 'CfdErrorValidation', " dtoValid->enumReason({$dtoValid->enumReason})");
//
//        // composite
        $asrData['ConvertableLuck'] = new EvenLuckierNumber(11);
        $dtoValid = Profile2::preValidatePropertyValues($asrData);
        $this->assertTrue($dtoValid->isValid == true, '');
//
//
        $asrData['ConvertableLuck'] = 11;
        $dtoValid = Profile2::preValidatePropertyValues($asrData);
//
        $this->assertTrue($dtoValid->isValid == true, '');
//
//
        $asrData['ConvertableLuck'] = 12;
        $dtoValid = Profile2::preValidatePropertyValues($asrData);
        $this->assertTrue($dtoValid->isValid == false, '');
        $this->assertTrue($dtoValid->enumReason == 'CfdErrorValidation', " dtoValid->enumReason({$dtoValid->enumReason})");

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
