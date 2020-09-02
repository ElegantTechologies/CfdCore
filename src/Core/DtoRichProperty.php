<?php
namespace SchoolTwist\Cfd\Core;

final class DtoRichProperty {

    public function __construct(
        public string $name,

        public bool $isManaged,
        public bool $isTypeEnforced,
        public bool $getsValidated,
        public ?string $getsValidatedByMethodName,

        public bool $hasDefault,
        public $default = null,

        public array $types, // 'null' as string might be in here.

        public bool $isStatic,

        public bool $isPublic,
        public bool $isProtected,
        public bool $isPrivate,

        // Helpers
        public bool $isNullAnAllowedType,
        public bool $isValueRequiredAtCreation,

        array $asrValues
) {}