<?php
declare(strict_types=1);
namespace ElegantTechnologies\Cfd\Core;

/* Usage
Dto is really just the Initialize Through Constructor php 8 pattern.

class Dto3 extends Dto
{
    public function __construct(
        public string $name,
        public ?int $age,
        public int $numNoses = 1,
    ) {}
}


class BeEven extends \ElegantTechnologies\Cfd\Core\Dto {
    public function __construct(public int $value) {
        $isEven = ($value % 2) == 0;
        if (!$isEven) {
            throw new \Exception("$value is not an even number");
        }
    }
}


*/
abstract class Dto {
    #nothing to see here. Just follow the php assign in constructor declaration.
}



