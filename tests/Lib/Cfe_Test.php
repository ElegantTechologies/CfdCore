<?php
declare(strict_types=1);
namespace testworld;
use PHPUnit\Framework\TestCase;



class CfePhaseSimple extends \ElegantTechnologies\Cfd\Lib\Cfe {
    public function __construct(public string $value,)
    {
        parent::__construct(['Draft', 'RollOut', 'LaunchPad', 'OnOrbit', 'Descent', 'Museum', 'Trash']);
    }
}



final class Cfe_Test extends TestCase {

    function testBasics() {
        $obj = new CfePhaseSimple('Draft');
        $this->assertTrue($obj->value == 'Draft', "Good");

    }

        function testBasics2() {
        $obj = new CfePhaseSimple('Draft');
        $this->assertTrue($obj->value == 'Draft', "Good");


        try {
            $obj = new \testworld\CfePhaseSimple('Explosion');
            $this->assertTrue(0, "Exception: 'Explosion' is not a valid enumeration for testworld\CfePhaseSimple::valuePossibilities([Draft, RollOut, LaunchPad, OnOrbit, Descent, Museum, Trash])");
        } catch (\ElegantTechnologies\Cfd\Core\ErrorFromCfd $e) {
            $this->assertTrue(true, "Good - that failed as expected");
        }

    }

}
