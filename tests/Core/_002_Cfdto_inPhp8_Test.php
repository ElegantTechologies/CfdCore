<?php

declare(strict_types=1);
namespace SchoolTwist\Cfd\Core;
use PHPUnit\Framework\TestCase;

abstract class CfdtoBase
{
    public const FORBIDDEN_TYPES = [ ]; // mainly to avoid confusion. Actually - bool is 'true'. Boolean is just an alias: https://stackoverflow.com/questions/44009037/php-bool-vs-boolean-type-hinting
    public function __construct(...$params)
    {
        // maybe upconvert things... not sure
        // further validate each value
        print_r($this->getRichProperties());
    }

    private static ?array $_asrRichProperties = null;

    public static function getRichProperties(): array
    {
        $rootClassName = get_called_class();
        if (!$rootClassName::$_asrRichProperties) {
            $rootClassName::$_asrRichProperties = static::getRichProperties_ofDtoNamed(get_called_class());
        }
        return static::getRichProperties_ofDtoNamed(get_called_class());
    }

      private
    static function getRichProperties_ofDtoNamed(string $CfdClassName): array
    {

        $reflectionClass = new \ReflectionClass($CfdClassName);

        $asrDefaultProperties = $reflectionClass->getDefaultProperties();
        #print_r([__FILE__,__LINE__,$asrDefaultProperties]);

        $richProperties = [];

        $numNonMeta = 0;
         #print_r([__FILE__,__LINE__,$reflectionClass->getProperties()]);
        foreach ($reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
            $asrThisProperty = [];

            $propertyName = $reflectionProperty->getName();
            $asrThisProperty['name'] = $propertyName;

            $asrThisProperty['isStatic'] = $reflectionClass->getProperty($propertyName)->isStatic();
            $asrThisProperty['hasDefault'] =  isset($asrDefaultProperties[$propertyName]); // Note: look at getDefaultProperties. It returns an asr of all properties and their defualt value, or NULL if no default is set.  This breaks when the default is actually NULL.
            $asrThisProperty['default'] = ($asrThisProperty['hasDefault']) ? $asrDefaultProperties[$propertyName] : 'no default set';
            #assert($asrThisProperty['hasDefault'] || $asrDefaultProperties[$propertyName])

            $asrThisProperty['isPublic'] = $reflectionProperty->isPublic();
            $asrThisProperty['isProtected'] = $reflectionProperty->isProtected();
            $asrThisProperty['isPrivate'] = $reflectionProperty->isProtected();

            $asrThisProperty['isManaged'] =  $asrThisProperty['isPublic'];
            $asrThisProperty['isTypeEnforced'] = $reflectionProperty->hasType() && $asrThisProperty['isManaged'];
            $asrThisProperty['isValueRequiredAtCreation'] =  $asrThisProperty['isPublic'] && !$asrThisProperty['hasDefault'];


            $validatingMethodName = "{$propertyName}_Validates";
            $asrThisProperty['getsValidated'] = method_exists(static::class, $validatingMethodName);
            $asrThisProperty['getsValidatedByMethodName'] = $asrThisProperty['getsValidated'] ? $validatingMethodName : null;

            if (!$asrThisProperty['isTypeEnforced']) {
                $asrThisProperty['types'] = ['na - not type enforced'];
                $asrThisProperty['isNullAnAllowedType']= 'na - not type enforced';
            } else {
                // this will change in php 8
                $typeIsAsString = $reflectionProperty->getType()->getName(); //https://www.php.net/manual/en/reflectiontype.tostring.php
                $asrThisProperty['types'] = [$typeIsAsString];


                /* 1) Handle builtin name mismatches.
                     public int $i; // this returns a type with name 'integer' which is annoying
                     public bool $b;// this returns a type with name 'boolean'.
                     We're going to account for this by saying it can be of type 'int' or 'integer'.
                     I suspect this will problematic in the future, but I'm not sure.
                2) In php 8, we'll be able to let a property be restricted a list of specified types, like int, double..
                     This is supposed to handle that.  It used to work when in docblock days, but not really used.
                */
                if ($typeIsAsString == 'int') {
                    $asrThisProperty['types'][] = 'integer';// in php 8, we should start allowing multiple types
                } elseif ($typeIsAsString == 'bool') {
                    $asrThisProperty['types'][] = 'boolean';// in php 8, we should start allowing multiple types
                }

                $asrThisProperty['isNullAnAllowedType'] = $reflectionProperty->getType()->allowsNull();
                if ($asrThisProperty['isNullAnAllowedType']) {
                    $asrThisProperty['types'][] = 'null';
                }
            }

            // Ensure no forbidden types
            $forbiddenTypes = $CfdClassName::FORBIDDEN_TYPES;
            $forbiddenTypesFoundHere = array_intersect($forbiddenTypes, $asrThisProperty['types']);
            if (count($forbiddenTypesFoundHere) > 0) {
                $csvTypes = implode(', ', $forbiddenTypesFoundHere);
                throw ErrorFromCfd::LogicError(
                    "This " . get_called_class(
                    ) . "($propertyName) is specked to be of type $csvTypes, but that is on the forbidden list."
                );
            }


            $dtoThisProperty = new DtoRichProperty($asrThisProperty);
            $richProperties[$propertyName] = $dtoThisProperty;
        }


        $asr = ['_meta' =>
            ['numNonMeta' => $numNonMeta,
                'className'=>get_called_class()],
            'properties' => $richProperties];

        return $asr;

    }
}

