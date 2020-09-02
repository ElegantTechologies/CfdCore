<?php
declare(strict_types=1);
namespace ElegantTechnologies\Cfd\Lib;

/*
 * Usage:
 *      class Phase extends EnumValueBase {
            public function __construct(public string $value) { parent::__construct(['open','closed']);}
        }
        $o = new Phase('open');
        print "\n I am ".$o->value;

        $o = new Phase('closed');
        print "\n I am ".$o->value;

        try {
            $o = new Phase('shut');
        } catch (\Throwable $exception) {
            print "\n " .$exception->getMessage();
        }
*/


abstract class Cfe extends \ElegantTechnologies\Cfd\Core\Vto {
    #abstract public string $value;
    public function __construct(private array $_ArrEnumValuePossibilities){
            $maybeValidItem = $this->value;
            $hasPropertyThere = in_array($maybeValidItem, $_ArrEnumValuePossibilities) ? 1 : 0;

           if (!$hasPropertyThere){
               $csvOptions = implode(', ', $_ArrEnumValuePossibilities);
                $meClass = $this::class;
               throw new \ElegantTechnologies\Cfd\Core\ErrorFromCfd("'$maybeValidItem' is not a valid enumeration for $meClass::valuePossibilities([$csvOptions])",-87687545);
           }
           parent::__construct();
    }


}
