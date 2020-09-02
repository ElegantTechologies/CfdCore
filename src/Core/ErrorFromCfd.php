<?php
namespace ElegantTechnologies\Cfd\Core;


use TypeError;


class ErrorFromCfd extends TypeError
{
    public static function NotValidating(\SchoolTwist\Validations\Returns\DtoValid $dtoValueValidation, string $property_name, $property_value): ErrorFromCfd
    {
        return new self("{$dtoValueValidation->message}");
    }

    public static function doesHave_MissingFor_AccompanyingProperty(string $prefix, string $property_name, string $needy_proerty_name): ErrorFromCfd
    {
        return new self("Property '$property_name' is missing, even though you have another property called $needy_proerty_name.");
    }

    public static function doesHave_ControlledProperty_notSet(string $msg): ErrorFromCfd
    {
        return new self($msg);
    }

    public static function doesHave_NotFalseFor_AccompanyingProperty(string $msg): ErrorFromCfd
    {
        return new self($msg);
    }

    public static function doesHave_NotTrueFor_AccompanyingProperty(string $msg): ErrorFromCfd
    {
        return new self($msg);
    }

    public static function ControllerNotBool(string $prefix, string $property_name, string $key, array $parameters, string $needy_proerty_name): ErrorFromCfd
    {
        $t = gettype($parameters[$key]);
        $value = $parameters[$key];
        return new self("Your controller field '$needy_proerty_name' must be true or false - nothing else. $t($value)");
    }

    public static function LogicError(string $message): ErrorFromCfd
    {

        return new self("I don't know how to program: $message");
    }
}
