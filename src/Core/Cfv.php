<?php

declare(strict_types=1);

namespace ElegantTechnologies\Cfd\Core;

/* Usage
class Vto1 extends \ElegantTechnologies\Cfd\Core\Vto
{
    public function __construct(
        public string $value,
    ) {parent::__construct();}
}

--or--
class BeEven extends \ElegantTechnologies\Cfd\Core\Vto {
    public function __construct(public int $value) {
        $isEven = ($value % 2) === 0;
        if (!$isEven) {
            throw new \ElegantTechnologies\Cfd\Core\CfdError("$value is not an even number");
        }
        parent::__construct();
    }
}

*/

class Cfv extends Cfd
{
    public function __construct() {
        parent::__construct();

        // Cfv (vs. Cfd) has one, and only one, property. It is called 'value'
        if (count($this->_wrappedValues) != 1 || array_key_first($this->_wrappedValues) != 'value') {
            $csvPropertyNames = implode(', ',array_keys($this->_wrappedValues) );
            $meName = $this::class;
            throw new CfdError(
                "$meName is a Vto, which can  have one, and only property. It must be named 'value'. You currently have these properties: ($csvPropertyNames)"
            );
        }

    }
}



