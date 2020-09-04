<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;


class CfdbDateTimeWrong  extends \ElegantTechnologies\Cfd\Lib\CfvDateTime {
}

class CfdbDateTime extends \ElegantTechnologies\Cfd\Lib\CfvDateTime
{
    public static function getSqlCreateColumn(): \EtFramework19\Cfdb\Core\CfdSqlCreateCol
    {
        return new \EtFramework19\Cfdb\Core\CfdSqlCreateCol([
            'OriginalPropertyName' => 'Value',
            'SqlType' => "datetime",
            #'Comment'=>,
            'IsUnique' => false,
            'canBeNull' => true,
            'hasDefault' => true,
            'Default' => NULL,
        ]);
    }
    public static function dbStringCastBack($stringStraigtFromDb)
    {
        return $stringStraigtFromDb;
    }
}

class DtoDbWip_Basket extends \ElegantTechnologies\Cfd\Core\Cfd {
    /* CREATE TABLE `wp_etac_events_Basket` (
      `Uuid` varchar(255) NOT NULL,
      `_OriginStory` text COMMENT 'The csvBookingIds probably regarding how this basket was derived.',
      `EnumPhase` varchar(255) DEFAULT NULL COMMENT 'Draft, OnOrbit, Trash, Museum',
      `BornOnDateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `_ReplacedByOtherUuid` varchar(255) DEFAULT NULL COMMENT 'Generally merged into new Uuid. Regardless, link by user to this Basket will redirect to this other Basket. ',
      PRIMARY KEY (`Uuid`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8
    */
    public function __construct(public string $Uuid, public \ElegantTechnologies\Cfd\Lib\CfvDateTime $DtoDateTime_BornOn) {
         parent::__construct(...func_get_args());
         }




    #public $DtoEnumValue_BasketPhase; // untracked 8/20 'untracked isn't a thing
}



final class DtoDateTime_SubTest extends TestCase {


    function testMakeBad()
    {
        try {
            $badDbt = ';lakjsdf;';
            $obj = new \ElegantTechnologies\Cfd\Lib\CfvDateTime($badDbt);
            $this->assertTrue(0, "1Should not get this far ut: " . strtotime($badDbt));
        } catch (\ElegantTechnologies\Cfd\Core\CfdError $e) {
            $this->assertTrue(true, "1Good - that faiiled as expected");
        }

        try {
            $obj = new \ElegantTechnologies\Cfd\Lib\CfvDateTime(0);
            $this->assertTrue(0, "2Should not get this far");
        } catch (\TypeError $e) {
            $this->assertTrue(true, "2Good - that faiiled as expected");
        }


        try {
            $obj = new \ElegantTechnologies\Cfd\Lib\CfvDateTime(1970);
            $this->assertTrue(0, "3Should not get this far");
        } catch (\TypeError $e) {
            $this->assertTrue(true, "3Good - 1970 iis not a string, plus it is a vague date");
        }

        try {
            $obj = new \ElegantTechnologies\Cfd\Lib\CfvDateTime('tomorrow');
            $this->assertTrue(0, "4Should not get this far");
        } catch (\ElegantTechnologies\Cfd\Core\CfdErrorValidation $e) {
            $this->assertTrue(true, "4Good - that faiiled as expected");
        }

        try {
            $obj = new \ElegantTechnologies\Cfd\Lib\CfvDateTime('1970-11-04');
            $this->assertTrue(0, "4Should not get this far");
        } catch (\TypeError $e) {
            $this->assertTrue(true, "4Good - that faiiled as expected. Needs a time after it.");
        }
    }

    function test_makeGood() {

        $obj = new \ElegantTechnologies\Cfd\Lib\CfvDateTime('1970-11-04 13:11:25');
        $this->assertTrue(isset($obj), "");

        $obj = new \ElegantTechnologies\Cfd\Lib\CfvDateTime(\ElegantTechnologies\Cfd\Lib\CfvDateTime::now_asString());
        $this->assertTrue(isset($obj), "");

    }

     function test_inheritenceCuzHadAnIssue() {

        $obj = new CfdbDateTime('1970-11-04 13:11:25');
        $this->assertTrue(isset($obj), "");

        $obj = new CfdbDateTime(\ElegantTechnologies\Cfd\Lib\CfvDateTime::now_asString());
        $this->assertTrue(isset($obj), "");
    }

     function test_inheritenceCuzHadAnIssue2() {

        // be bad

         try {
             $DtoBasket = new DtoDbWip_Basket(
                 Uuid:'hi im uuid',
                 DtoEnumValue_BasketPhase:'untracked, so what evs',
                 DtoDateTime_BornOn: new CfdbDateTimeWrong('1970-11-04 13:11:25'),
             );
             $this->assertTrue(false, "never this far");
         } catch (\Error $e) {
             $this->assertTrue(true, "");
         }



        //         // works cuz forces to base type
        //         $DtoBasket = new DtoDbWip_Basket([
        //             'Uuid' => 'hi im uuid',
        //             'DtoEnumValue_BasketPhase' => 'untracked, so what evs',
        //             'DtoDateTime_BornOn' => new \ElegantTechnologies\Cfd\Lib\CfvDateTime(['Value' => '1970-11-04 13:11:25']),
        //         ]);
        //
        //         $this->assertTrue(true, "never this far");


         // should work cuz decendant of base type
        $DtoBasket = new DtoDbWip_Basket(
             Uuid:'hi im uuid',
             # 8/20' this used to be ok, but now ALL public properties must be typed. 'DtoEnumValue_BasketPhase' => 'untracked, so what evs',
             DtoDateTime_BornOn: new CfdbDateTime('1970-11-04 13:11:25'),
         );
         $this->assertTrue(true, "never this far");


    }
}
