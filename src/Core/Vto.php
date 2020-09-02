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

class Vto #implements \ElegantTechnologies\Validations\Contracts\ArrayableShallow
{
    private $_value; #our little hack to keep others from updating $that->value
    private $_wasParentCalled = false;
    public function __construct() {
        #hint: $value would need to have the same type as child per compiler
        // Ensure only one public property
        $reflectionClass = new \ReflectionClass($this::class);
        $asrPublicProperties = $reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC);
        if (count($asrPublicProperties) != 1) {
            $numPublicProperties = count($asrPublicProperties);
            throw new ErrorFromCfd("There are '$numPublicProperties' public properties.  Vto can only have one and it must be named 'value'");
        }

        // Ensure it is named 'value'
        if ($asrPublicProperties[0]->name != 'value') {
            $currentName = $asrPublicProperties[0]->name;
            $meName = $this::class;
            throw new ErrorFromCfd(
                "This is a Vto, so '$meName::$currentName' must be renamed to '$meName::value'.  Vto can only have one and it must be named 'value'"
            );
        }

        // Helps ensure it can't be changed externally.  Unsetting it lets __get get invoked
        $this->_value = $this->value;
        unset($this->value);
        $this->_wasParentCalled = true;

    }

    #https://ocramius.github.io/blog/intercepting-public-property-access-in-php/
    public function __get($name)
    {
        #$this->_ensureInited(); this won't work cuz __get won't get called if  unset($this->value); wasn't hit on __co.

        if ($name != 'value') {
            throw new ErrorFromCfd("$name is not a public property of ".$this::class. ". This is a Vto, so just 'value' is allowed.");
        }

        return $this->_value;
    }

    public function __set($name, $value)
    {
        if ($name != 'value') {
            throw new ErrorFromCfd("$name is not a public property of $meName. This is a Vto, so just 'value' is allowed. And updating is not allowed, just so you know.");
        }
        $meName = $this::class;
        throw new ErrorFromCfd("$meName is a Vto, so you can not update $meName::$name. $meName->$name is read only, so you can do \$o = new $meName(x); \$v = \$o->$name, but not \$o->$name = x2;");
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
        return ['value'=>$this->_value];

    }

}



