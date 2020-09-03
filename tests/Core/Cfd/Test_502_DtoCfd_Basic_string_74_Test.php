<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;


class CfdoLove extends \ElegantTechnologies\Cfd\Core\Cfd {
    public function __construct(
        public int $isInLove,
        public string $name,
    ){
        parent::__construct(...func_get_args());
    }
}
class Test_502_DtoCfd_Basic_string_74_Test extends TestCase
{
    function testPreValidationsSubmission_byProperty()
    {
        $asrData = [
            'isInLove' => 1,
            'name'=>'Lisa'
        ];
        $obj = new CfdoLove(...$asrData);
        $this->assertTrue($obj->isInLove == 1, "ouch");

        $obj = CfdoLove::newViaAsr($asrData);
        $this->assertTrue($obj->isInLove == 1, "ouch");
    }

}
