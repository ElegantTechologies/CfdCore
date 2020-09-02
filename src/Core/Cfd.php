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
        $asrPublicProperties = $reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($asrPublicProperties as $asrPublicProperty) {
            $propertyName = $asrPublicProperty->name;
            $this->_wrappedValues[$propertyName] = $this->$propertyName;
            unset($this->$propertyName);
        }

        $this->_wasParentCalled = true; // CAUTION: It is pointless to check this via __get or __set cause they'd only be called if the property got unset


            }
    public function __get($name)
    {
        if (!array_key_exists($name,$this->_wrappedValues)) {
            $csvPropertyNames = implode(', ',array_keys($this->_wrappedValues));
            throw new CfdError("$name is not a public property of ".$this::class. "::[$csvPropertyNames]");
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


}



