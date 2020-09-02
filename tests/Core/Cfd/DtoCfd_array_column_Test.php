<?php
declare(strict_types=1);
namespace testworld;

use PHPUnit\Framework\TestCase;


class DtoTabForTestingStuff extends \ElegantTechnologies\Cfd\Core\Cfd {
    public string $Slug;
    public string $Text;
}



final class TestDtoCfd_array_column extends TestCase {
    function testBasics() {
        $obj = new \testworld\DtoTabForTestingStuff(['Slug' => 'Delete', 'Text'=>'Trash']);
        $this->assertTrue(isset($obj), "Good");

    }

    function testMore() {
        $arrTabs = [
            new \testworld\DtoTabForTestingStuff(['Slug' => 'Delete', 'Text'=>'Trash']),
           new \testworld\DtoTabForTestingStuff(['Slug' => 'Merging', 'Text'=>'Shrink']),
            ];
        $arrSlugs = \ElegantTechnologies\Cfd\Core\Cfd::arrDto_column($arrTabs, 'Slug');
          $this->assertTrue($arrSlugs[0] == 'Delete', "ok");
          $this->assertTrue($arrSlugs[1] == 'Merging', "ok");
          $this->assertTrue(count($arrSlugs) ==2 , "ok");



    }
}
