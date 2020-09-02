<?php
declare(strict_types=1);
namespace SchoolTwist\Cfd\Lib;


abstract class CfdEnumValues extends \SchoolTwist\Cfd\Core\CfdBase
{
    /** @var array */
    public array $EnumValues;

    use property_EnumValue_Validates;

    public static function EnumValues_Validates(array $maybeValidItems): \SchoolTwist\Validations\Returns\DtoValid
    {

        foreach ($maybeValidItems as $maybeValidItem) {
            $dtoValid = static::EnumValue_Validates($maybeValidItem);
            if ($dtoValid->isValid == false) {
                return $dtoValid;
            }
        }
        return new \SchoolTwist\Validations\Returns\DtoValid(['isValid' => true]);

    }

    public function doesHaveThis($doesHave_singleValue) : bool
    {
         $arrDoesHaves = $this->EnumValues;
        return (in_array($doesHave_singleValue, $arrDoesHaves,true));
    }

    public function doesHaveThese(array $arrMustHaves) : bool
    {
        $arrDoesHaves = $this->EnumValues;
        return (array_intersect($arrMustHaves, $arrDoesHaves) == $arrMustHaves);
        // $array1 is a subset of $array2
        //https://stackoverflow.com/a/12276627/93933
    }

     public function doesHaveOnlyThis($doesHave_singleValue) : bool
    {
         $arrDoesHaves = $this->EnumValues;
        return (count($arrDoesHaves) == 1 && $this->doesHaveThis($doesHave_singleValue));
    }
}
