<?php
declare(strict_types=1);
namespace testworld;


use PHPUnit\Framework\TestCase;


class CfdEnumPhase2 extends \ElegantTechnologies\Cfd\Lib\Cfes {
    /** @var array */
    public array $EnumValues;
    public static array $_ArrEnumValuePossibilities = ['Draft', 'RollOut', 'LaunchPad', 'OnOrbit', 'Descent', 'Museum', 'Trash'];
}

final class TestDtoEnumPhase2 extends TestCase {

    function testBasics() {
        $obj = new CfdEnumPhase2(['EnumValues' => ['Draft']]);
        $this->assertTrue(isset($obj), "Good");
        $this->assertTrue($obj->EnumValues[0] == 'Draft', "Good");

        try {
            $obj = new \testworld\CfdEnumPhase2(['EnumValues' => 'Explosion']);
            $this->assertTrue(0, "Should not get this far cuz not an array");
        } catch (\TypeError $e) {
            $this->assertTrue(true, "Good - that faiiled as expected cuz tried passing an value instead of an array");
        }

        try {
            $obj = new \testworld\CfdEnumPhase2(['EnumValues' => ['Explosion']]);
            $this->assertTrue(0, "Should not get this far cuz not an valid type");
        } catch (\ElegantTechnologies\Cfd\Core\ErrorFromCfd $e) {
            $this->assertTrue(true, "Good - that faiiled as expected");
        }

    }

    function testBasics2() {
        $obj = new CfdEnumPhase2(['EnumValues' => ['Draft', 'OnOrbit']]);
        $this->assertTrue($obj->EnumValues[0] == 'Draft', "Good");
        $this->assertTrue($obj->EnumValues[1] == 'OnOrbit', "OnOrbit");


        try {
            $obj = new CfdEnumPhase2(['EnumValues' => ['Draft', 'Explosion']]);
            $this->assertTrue(0, "Should not get this far cuz Explosion is a valid value");
        } catch (\ElegantTechnologies\Cfd\Core\ErrorFromCfd $e) {
            $this->assertTrue(true, "Good - that faiiled as expected");
        }

    }
}
