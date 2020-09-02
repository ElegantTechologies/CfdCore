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
            throw new \ElegantTechnologies\Cfd\Core\ErrorFromCfd("$value is not an even number");
        }
        parent::__construct();
    }
}

*/

class Vto implements \ElegantTechnologies\Validations\Contracts\ArrayableShallow
{
    private $_value; #our little hack to keep others from updating $that->value
    private $_wasParentCalled = false;
    private $_wrappedValues = [];
    public function __construct() {
        // make all public properties now be private to avoid accidental access
        // Helps ensure it can't be changed externally.  Unsetting it lets __get get invoked. Getter/Setter Hack.
        $reflectionClass = new \ReflectionClass($this::class);
        $asrPublicProperties = $reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($asrPublicProperties as $asrPublicProperty) {
            $propertyName = $asrPublicProperty->name;
            $this->_wrappedValues[$propertyName] = $this->$propertyName;
            unset($this->$propertyName);
        }

        $this->_wasParentCalled = true; // CAUTION: It is pointless to check this via __get or __set cause they'd only be called if the property got unset

        // ----- Cfv --------
        // Cfv (vs. Cfd) has one, and only one, property. It is called 'value'
        if (count($asrPublicProperties) != 1 || $asrPublicProperties[0]->name != 'value') {
            $currentName = $asrPublicProperties[0]->name;
            $meName = $this::class;
            throw new ErrorFromCfd(
                "This is a Vto, so '$meName::$currentName' must be renamed to '$meName::value'.  Vto can only have one, and only property. It must be named 'value'"
            );
        }

    }

    #https://ocramius.github.io/blog/intercepting-public-property-access-in-php/
    public function __get($name)
    {
        if (!array_key_exists($name,$this->_wrappedValues)) {
            $csvPropertyNames = implode(', ',array_keys($this->_wrappedValues));
            throw new ErrorFromCfd("$name is not a public property of ".$this::class. "::[$csvPropertyNames]");
        }
        return $this->_wrappedValues[$name];
    }

    public function __set($name, $value)
    {
        $meName = $this::class;
        throw new ErrorFromCfd("$meName is a Cfd/Cfv, so you can not update $meName::$name. $meName->$name is read only, so you can do \$o = new $meName(x); \$v = \$o->$name, but not \$o->$name = x2;");
    }

    private function _ensureInited() {
        if (!$this->_wasParentCalled) {
            throw new ErrorFromCfd($this::class." did not get it's construction called.");
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

}



