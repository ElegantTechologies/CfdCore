<?php

declare(strict_types=1);

namespace ElegantTechnologies\Cfd\Core;
use \ElegantTechnologies\Validations\Returns\DtoValid;
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
    private static function _doesPropertyExist(string $propertyName): bool {
        $thisReflector = new \ReflectionClass(static::class);
        return ($thisReflector->hasProperty($propertyName));
    }

    private static function _isExistingPropertyManaged(string $propertyName):bool {
        assert(static::_doesPropertyExist($propertyName), static::class."::$propertyName does not exist");
        return (new \ReflectionClass(static::class))->getProperty($propertyName)->isPublic();
    }
     public static function isArgumentType_sameAsPropertyType_onlyUseForTesting(string $propertyName, $value_withUnknownTypeCompatibility):bool
    {
        return static::_isArgumentType_sameAsPropertyType( $propertyName, $value_withUnknownTypeCompatibility);
    }

    private static function _isArgumentType_sameAsPropertyType(string $propertyName, $value_withUnknownTypeCompatibility):bool {
        assert(static::_isExistingPropertyManaged($propertyName));
        // Assume property exists...
        $thisReflector = new \ReflectionClass(static::class);
        $typeOfNamedProperty_asString = $thisReflector->getProperty($propertyName)->getType().''; // testworld\ALuckyNumber|string
        $typeOfPassedValue = gettype($value_withUnknownTypeCompatibility);
        if ($typeOfPassedValue == 'object') {
            $typeOfPassedValue = get_class($value_withUnknownTypeCompatibility);
        }
        $typeOfPassedValue = ($typeOfPassedValue == 'integer') ? 'int' : $typeOfPassedValue;
        $typeOfPassedValue = ($typeOfPassedValue == 'boolean') ? 'bool' : $typeOfPassedValue;
        $arrPropertiesTypes = explode('|',$typeOfNamedProperty_asString.'');// probably a more elegant way to test natively with type casting
        $isGoodEnoughAsPassed =
            in_array($typeOfPassedValue, $arrPropertiesTypes)
            ||
            is_a($typeOfPassedValue, $typeOfNamedProperty_asString, true);
        #print_r([__FILE__,__LINE__,$arrPropertiesTypes, static::class."==>$propertyName($typeOfNamedProperty_asString) <--> $typeOfPassedValue ~~~ isGoodEnoughAsPassed:".(  $isGoodEnoughAsPassed ? 1 : 0)]);
        if ($isGoodEnoughAsPassed) {
            return true;
        } else {
            return false;
        }
    }

    private static function _getPropertyType_asString(string $propertyName): string {
        assert(static::_doesPropertyExist($propertyName));
        assert(static::_isExistingPropertyManaged($propertyName));
        $thisReflector = new \ReflectionClass(static::class);
        $typeOfNamedProperty_asString = $thisReflector->getProperty($propertyName)->getType().''; // testworld\ALuckyNumber|string
        return $typeOfNamedProperty_asString;
    }

    private static function _isPropertyCfd(string $propertyName) : bool {
        $cfdName = Cfd::class;
        $propertyNameType = static::_getPropertyType_asString($propertyName);
        $isSub = is_subclass_of($propertyNameType, $cfdName, true);
        #print_r([__FILE__,__LINE__," is_subclass_of($propertyNameType, $cfdName) isSub(".($isSub ? 1 : 0).")"]);
        return is_subclass_of(static::_getPropertyType_asString($propertyName), $cfdName, true);
    }

    //    private static function _isArgumentAnObject($suspiciousValue): bool {
    //        // this is an object, maybe we can upconvert it later?
    //        $typeOfPassedValue = gettype($suspiciousValue);
    //        if ($typeOfPassedValue == 'object') {
    //            return true; //$typeOfPassedValue = get_class($suspiciousValue);
    //        } else {
    //            return false;
    //        }
    //    }

    //    private static function _isArgumentACfd($suspiciousValue) {
    //        return static::_isArgumentAnObject() && is_subclass_of($suspiciousValue, static::class);
    //    }
    private static function _getArgumentType($unsafeValue): string {
        $typeOfPassedValue = gettype($unsafeValue);
        if ($typeOfPassedValue == 'object') {
            return get_class($unsafeValue);
        } else {
            return $typeOfPassedValue;
        }
    }

    private static function _isArgument_havingNonDirectConversionPathToPropertyType(string $propertyName, $suspiciousValue):bool {
        assert(static::_doesPropertyExist($propertyName));
        assert(static::_isExistingPropertyManaged($propertyName));
        if (static::_isArgumentType_sameAsPropertyType($propertyName, $suspiciousValue)) {
            return false; // Its directly convertable via simple assigned, which is distinct from NonDirectConversion
        }

        if (!static::_isPropertyCfd($propertyName) ) {
            print_r([__FILE__,__LINE__,"$propertyName is not a cfd"]);
            return false;  // we can only convert stuff to Cfds
        }

        try {
            $propertyType_asString = static::_getPropertyType_asString($propertyName);
            #print_r([__FILE__,__LINE__,"about to $propertyType_asString($suspiciousValue)"]);
            $convertedValue = $value = new $propertyType_asString($suspiciousValue);
            // FYI: Throws a \TypeError if still not the right type
             return true;
        } catch ( CfdErrorValidation $e) {
            // I got here, so it converted (but didn't validate). We don't care yet if it doesn't validate
            return true;
        } catch (int $e) {
            // I got here, which means compiler couldn't figure out how to upconvert
            return false;
        }
    }



    //    // ------- Ensure... -----
    //    private static function _ensurePropertyExists(string $propertyName): void {
    //        if (!static::_doesPropertyExist($propertyName)) {
    //            throw new CfdErrorExistance();
    //        }
    //    }
    //    private static function _ensurePropertyIsManaged(string $propertyName): ?CfdErrorUnmanaged {
    //        if (!static::_isExistingPropertyManaged($propertyName)) {
    //            throw new CfdErrorUnmanaged();
    //        }
    //    }
    //
    //    private static function _ensureExistingArguement_directlyMatchPropertyType(string $propertyName, $suspiciousValue): ?CfdErrorType {
    //        if (!static::_doesExistingArgument_directlyMatchPropertyTypeOrIsCfd($propertyName,$suspiciousValue)) {
    //            throw new CfdErrorUnmanaged();
    //        }
    //    }
    //
    //    private static function _doesExistingArgument_convertToPropertyType(string $propertyName, $argumentKnownTBeAnObject): ?CfdErrorUnconvertable {
    //
    //    }

    //    private static function _ensureArgumentObject_convertToPropertyType(string $propertyName, $argumentKnownTBeAnObject): ?CfdErrorUnconvertable {
    //        $typeOfPassedValue = gettype($argumentKnownTBeAnObject);
    //        assert($typeOfPassedValue == 'object',[__FILE__,__LINE__]);
    //        $thisReflector = new \ReflectionClass(static::class);
    //        $typeOfNamedProperty = $thisReflector->getProperty($propertyName)->getType().''; // testworld\ALuckyNumber|string
    //        try {
    //            $convertedValue = $value = new $typeOfNamedProperty(
    //                $argumentKnownTBeAnObject
    //            ); // FYI: Throws a \TypeError if still not the right type
    //        } catch ( CfdErrorValidation $e) {
    //            // I got here, so it converted (but didn't validate). We don't care yet if it doesn't validate
    //            return null;
    //        } catch (int $e) {
    //            // I got here, which means compiler couldn't figure out how to upconvert
    //            return new CfdErrorUnconvertable();
    //        }
    //    }

    private static function _ensureArgumentValidate(string $propertyName, $unsafeValue): ?CfdErrorValidation {
        $thisReflector = new \ReflectionClass(static::class);
        $typeOfNamedProperty = $thisReflector->getProperty($propertyName)->getType() . '';
        // testworld\ALuckyNumber|string
        try {
            $convertedValue = $value = new $typeOfNamedProperty($unsafeValue);
            // FYI: Throws a \TypeError if still not the right type
            return null;
        } catch (CfdErrorValidation $e) {
            // I got here, so it converted (but didn't validate)
            return $e;
        }
    }

    public static function preValidatePropertyValues(array $asrPropertyValues): DtoValid {
        foreach ($asrPropertyValues as $propertyName=>$propertyValue) {
            $dtoValid = static::preValidatePropertyValue($propertyName,$propertyValue);
            if (!$dtoValid->isValid) {
                return $dtoValid;
            }
        }
        return new DtoValid(isValid:true);
    }
    public static function preValidatePropertyValue(string $propertyName, $unsafeValue): DtoValid {
        if (!static::_doesPropertyExist($propertyName)
            ||
            !static::_isExistingPropertyManaged($propertyName)
            ||
            !(
                static::_isArgumentType_sameAsPropertyType($propertyName, $unsafeValue)
                ||
                static::_isArgument_havingNonDirectConversionPathToPropertyType($propertyName, $unsafeValue)
            )
        ) {
            throw new \Exception(static::class."::$propertyName Hacking or Logic Error - please catch earlier ");
        }

        try {
            $enumReason = '';
            if (static::_isArgumentType_sameAsPropertyType($propertyName, $unsafeValue)) {
                #$v = $unsafeValue;
                #$enumReason = 'sameAsPropertyType';
                $reflectionClass = new \ReflectionClass(static::class);
                $asrPublicProperties = $reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC + !\ReflectionProperty::IS_STATIC);

                if (count($asrPublicProperties) == 1) {
                    $meName = static::class;
                    $v = new $meName($unsafeValue);
                }
            } elseif (static::_isArgument_havingNonDirectConversionPathToPropertyType($propertyName, $unsafeValue)) {
                $propertyCfdType_asString = static::_getPropertyType_asString($propertyName).'';
                $v = new $propertyCfdType_asString($unsafeValue);
            } else {
                return new DtoValid(isValid:false, enumReason:'CfdErrorUnconvertable', message:"propertyName($propertyName) unsafeValue($unsafeValue)");
            }

            return new DtoValid(isValid:true, enumReason:$enumReason, message:"propertyName($propertyName)");

        } catch (CfdErrorValidation $e) {

            return new DtoValid(isValid:false, enumReason:'CfdErrorValidation',message:$e->getMessage());
        }

