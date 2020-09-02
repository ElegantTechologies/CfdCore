<?php

namespace SchoolTwist\Cfd\Lib;
trait property_EnumValue_Validates
{
    public  static array $_ArrEnumValuePossibilities;


    public static function EnumValue_Validates($maybeValidItem): \SchoolTwist\Validations\Returns\DtoValid {

        try {

            $hasPropertyThere = in_array($maybeValidItem, static::$_ArrEnumValuePossibilities) ? 1 : 0;
            #dd([$maybeValidItem, static::$_ArrEnumValuePossibilities, $hasPropertyThere]);
           if ($hasPropertyThere){
                return new \SchoolTwist\Validations\Returns\DtoValid(['isValid' => true]);
           } else {
               return new \SchoolTwist\Validations\Returns\DtoValid(['isValid' => false, 'enumReason' => 'NotReal', 'message' => "'$maybeValidItem' is not valid enum from list : " . implode(', ', static::$_ArrEnumValuePossibilities)]);
           }
        } catch (\Exception $exception) {
            $meClass = get_called_class();
            throw new \Exception("$meClass::_ArrEnumValuePossibilities is not there, seemingly even though it is an enum ". __FILE__.__LINE__,-87687545);
        }
        //        #$asr_AsrRichProperties = static::getRichProperties();
        //        #$asrRichProperties = $asr_AsrRichProperties['properties'];
        //
        //        #if (!isset($asrRichProperties['_ArrEnumValuePossibilities']) || !($asrRichProperties['_ArrEnumValuePossibilities']['hasStaticDefault'])) {
        //            $thisClassName = get_called_class();
        //            $reflectionClass = new \ReflectionClass($thisClassName);
        //            $asrStaticProperties = $reflectionClass->getProperties();
        //
        //            $arrStaticPropertyName = array_column($asrStaticProperties, 'name');
        //            $csvStaticPropertyName = implode(', ', $arrStaticPropertyName);
        //            $hasPropertyThere = in_array('_ArrEnumValuePossibilities', $arrStaticPropertyName);
        //            if (!$hasPropertyThere) {
        //                return new \SchoolTwist\Validations\Returns\DtoValid(['isValid'=>false, 'enumReason'=>'_ArrEnumValuePossibilities_notExisting','message'=>"This is an CfdEnumValue, so you must set \$_ArrEnumValuePossibilities as 'public \$_ArrEnumValuePossibilities = [];' with an array of allowed values. The current properties set in  '$thisClassName' are $csvStaticPropertyName"]);
        //                #throw CfdError::doesHave_NotFalseFor_AccompanyingProperty("This is an CfdEnumValue, so you must set \$_ArrEnumValuePossibilities as 'public \$_ArrEnumValuePossibilities = [];' with an array of allowed values. The current properties set in  '$thisClassName' are $csvStaticPropertyName");
        //            } else {
        //                // state: _ArrEnumValuePossibilities is set
        //
        //
        //                $hasEnumValuesSet = isset($thisClassName::$_ArrEnumValuePossibilities);
        //                if (!$hasEnumValuesSet) {
        //                    return new \SchoolTwist\Validations\Returns\DtoValid(['isValid' => false, 'enumReason' => '_ArrEnumValuePossibilities_notSet', 'message' => "This is an CfdEnumValue($thisClassName), so you must set \$_ArrEnumValuePossibilities to an array, like 'public \$_ArrEnumValuePossibilities = [];' with an array of allowed values. "]);
        //                }
        //
        //                $isListOfPossibleEnumValuesActuallyAnArray = is_array($thisClassName::$_ArrEnumValuePossibilities);
        //                if (!$isListOfPossibleEnumValuesActuallyAnArray) {
        //                    $setToX =  $thisClassName::$_ArrEnumValuePossibilities;
        //                    $setToType = gettype($setToX);
        //                    return new \SchoolTwist\Validations\Returns\DtoValid(['isValid' => false, 'enumReason' => '_ArrEnumValuePossibilities_notSetToBeAnArray', 'message' => "This is an CfdEnumValue ($thisClassName), so you must set \$_ArrEnumValuePossibilities to an array, like 'public \$_ArrEnumValuePossibilities = [];' with an array of allowed values. It is set to '$setToX' which is of type '$setToType', but it instead needs to be set to be an array."]);
        //                }
        //            }
        //        #}
        //
        //
        //        if (!in_array($maybeValidItem, $asrRichProperties['_ArrEnumValuePossibilities']['staticDefault'])) {
        //            return new \SchoolTwist\Validations\Returns\DtoValid(['isValid' => false, 'enumReason' => 'NotReal', 'message' => "'$maybeValidItem' is not valid enum from list : " . implode(', ', $asrRichProperties['_ArrEnumValuePossibilities']['staticDefault'])]);
        //        }
        //        return new \SchoolTwist\Validations\Returns\DtoValid(['isValid' => true]);
    }
}
