<?php
declare(strict_types=1);
class BeEven {
    public function __construct(public int $value) {
        $isEven = ($value % 2) == 0;
        if (!$isEven) {
            throw new \Exception("$value is not an even number");
        }
    }
}

class EnumValueBase {
    public function __construct(array $_ArrEnumValuePossibilities){
            $maybeValidItem = $this->value;
            $hasPropertyThere = in_array($maybeValidItem, $_ArrEnumValuePossibilities) ? 1 : 0;

           if (!$hasPropertyThere){
               $csvOptions = implode(', ', $_ArrEnumValuePossibilities);
                $meClass = $this::class;
               throw new \Exception("'$maybeValidItem' is not a valid enumeration for $meClass::valuePossibilities([$csvOptions])",-87687545);
           }
    }
}

class QuickEnumValue extends EnumValueBase {
    public function __construct(array $valuePossibilities, public string $value, ) { parent::__construct($valuePossibilities);}
}


class Phase extends EnumValueBase {
    public function __construct(public string $value) { parent::__construct(['open','closed']);}
}



class EnumValuesBase {
    public function __construct(array $_ArrEnumValuePossibilities){
            $maybeValidItem = $this->value;
            $hasPropertyThere = in_array($maybeValidItem, $_ArrEnumValuePossibilities) ? 1 : 0;

           if (!$hasPropertyThere){
               $csvOptions = implode(', ', $_ArrEnumValuePossibilities);
                $meClass = $this::class;
               throw new \Exception("'$maybeValidItem' is not a valid enumeration for $meClass::valuePossibilities([$csvOptions])",-87687545);
           }
    }
}

class QuickEnumValues extends EnumValuesBase {
    public function __construct(array $valuePossibilities, public array $values, ) { parent::__construct($valuePossibilities);}
}



$e = new BeEven(2);
print "\n " .$e->value;
try {
    $e = new BeEven(3);
} catch (\Throwable $exception) {
    print "\n " .$exception->getMessage();
}


$s = new QuickEnumValue( ['active','inactive'], value:'active',);

try {
    $e = new QuickEnumValue(['active','inactive'], value:'done', );
} catch (\Throwable $exception) {
    print "\n " .$exception->getMessage();
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