//I'm getting into circular logic.  There might be something here about lone values
//but validation now happens during construction....
//and in test 510 - there is just one property, which is throwing me off
//so, how to pre-validate???
//
//1) if just one property, then try
//2) ...
        #try... if something fails above, it is serious and not just preValidating related.  It legit bug.
    }

    public static function newViaAsr(array $unsafeValues) : self {
        $meName = get_called_class();
        // if you see Error: Unknown named parameter $Slug here, then I bet you have class properties instead of properties as part of the constructor

        try {
            return new $meName(...$unsafeValues);
        } catch (\TypeError $e) {
            // see if these can be upconverted
            foreach ($unsafeValues as $propertyName=>$unsafeValue) {

                if (static::_isArgumentType_sameAsPropertyType($propertyName, $unsafeValue)) {
                     $values[$propertyName] = $unsafeValue;
                } elseif (static::_isArgument_havingNonDirectConversionPathToPropertyType($propertyName, $unsafeValue)) {
                    $propertyCfdType_asString = static::_getPropertyType_asString($propertyName).'';
                    $values[$propertyName] = new $propertyCfdType_asString($unsafeValue);
                } else {
                    $argumentType = static::_getArgumentType($unsafeValue);
                    $propertyType_asString = static::_getPropertyType_asString($propertyName);
                    throw new CfdErrorUnconvertable(static::class."::$propertyName($propertyType_asString) -->$argumentType ");
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



