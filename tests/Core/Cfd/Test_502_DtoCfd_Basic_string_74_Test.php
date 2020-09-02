<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;


class CfdoLove extends \ElegantTechnologies\Cfd\Core\Cfd {

    public int $isInLove;

    public string $name;
}
class Test_t02_DtoCfd_Basic_string_74 extends TestCase
{
    function testPreValidationsSubmission_byProperty()
    {
        $asrData = [
            'isInLove' => 1,
            'name'=>'Lisa'
        ];
        $obj = new CfdoLove($asrData);
        #var_dump($obj->toArray());
        $this->assertTrue($obj->isInLove == 1, "ouch");
    }

}
