<?php

declare(strict_types=1);

namespace ElegantTechnologies\Cfd\Core;

/* Usage
class Vto1 extends \ElegantTechnologies\Cfd\Core\Vto
{
    public function __construct(
        public string $value,
    ) {parent::__construct(...func_get_args());}
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

class Cfd implements \ElegantTechnologies\Validations\Contracts\ArrayableShallow
{

    private $_wasParentCalled = false;
    protected $_wrappedValues = [];
    #https://ocramius.github.io/blog/intercepting-public-property-access-in-php/
        public function __construct()
        {
            // make all public properties now be private to avoid accidental access
            // Helps ensure it can't be changed externally.  Unsetting it lets __get get invoked. Getter/Setter Hack.
            $reflectionClass = new \ReflectionClass($this::class);
        $asrPublicProperties = $reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC + !\ReflectionProperty::IS_STATIC);
        #$asrStaticProperties = $reflectionClass->getProperties(\ReflectionProperty::IS_STATIC );// I forget... xor(?)
        foreach ($asrPublicProperties as $asrPublicProperty) {
            $propertyName = $asrPublicProperty->name;
            #if (!array_key_exists($propertyName, $asrStaticProperties)) {
            // expect issues here when public and static... worry later.

                $this->_wrappedValues[$propertyName] = $this->$propertyName;
                unset($this->$propertyName);
            #}

        }

        $this->_wasParentCalled = true; // CAUTION: It is pointless to check this via __get or __set cause they'd only be called if the property got unset


            }
    public function __get($name)
    {
        if (!array_key_exists($name,$this->_wrappedValues)) {
            $csvPropertyNames = implode(', ',($this->_wrappedValues));
            throw new CfdError("$name is not a public property of ".$this::class. "::[{$csvPropertyNames}]"); #not seeting the keys?  9/2/20' issue with public static got this called when a public static wasn't already set.
        }
        return $this->_wrappedValues[$name];
    }

    public function __set($name, $value)
    {
        $meName = $this::class;
        throw new CfdError("$meName is a Cfd/Cfv, so you can not update $meName::$name. $meName->$name is read only, so you can do \$o = new $meName(x); \$v = \$o->$name, but not \$o->$name = x2;");
    }

    private function _ensureInited() {
        if (!$this->_wasParentCalled) {
            throw new CfdError($this::class." did not get it's construction called.");
        }
    }

    //Interface ArrayableShallow
    /**
     * Get the instance as an array.
     */
    public function toShallowArray() : array {
        $this->_ensureInited();
        return $this->_wrappedValues;
    }

    public function getPropertyNames() : array {
        $this->_ensureInited();
        return array_keys( $this->_wrappedValues);
    }

    public static function newViaAsr(array $values) : self {
        $meName = get_called_class();
        // if you see Error: Unknown named parameter $Slug here, then I bet you have class properties instead of properties as part of the constructor
        #print_r([__FILE__,__LINE__,$meName, $values]);
        #return new $meName(...$values);
        try {
            return new $meName(...$values);
        } catch (\TypeError $e) {
            // see if these can be upconverted
            $thisReflector = new \ReflectionClass(static::class);
            foreach ($values as $propertyName=>$value) {
                $cfdBaseClassName = Cfd::class;;
                $typeOfNamedProperty = $thisReflector->getProperty($propertyName)->getType().''; // testworld\ALuckyNumber|string
                $typeOfPassedValue = gettype($value);
                if ($typeOfPassedValue == 'object') {
                    $typeOfPassedValue = get_class($value);
                }
                $typeOfPassedValue = ($typeOfPassedValue == 'integer') ? 'int' : $typeOfPassedValue;
                $isGoodEnoughAsPassed = ($typeOfPassedValue == $typeOfNamedProperty) || is_a($typeOfPassedValue, $typeOfNamedProperty.'', true);
                #print_r([__FILE__,__LINE__,"$propertyName is type: ".$typeOfNamedProperty]);
                #$isTargetCfd = is_a($typeOfNamedProperty,$cfdBaseClassName, true);
                if ($isGoodEnoughAsPassed) {
                    #print_r([__FILE__,__LINE__,"Good Enough: $propertyName($typeOfPassedValue) is_a $typeOfNamedProperty"]);
                } else {
                    $isDestinedForCfd = is_a($typeOfNamedProperty, $cfdBaseClassName, true) ? 1 : 0;
                    $isTargetCfd = is_subclass_of($typeOfNamedProperty, '\\'.$cfdBaseClassName, true);
                    if (!$isDestinedForCfd) {
                        throw new CfdError(
                            "Type Error: $propertyName was passed as type $typeOfPassedValue but it must be  $typeOfNamedProperty or $typeOfNamedProperty must derive from $cfdBaseClassName so that it might be up-converted."
                        );
                    } else {
                        // This is a cfd - maybe we can convert it
                        $convertedValue = $value = new $typeOfNamedProperty($value); // FYI: Throws a \TypeError if still not the right type
                        $values[$propertyName] = $convertedValue;
                        $typeOfPassedValueOrig = $typeOfPassedValue;
                        $typeOfPassedValue = gettype($values[$propertyName]);
                        if ($typeOfPassedValue == 'object') {
                            $typeOfPassedValue = get_class($value);
                        }
                        $isGoodEnoughAsPassed = ($typeOfPassedValue == $typeOfNamedProperty) || is_a($typeOfPassedValue, $typeOfNamedProperty.'', true) ? 1 : 0;
                        //                        print_r(
                        //                            [
                        //                                __FILE__,
                        //                                __LINE__,
                        //                                "CFD: Converted $propertyName($typeOfPassedValueOrig) to specked type $typeOfNamedProperty. New type is $typeOfPassedValue isGoodEnoughAsPassed/upconverted($isGoodEnoughAsPassed)"
                        //                            ]
                        //                        );
                    }
                }
            }
            return new $meName(...$values);
        }
    }

    /*
     * This should be in a utility area...
    Given an array of dto items, and a key, return an array
    class DtoTab {
         public $Slug;
        public $Text
    }
    $arrTabs = [
        new DtoTab(['Slug'=>'Merge','Text'=>'Combine']),
        new DtoTab(['Slug'=>'Trash','Text'=>'Delete'),
    ];
    $selected = $_REQUEST['Tab'];// say, 'Trash',
    $slugOptions = Cfd::arrCfd_toColumn($arrTabs,'Slug');// now [Merge,Trash]
    if (in_array($selected,Cfd_Base::arrDto_column($arrTabs,'Slug')) {
        ....

    */
    public static function arrCfd_toColumn(array $arrayOfCfdObjects, $subKey): array
    { // inspired by php's array_column
        $arr = array_map(function (Cfd $Dto) use ($subKey) {
            return $Dto->$subKey;
        }, $arrayOfCfdObjects);

        return $arr;
    }
}



