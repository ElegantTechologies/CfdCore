<?php
declare(strict_types=1);
namespace testworld;

use PHPUnit\Framework\TestCase;


class DtoTabForTestingStuff extends \ElegantTechnologies\Cfd\Core\Cfd {
    public function __construct(
        public string $Slug,
        public string $Text,
        ) {parent::__construct(...func_get_args());}
}



final class DtoCfd_array_column_Test extends TestCase {
    function testBasics() {
        $obj = DtoTabForTestingStuff::newViaAsr(['Slug' => 'Delete', 'Text'=>'Trash']);
        $this->assertTrue(isset($obj), "Good");

    }

    function testMore() {
        $arrTabs = [
             \testworld\DtoTabForTestingStuff::newViaAsr(['Slug' => 'Delete', 'Text'=>'Trash']),
           \testworld\DtoTabForTestingStuff::newViaAsr(['Slug' => 'Merging', 'Text'=>'Shrink']),
            ];
        $arrSlugs = \ElegantTechnologies\Cfd\Core\Cfd::arrCfd_toColumn($arrTabs, 'Slug');
          $this->assertTrue($arrSlugs[0] == 'Delete', "ok");
          $this->assertTrue($arrSlugs[1] == 'Merging', "ok");
          $this->assertTrue(count($arrSlugs) ==2 , "ok");



    }
}
