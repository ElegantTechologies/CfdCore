<?php
declare(strict_types=1);
namespace ElegantTechnologies\Cfd\Lib;

/* Usage:
    $s = new Cfesz( ['active','inactive'], value:['active'],);
    $s = new Cfesz( ['php','c++','go'], value:['php','c++'],);


*/
class Cfesz extends Cfes {
    public function __construct(array $valuePossibilities, public array $value, ) { parent::__construct($valuePossibilities);}
}