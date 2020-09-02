<?php
declare(strict_types=1);
namespace testworld;
use PHPUnit\Framework\TestCase;



class CfesPhaseSimple extends \ElegantTechnologies\Cfd\Lib\Cfes {
    public function __construct(public array $value,)
    {
        parent::__construct(['Draft', 'RollOut', 'LaunchPad', 'OnOrbit', 'Descent', 'Museum', 'Trash']);
    }
}

class CfesWrongType extends \ElegantTechnologies\Cfd\Lib\Cfes {
    public function __construct(public string $value) { #ouch, value must be an array
        parent::__construct(['one','two']);
    }
}



final class Cfes_Test extends TestCase {

    function testBasics() {
        $obj = new CfesPhaseSimple(['Draft']);
        $this->assertTrue( $obj->value == ['Draft'], "Good");

        $this->assertTrue($obj->doesHaveThis('Draft'), "Good");
        $this->assertTrue($obj->doesHaveOnlyThis('Draft'), "Good");

    }

        function testBasics2() {
        $obj = new CfesPhaseSimple(['RollOut']);
        $this->assertTrue($obj->value == ['RollOut'], "Good");


        try {
            $obj = new \testworld\CfesPhaseSimple(['Explosion']);
            $this->assertTrue(0, "Exception: 'Explosion' is not a valid enumeration for testworld\CfePhaseSimple::valuePossibilities([Draft, RollOut, LaunchPad, OnOrbit, Descent, Museum, Trash])");
        } catch (\ElegantTechnologies\Cfd\Core\ErrorFromCfd $e) {
            $this->assertTrue(true, "Good - that failed as expected");
        }

    }

    function testArrayOnlyForVal() {
        try {
            $obj = new CfesWrongType('one');
            $this->assertTrue(0, "ElegantTechnologies\Cfd\Core\ErrorFromCfd: 'testworld\CfesWrongType::value' must be declared as  an array");
        } catch (\ElegantTechnologies\Cfd\Core\ErrorFromCfd $e) {
            $this->assertTrue(true, "Good - that failed as expected");
        }
    }

}