class Cfdto0 extends
    CfdtoBase #implements \SchoolTwist\Validations\Contracts\ArrayableDeep, \SchoolTwist\Validations\Contracts\ArrayableShallow
{
    public function __construct()
    {
        parent::__construct(...func_get_args());
    }
}

class Cfdto1 extends CfdtoBase
{
    public function __construct(
        public string $name
    ) { parent::__construct(...func_get_args());}
}

class DtoValid
{
    public function __construct(
        public bool $isValid,
        public string $message = '',
        public string $enumReason = '',
        public mixed $oldValue = null,
        public mixed $newValue = null,
    ){}
}

//class Cfdto1
//{
//    public function __construct(
//        public string $name,
//    ) {}
//}
//
class Cfdto2 extends CfdtoBase
{
    public function __construct(
        public string $name,
        public int $age,
    ) { parent::__construct(...func_get_args());}
}

//
class Cfdto3 extends CfdtoBase
{
    public function __construct(
        public string $name,
        public ?int $age,
        public int $numNoses = 1,
    ) { parent::__construct(...func_get_args());}
}




class _002_Cfdto_inPhp8_Test extends TestCase
{

    function test_Num()
    {
        $this->assertTrue(true, "Should not get here: Not passed any vars");

        // Simple
        $c = new Cfdto0();

        // Missing var
        try {
            $c = new Cfdto1();
            $this->assertTrue(false, "Should not get here: Not passed any vars");
        } catch (\ArgumentCountError $e) {
            $this->assertTrue(
                true,
                "This should have failed hard " . $this::class . "  " . __LINE__
            );
        }
        //
        // Right num vars
        $c = new Cfdto1('jj');
        $this->assertTrue($c->name == 'jj', "is ok");
        //
        //
        // Missing var
        try {
            $c = new Cfdto2('jj');
            $this->assertTrue(false, "Should not get here: missing age");
        } catch (\ArgumentCountError $e) {
            $this->assertTrue(
                true,
                "This should have failed hard (and gotten here) .  " . $this::class. "  " . __LINE__
            );
        }
        //
        // Right num vars
        $c = new Cfdto2('jj', 49);
        $this->assertTrue($c->name == 'jj', "is ok");

        //
        // Wrong Type
        try {
            $c = new Cfdto2(49, 'jj');
            $this->assertTrue(false, "Should not get here: missing age");
        } catch (\TypeError $e) {
            $this->assertTrue(
                true,
                "This should have failed hard (and gotten here) .  " . $this::class. "  " . __LINE__
                    );
                }

        // Named Vars - the way god intended.
        $c = new Cfdto2(age:49, name:'jj');
                $this->assertTrue(true, "ok");

                // Named Vars - with default limbs.
                $c = new Cfdto3(age:49, name:'jj', numNoses:1);
                $this->assertTrue(true, "ok");


                $c = new Cfdto3(age:49, name:'jj');
                $this->assertTrue(true, "ok cuz numNoses has a default even though we didn't specify numNoses");

              try {
                  $c = new Cfdto3(name:'jj');
                    $this->assertTrue(
                        false,
                        "Should not get here cuz we didn't specify age.  One might think it defaults to null.  It doesn't"
                    );
                } catch (\TypeError $e) {
                  $this->assertTrue(
                      true,
                      "This should have failed hard (and gotten here) .  " . $this::class. "  " . __LINE__
                    );
                }

                      $c = new Cfdto3(name:'jj', age:null);
                $this->assertTrue(
                    $c->name == 'jj',
                    "ok cuz numNoses has a default even though we didn't specify numNoses"
                );
                $this->assertTrue(
                    is_null($c->age),
                    "ok cuz numNoses has a default even though we didn't specify numNoses"
                );
//

    }

}
