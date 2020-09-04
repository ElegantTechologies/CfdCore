<?php
erase me
namespace SchoolTwist\Cfd\Core;


class CfdRichPropertyOBE_seeDtoRichProperty extends CfdBase // hmm, feels like this should be a Dto
{
    public string $name;
    public bool $isManaged;
    public bool $isTypeEnforced;
    public bool $getsValidated;
    public ?string $getsValidatedByMethodName;

    public bool $hasDefault;
    public $default = null;

    public array $types; // 'null' as string might be in here.

    public bool $isStatic;

    public bool $isPublic;
    public bool $isProtected;
    public bool $isPrivate;

    // Helpers
    public bool $isNullAnAllowedType;
    public bool $isValueRequiredAtCreation;
}