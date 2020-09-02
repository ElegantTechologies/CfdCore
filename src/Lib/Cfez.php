<?php
declare(strict_types=1);
namespace ElegantTechnologies\Cfd\Lib;

/* Usage:
    $s = new QuickEnumValue( ['active','inactive'], value:'active',);

    try {
        $e = new QuickEnumValue(['active','inactive'], value:'done', );
    } catch (\Throwable $exception) {
        print "\n " .$exception->getMessage();
    }
*/
class Cfez extends Cfe {
    public function __construct(array $valuePossibilities, public string $value, ) { parent::__construct($valuePossibilities);}
}