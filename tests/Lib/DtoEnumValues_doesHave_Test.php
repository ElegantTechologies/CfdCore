<?php
declare(strict_types=1);
namespace testworld;

use PHPUnit\Framework\TestCase;

class CfdEnumPhase2 extends \ElegantTechnologies\Cfd\Lib\Cfes {
    public function __construct(public array $value,)
    {
        parent::__construct(['Draft', 'RollOut', 'LaunchPad', 'OnOrbit', 'Descent', 'Museum', 'Trash']);
    }
}

final class DtoEnumValues_doesHave_Test extends TestCase {
    function testBad()
    {
        $obj = new CfdEnumPhase2(['Draft']);
        $this->assertTrue(isset($obj), "Good");
        $this->assertFalse($obj->doesHaveThis('Prod'), "Good");
        $this->assertFalse($obj->doesHaveThis(null), "Good");
        $this->assertFalse($obj->doesHaveThis(1), "Good");
        $this->assertFalse($obj->doesHaveThis(true), "Good");
        $this->assertFalse($obj->doesHaveThis(''), "Good");
        $this->assertTrue($obj->doesHaveThis('Draft'), "Good");
    }
    function testGood() {
        $obj = new CfdEnumPhase2(['Draft']);
        $this->assertTrue(isset($obj), "Good");
        $this->assertTrue($obj->doesHaveThis('Draft'), "Good");
    }

  function testBad2()
    {
        $obj = new CfdEnumPhase2(['Draft']);
        $this->assertTrue(isset($obj), "Good");
        $this->assertFalse($obj->doesHaveThese(['Prod']), "Good");
        $this->assertFalse($obj->doesHaveThese([null]), "Good");
        $this->assertFalse($obj->doesHaveThese([1]), "Good");
        $this->assertFalse($obj->doesHaveThese([true]), "Good");
        $this->assertFalse($obj->doesHaveThese(['']), "Good");
    }

    function testBad4()
    {
        $obj = new CfdEnumPhase2(['Draft']);
        $this->assertTrue(isset($obj), "Good");
         try {
             $isTrue = $obj->doesHaveThese('Prod');
            $this->assertTrue(0, "Should not get this far cuz not an array");
        } catch (\Throwable $e) {
            $this->assertTrue(true, "Good - that faiiled as expected");
        }

         try {
             $isTrue = $obj->doesHaveThese();
            $this->assertTrue(0, "Should not get this far cuz no params");
        } catch (\Throwable $e) {
            $this->assertTrue(true, "Good - that faiiled as expected");
        }

    }


    function testGood2() {
        $obj = new CfdEnumPhase2(['Draft']);
        $this->assertTrue(isset($obj), "Good");
        $this->assertTrue($obj->doesHaveThese(['Draft']), "Good");
    }
    function testGood3() {
        $obj = new CfdEnumPhase2(['Draft','RollOut']);
        $this->assertTrue(isset($obj), "Good");
        $this->assertTrue($obj->doesHaveThese(['Draft']), "Good");
    }
    function testGood4() {
        $obj = new CfdEnumPhase2(['Draft', 'RollOut', 'LaunchPad', 'OnOrbit', 'Descent']);
        $this->assertTrue(isset($obj), "Good");
        $this->assertTrue($obj->doesHaveThese(['Draft','RollOut']), "Good");
        $this->assertTrue($obj->doesHaveThese(['RollOut','Draft']), "Good");
        $this->assertTrue($obj->doesHaveThese(['RollOut','Draft','OnOrbit']), "Good");
        $this->assertTrue($obj->doesHaveThese(['RollOut','OnOrbit']), "Good");
        $this->assertTrue($obj->doesHaveThese(['RollOut']), "Good");
    }


     function testBadOnly()
    {
        $obj = new CfdEnumPhase2(['Draft','RollOut']);
        $this->assertTrue(isset($obj), "Good");
        $this->assertFalse($obj->doesHaveOnlyThis('Prod'), "Good");
        $this->assertFalse($obj->doesHaveOnlyThis('Draft'), "Good");
        $this->assertFalse($obj->doesHaveOnlyThis('Rollout'), "Good");
        $this->assertFalse($obj->doesHaveOnlyThis(true), "Good");
        $this->assertFalse($obj->doesHaveOnlyThis(''), "Good");

          $obj = new CfdEnumPhase2(['Draft']);
        $this->assertTrue(isset($obj), "Good");
        $this->assertFalse($obj->doesHaveOnlyThis('Prod'), "Good");
        $this->assertFalse($obj->doesHaveOnlyThis('Rollout'), "Good");
        $this->assertFalse($obj->doesHaveOnlyThis(true), "Good");
        $this->assertFalse($obj->doesHaveOnlyThis(''), "Good");

    }
    function testGoodOnly() {
        $obj = new CfdEnumPhase2(['Draft']);
        $this->assertTrue(isset($obj), "Good");
        $this->assertTrue($obj->doesHaveOnlyThis('Draft'), "Good");
    }
}
