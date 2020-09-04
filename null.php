<?php
declare(strict_types=1);
class a {
    CONST NullType = 'DefaultsToButWeUseThisCuzPhpCanNotSeemToDetectNullAsADefault';
    public $b;// fun fact, this actually defaults to null

    public $c = a::NullType;
    public $d = 1;
    public $n = null;
    public ?int $r = null;
}
//$r = new ReflectionClass(a::class);
//$asrDefaultProperties = $r->getDefaultProperties();
//foreach ($r->getProperties() as $reflectionProperty) {
//    $allowsNull = $reflectionProperty->getType()->allowsNull();
//
//}
$objA = new a();
print_r(isset($objA->b) ? 1 : 0);   // 0


//print_r(is_null($objA->b) ? 1 : 0); // 1 -- Look, it is null, even though not set
//print_r($r->getProperty('b')->isDefault() ? 1 : 0); // 1 -- cuz defined at compile time
//$objA->b2 = 1;
//#print_r($r->getProperty('b2')->isDefault($objA) ? 1 : 0); // 0
//print_r($r->getProperty('b')->isInitialized($objA) ? 1 : 0); // 1 for some reason
//
//print_r(isset($objA->d) ? 1 : 0);   // 1
//
//print_r(isset($objA->n) ? 1 : 0);   // 0 -- Look, still not set
//print_r(is_null($objA->n) ? 1 : 0); // 1
//print "\n";
//print_r($r->getDefaultProperties());   // 0
//print_r(is_null($objA->b) ? 1 : 0); // 1 -- Look, it is null, even though not set
//print_r($r->getProperty('b')->isDefault() ? 1 : 0); // 1 -- cuz defined at compile time




class b {}
$objB = new b();


class c {
    public function __construct(
        public float $theC,
        public int|string $theB = 5,
    )
    {}
}
$objC = new c(theC:1.1, theB:2);
print_r($objC);
$objC = new c(theC:1.1);
print_r($objC);



#print_r(ReflectionProperty::export(a::class,'n'));
//print_r(is_null($r->getDefaultProperties()['b']) ? 1 : 0);
//print_r(is_null($r->getDefaultProperties()['d']) ? 1 : 0);
//print_r($r->getProperty('d')->getDocComment());
//$objA->n = 1;
//print_r(is_null($r->getProperty('d')->isInitialized($objA)) ? 1 : 0);
//#print_r($r->getProperty('n')->isDefault() ? 1 : 0);
//#print_r($r->getProperty('b'));

#print is_null(a::$i) ? 1 : 0;



