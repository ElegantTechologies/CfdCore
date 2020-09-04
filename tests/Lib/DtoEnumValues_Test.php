<?php
declare(strict_types=1);
namespace testworld;


use PHPUnit\Framework\TestCase;



class CfdEnumPhase4 extends \ElegantTechnologies\Cfd\Lib\Cfes {
    public function __construct(public array $value,)
    {
        parent::__construct(['Draft', 'RollOut', 'LaunchPad', 'OnOrbit', 'Descent', 'Museum', 'Trash']);
    }
}

final class DtoEnumValues_Test extends TestCase {

    function testBasics() {
        $obj = new CfdEnumPhase4(['Draft']);
        $this->assertTrue(isset($obj), "Good");
        $this->assertTrue($obj->value[0] == 'Draft', "Good");

        try {
            $obj = new \testworld\CfdEnumPhase4(['EnumValues' => 'Explosion']);
            $this->assertTrue(0, "Should not get this far cuz not an array");
        } catch (\TypeError $e) {
            $this->assertTrue(true, "Good - that faiiled as expected cuz tried passing an value instead of an array");
        }

        try {
            $obj = new \testworld\CfdEnumPhase4(['Explosion']);
            $this->assertTrue(0, "Should not get this far cuz not an valid type");
        } catch (\ElegantTechnologies\Cfd\Core\CfdError $e) {
            $this->assertTrue(true, "Good - that faiiled as expected");
        }

    }

    function testBasics2() {
        $obj = new CfdEnumPhase4(['Draft', 'OnOrbit']);
        $this->assertTrue($obj->value[0] == 'Draft', "Good");
        $this->assertTrue($obj->value[1] == 'OnOrbit', "OnOrbit");


        try {
            $obj = new CfdEnumPhase4(['Draft', 'Explosion']);
            $this->assertTrue(0, "Should not get this far cuz Explosion is a valid value");
        } catch (\ElegantTechnologies\Cfd\Core\CfdError $e) {
            $this->assertTrue(true, "Good - that faiiled as expected");
        }

    }
}
