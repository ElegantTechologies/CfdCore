<?php
declare(strict_types=1);
namespace ElegantTechnologies\Cfd\Core;

#https://ocramius.github.io/blog/intercepting-public-property-access-in-php/
use TypeError;
use ReflectionClass;
use ReflectionProperty;

/*
 * Do lightly-rich typing and validation
 * Spec:
 *  All public properties, and only public properties, are managed.
 *  At creation, all managed properties must be set, unless there is a specified default.
 *  
 *  If a property type(s) is specified, then the type is enforced when set
 *  Type mismatches, and missing properties, through exceptions.
 *  Value validation is checked via public function propertyName_Validates($blah) : DtoValid
 *
 * class CfdPerson extends CfdBase {
 *   Known Limitation (php limitation). If NULL is an option and you don't specify a default, then it defaults to null.
 *   private $i;        // this is not public - we totally ignore this.
 *   protected $j;   // this is not public - we totally ignore this.
 *   public $requiredButNotTyped; // must be set, but we won't type enforce
 *   public $notRequiredAndNotTyped = 0; // optionally set, but we won't type enforce
 *   public $payloadIfNotNull = null;    // optionally set, but we won't type enforce.  Note:
 *   public string $name; // Must always be specified
 *
 *   public ?int $age = null; // We might, or might not, know the age, and we'll assume that we don't know it
 *   public $numEyes = 2; // We'll assume two eyes, unless otherwise specified.  Never null.
 *   public bool $likesIceCream = true;
 *   public $payload = null; // Not type enforced, but required;
 *   protected $stuff; // not managed at all, but will show up in the list of properties
 *   private $_canNotSeeMe;
 *   public static $x; // tis ok.  treated like public $x. Not sure use case, but wanted PHP consistency.
 *
 *   public static function numEyes_Validates($wellTypedButOtherwiseUntrustedValue) : DtoValid {
 *      $isLegitRange = $wellTypedButOtherwiseUntrustedValue => 0 && $wellTypedButOtherwiseUntrustedValue <= 2;
 *      return new DtoValid(['isValid'=>$isLegitRange]);
 *   }
 *  }
 *
 * class CfdAdult extends CfdPerson {
 *  public static function name_Validates($wellTypedButOtherwiseUntrustedValue) : DtoValid{
 *    $isLegitRange = $wellTypedButOtherwiseUntrustedValue >= 18;
 *    return new DtoValid(['isValid'=>$isLegitRange]);
 *  }
 * }
 * $maddie = new CfdAdult(['name'=>'Maddie', 'age'=>13]);// Throws exception.
 * $jj = new CfdAdult(['name'=>'JJ', 'age'=>49]);
 */

abstract class CfdBase implements \SchoolTwist\Validations\Contracts\ArrayableDeep, \SchoolTwist\Validations\Contracts\ArrayableShallow
{
    #public CONST NullIsDefault = 'UseThisWhenDefaultTypeShouldBeNull_InsteadOf public $blah = null; cus CuzPhpCanNotSeemToDetectNullAsADefault. Seriously.';

    public CONST ALLOW_UPCONVERT_TYPES = ['integer','string'];// OVERRIDE ME IF YOU DON"T WANT THESE - I'm not positive of this 8/20'
    public const FORBIDDEN_TYPES = [ ]; // mainly to avoid confusion. Actually - bool is 'true'. Boolean is just an alias: https://stackoverflow.com/questions/44009037/php-bool-vs-boolean-type-hinting
    protected const  META_PREFIXES = ['doesHave'];
    #protected const  META_PROPERTY_NAMES = ['_ArrEnumValuePossibilities', 'fieldOrder'];
    protected const  META_PROPERTY_NAMES = [
        '_ArrEnumValuePossibilities',
    ];

    public static function deriveShortNameFromCfdClassName() : string
    {
        $fqn = get_called_class();
        return static::deriveShortNameFromCfdClassName_givenFqn($fqn);
    }

    public static function deriveShortNameFromCfdClassName_givenFqn(string $fqn) : string
    {
         $arrFqn = explode('\\',$fqn);
         $shortTableName = array_pop($arrFqn);
         $Words_withoutHungarian = \EtStringConvert::camelCaseToEnglish($shortTableName, ['dto','Cfd','cfdb','cfe','cftbl','tbl']);
         $shortTableName = implode(explode(' ', $Words_withoutHungarian));
         return $shortTableName;
    }


    private static function StartsWith(string $prefix, string $longString): bool
    { //https://www.geeksforgeeks.org/php-startswith-and-endswith-functions/
        $len = mb_strlen($prefix);
        return (mb_substr($longString, 0, $len) === $prefix);
    }

    private static function TrimOffFront(string $prefix, string $longString): string
    {
        $len = mb_strlen($prefix);
        return mb_substr($longString, $len);
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
    if (in_array($selected,Cfd_Base::arrDto_column($arrTabs,'Slug')) {
        ....
    */
    public static function arrDto_column(array $dtoThatIsAnArray, $subKey): array
    { // inspired by php's array_column

        $arr = array_map(function (CfdBase $Dto) use ($subKey) {
            if (!isset($Dto->$subKey)) {
                $class_vars = get_class_vars(get_class($Dto));
                $arrPrettyClassVarNames = [];
                foreach ($class_vars as $name => $value) {
                    $arrKnownMeta = ['exceptKeys', 'onlyKeys']; // expand later, if needed
                    if (!in_array($name, $arrKnownMeta)) {
                        $arrPrettyClassVarNames[] = $name;
                    }
                }
                $strPrettyClassVarName = implode(', ', $arrPrettyClassVarNames);
                #\EtError::AssertTrue(0, 'error', __FILE__, __LINE__, " subKey($subKey) is not a property of " . get_class($Dto) . ". Try these valid options: $strPrettyClassVarName");
                #assert(0, 'error', __FILE__, __LINE__, " subKey($subKey) is not a property of " . get_class($Dto) . ". Try these valid options: $strPrettyClassVarName");

            }
            return $Dto->$subKey;
        }, $dtoThatIsAnArray);

        return $arr;
    }


    public function getValue_notHave(string $NameOfVariable, $DefaultVarValue_ifVarIsNull)
    {
        if (is_null($this->$NameOfVariable)) {
            return $DefaultVarValue_ifVarIsNull;
        } else {
            return $this->$NameOfVariable;
        }
    }

    public function __construct(array $asrSubmission,  $doAssumeValuesAreValid = false)
    {
        // Quick assign
        if ($doAssumeValuesAreValid) {
            foreach ($asrSubmission as $key=>$val) {
                $this->$key = $val;
            }
            return;
        }

        // Softly test if submission is valid
        $dtoValid = static::preValidateSubmission( $asrSubmission);
        if (!$dtoValid->isValid) {
             throw CfdError::LogicError($dtoValid->enumReason. $dtoValid->message);
        }
        $asrSubmissionNormalized = $dtoValid->newValue;
        unset($asrSubmission);


        $asrRichProperties = static::getRichProperties();
        foreach ($asrSubmissionNormalized as $submittedPropertyName => $submittedValue) {
            $dtoThisProperty = $asrRichProperties['properties'][$submittedPropertyName];
            $allowedTypes = $dtoThisProperty->types;
            $typeOfSubmittedValue = gettype($submittedValue);


            $isFinalType = ($typeOfSubmittedValue == 'object') ? (in_array(get_class($submittedValue), $allowedTypes)) : (in_array($typeOfSubmittedValue, $allowedTypes));
            if ($isFinalType) {
                $effectiveValueToUse = $submittedValue;
                unset($submittedValue);
            } else {
                $isDestinedForCfd = is_a($submittedValue,CfdBase::class, true) ? 1 : 0;
                if (!$isDestinedForCfd) {
                     if ($dtoThisProperty->isTypeEnforced) {
                         assert(
                            0,
                            "typeOfSubmittedValue($typeOfSubmittedValue) isn't one of allowedTypes(" . implode(
                                ",",
                                $allowedTypes
                            ) . ") and it isn't and CFD, so no upconverting was attempted."
                        );
                     } else {
                         $effectiveValueToUse = $submittedValue;
                         unset($submittedValue);
                     }
                } else {
                    $submittedCfdClass = get_class($submittedValue);
                    $couldAndDidUpconvert = false;
                    $upConvertedValue_elseNullIfDone = null;
                    foreach ($dtoThisProperty->types as $anAllowedType) {
                        $dtoValidUpconvert_elseNullIfDone = $anAllowedType::upConvertType_elseNull(
                            $submittedPropertyName,
                            $submittedValue
                        );
                        if ($dtoValidUpconvert_elseNullIfDone && $dtoValidUpconvert_elseNullIfDone->isValid) {
                            $upConvertedValue_elseNullIfDone = $dtoValidUpconvert_elseNullIfDone->newValue;
                            $newType = gettype($upConvertedValue_elseNullIfDone);

                            if ($upConvertedValue_elseNullIfDone) {
                                $submittedValue_old = $submittedValue;
                                $submittedValue = $upConvertedValue_elseNullIfDone;


                                if ($newType == 'object') {
                                    $newType = get_class($upConvertedValue_elseNullIfDone);
                                }
                            }
                        }
                        $couldAndDidUpconvert = true;
                        break;
                    }
                    if ($couldAndDidUpconvert) {
                        $effectiveValueToUse = $upConvertedValue_elseNullIfDone;
                    } else {
                        if ($dtoThisProperty->isTypeEnforced) {

                            $myClassName = get_class($this);
                            $csvAllowedTypes = implode(', ', $dtoThisProperty->types);
                            throw CfdError::LogicError("Could not upconvert $myClassName::$submittedPropertyName from type $submittedCfdClass to one of the allowed types ($csvAllowedTypes)");
                        } else {
                            $effectiveValueToUse = $upConvertedValue_elseNullIfDone;
                        }
                    }

                }
            }


            if ($dtoThisProperty->isStatic) {
                $this::$$submittedPropertyName = $effectiveValueToUse;
            } else {
                $this->$submittedPropertyName = $effectiveValueToUse;
            }

        }

    }


    /* Checks for missing value, extra values, type matching */
    public static function preValidateSubmission(array $asrSubmission): \SchoolTwist\Validations\Returns\DtoValid
    {   /* Remember: All properties must be set to something, even if it is null, unless it has a default
        */
        $asr_RichProperties = static::getCfdRichProperties();

        $asrRichProperties = $asr_RichProperties['properties'];
        $asrSubmission = $asrSubmission;

        // are any of the supplies properties extra?
        $arrExtras = array_diff( array_keys($asrSubmission), array_keys($asrRichProperties));
        if (count($arrExtras) > 0) {
            $propertyNameCsv = implode(', ',$arrExtras);
            return new \SchoolTwist\Validations\Returns\DtoValid(
                            [
                                'isValid' => false,
                                'enumReason' => 'extraProperty',
                                'message' => " Extra submission. ($propertyNameCsv) is not an allowed public property of " . static::class,
                            ]
                        );
        }
        unset($arrExtras);

        // are any of the supplies properties missing?
        $arrMissing = array_diff(  array_keys($asrRichProperties), array_keys($asrSubmission));
                    print_r([__FILE__,__LINE__,$asr_RichProperties]);
        if (count($arrMissing) > 0) {
            foreach ($arrMissing as $missingPropertyIfDefaultIsRequired) {
                if ($asrRichProperties[$missingPropertyIfDefaultIsRequired]->isValueRequiredAtCreation) {

                    return new \SchoolTwist\Validations\Returns\DtoValid(
                        [
                            'isValid' => false,
                            'enumReason' => 'missingProperty',
                            'message' => " Missing submission. ($missingPropertyIfDefaultIsRequired) is missing public property of " . static::class. " and since it does not have a default value, it must be set.",
                        ]
                    );
                }
            }
        }
        unset($arrMissing);

        // are all supplied properties valid?
        $arrPropertiesChecked = [];
        foreach ($asrSubmission as $submittedPropertyName => $proposedValue) {
            $dtoValid = static::preValidateProperty($submittedPropertyName, $proposedValue, null);
            if (!$dtoValid->isValid) {
                return $dtoValid;
            }
            $arrPropertiesChecked[$submittedPropertyName] = $submittedPropertyName;

        }
        unset($submittedPropertyName);

        // Are any required properties missing?
        foreach ($asr_RichProperties['properties'] as $propertyName=>$dtoProperty) {


            if ($dtoProperty->isValueRequiredAtCreation) {
                if (!isset($arrPropertiesChecked[$propertyName])) {
                    if ($dtoProperty->hasDefault) {
                        $asrSubmission[$propertyName] = $dtoProperty->default;
                    } else {
                        #var_export($asr_RichProperty);
                        return new \SchoolTwist\Validations\Returns\DtoValid(
                            [
                                'isValid' => false,
                                'enumReason' => 'missingProperty',
                                'message' => " Missing '$propertyName'  '" . static::class . "'",

                            ]
                        );
                    }
                }

            }
        }



        return new \SchoolTwist\Validations\Returns\DtoValid(['isValid' => true, 'newValue'=>$asrSubmission, 'oldValue'=>$asrSubmission]);
    }

    public
    static function preValidateLoneValue($dangerousValue, ?bool $isRequired_ifSet_otherwise_requireAsConfiggedWhenNull, array $callingRichProperties): \SchoolTwist\Validations\Returns\DtoValid
    {
        $asr_RichProperties = static::getCfdRichProperties();

        if (count($asr_RichProperties['properties']) != 1) {


            return new \SchoolTwist\Validations\Returns\DtoValid(['isValid' => false, 'enumReason' => 'onlyLoneSubValuesAllowed', 'message' => get_called_class() . ": Composed DTOs can only have a single value unless they are subclasses of CFDs, like DtoEmail just has a single value for its email address. " . __LINE__]);
        }
        $propertyName = array_key_first($asr_RichProperties['properties']);
        return static::preValidateProperty($propertyName, $dangerousValue, $isRequired_ifSet_otherwise_requireAsConfiggedWhenNull);
    }


    /* Just to clarify: 8/20'
        null is allowed, when specked to allow null.
        is $mustBeInitializedToNonNull_ifSet_otherwise_requireAsConfiggedWhenNull == true, then it must be non-null.
        When we say it mustBeInitialized, it probably means we don't have a default value.
        so, isRequiredNow = mustBeInitialized || $mustBeInitializedToNonNull_ifSet_otherwise_requireAsConfiggedWhenNull
        Caution: the string '' is considered set.

        Flow Note: since this is preValidateProperty, it is logical to require a value to test.  Specked Default values just aren't relevant here.
    */

    public
    static function preValidateProperty($propertyName, $dangerousValue, ?bool $mustBeInitializedToNonNull_ifSet_otherwise_requireAsConfiggedWhenNull): \SchoolTwist\Validations\Returns\DtoValid
    {
        // Ensure only setting managed properties
        $asrDtoProperties = static::getRichProperties()['properties'];
        if (!isset($asrDtoProperties[$propertyName])) {
            $csvValidPropertyNames = implode(",", array_keys($asrDtoProperties));
            print_r(static::getRichProperties());
            return new \SchoolTwist\Validations\Returns\DtoValid(['isValid' => false, 'enumReason' => 'notARealOption', 'message' => "'$propertyName' is not a valid property for class " . get_called_class().".  ($csvValidPropertyNames) are allowed. Hint: Sometimes you see this when you pass a list instead of associative array"]);
        }

        // - property, but not managed....
        $dtoThisProperty = $asrDtoProperties[$propertyName];
        if (!$dtoThisProperty->isManaged) {
            return new \SchoolTwist\Validations\Returns\DtoValid(['isValid' => false, 'enumReason' => 'passingValuesForNonManagedProperties', 'message' => " " . get_called_class()."->$propertyName is not a public property.  Let's just keep this simple and only initiated the managed properties."]);
        }


        // Ensure is an allowed type, or can upconvert to an allowed type
        if ($dtoThisProperty->isTypeEnforced) {
            $typeOfSubmittedValue = gettype($dangerousValue);

            $setButEmpty = ($typeOfSubmittedValue == 'string') ? (strlen(
                    trim($dangerousValue)
                ) == 0) : 0; //  Careful: empty('0') evaluates to true
            $nonEmpty = !$setButEmpty;

            $doesMatchYet = $doesTypeMatchDirectly = $doesTypeMatchViaObject = $doesTypeMatchViaUpConverting = false;

            // -- Matches directly -- $doesTypeMatchDirectly?
            $doesTypeMatchDirectly = $doesMatchYet = in_array($typeOfSubmittedValue, $dtoThisProperty->types);

            // -- Matches via inheritence -- $doesTypeMatchViaObject?
            if (!$doesMatchYet) {
                if ($typeOfSubmittedValue == 'object') {
                    $typeOfSubmittedValue = get_class($dangerousValue);
                    foreach ($dtoThisProperty->types as $typeItShouldBeOrdAncestorOfThis_Name) {
                        if (is_a($dangerousValue, $typeItShouldBeOrdAncestorOfThis_Name)) {
                            $doesTypeMatchViaObject = $doesMatchYet = true;
                            break;
                        } elseif (class_implements($dangerousValue, $typeItShouldBeOrdAncestorOfThis_Name)) {
                            $doesTypeMatchViaObject = $doesMatchYet = true;
                            break;
                        }
                    }
                }
            }

            // If not matching yet - try upconverting -- $doesTypeMatchViaUpConverting
            if (!$doesMatchYet) {
                $nameOfCfdBase = CfdBase::class;
                $isDestinedForCfd = is_a($typeItShouldBeOrdAncestorOfThis_Name, $nameOfCfdBase, true) ? 1 : 0;
                if (!$isDestinedForCfd) {
                    $doesTypeMatchViaUpConverting = false;
                } else {
                    $dtoValidIfConversionAllowed_elseNullNotAttempted = $typeItShouldBeOrdAncestorOfThis_Name::upConvertType_elseNull(
                        $propertyName,
                        $dangerousValue,
                        $mustBeInitializedToNonNull_ifSet_otherwise_requireAsConfiggedWhenNull
                    );
                    $typeOfSubmittedValue_old = $typeOfSubmittedValue;

                    if (!$dtoValidIfConversionAllowed_elseNullNotAttempted) {
                        $doesTypeMatchViaUpConverting = false;
                        return new \SchoolTwist\Validations\Returns\DtoValid(['isValid' => false, 'enumReason' => 'wrongTypeF', 'message' => " " . get_called_class()."->$propertyName is type-enforced to (".implode(', ',$dtoThisProperty->types).") but passed something of type '$typeOfSubmittedValue'"]);
                    } elseif (!$dtoValidIfConversionAllowed_elseNullNotAttempted->isValid) {
                        // I should be able to upconvert, but there was a validation error - like bad data, not bad typing
                        return $dtoValidIfConversionAllowed_elseNullNotAttempted;
                    } elseif ($dtoValidIfConversionAllowed_elseNullNotAttempted->isValid) {
                        $newItem = $dtoValidIfConversionAllowed_elseNullNotAttempted->newValue;

                        // Update typing info/logic
                        $typeOfSubmittedValue_upconverted = gettype($newItem);
                        $typeOfSubmittedValue_upconverted = ($typeOfSubmittedValue_upconverted == 'object') ? get_class(
                            $newItem
                        ) : $typeOfSubmittedValue;

                        $doesTypeMatchViaUpConverting = ($typeOfSubmittedValue_upconverted == $typeItShouldBeOrdAncestorOfThis_Name) ? 1 : 0;
                        if (!$doesTypeMatchViaUpConverting && (gettype($newItem) == 'object')) {
                            $doesTypeMatchViaUpConverting_viaAncestry = is_a(
                                $newItem,
                                $typeOfSubmittedValue_upconverted
                            ) ? 1 : 0;
                            // Look above at in_array.  If we really start having multiple allowed types, this will have to match
                        }
                        $dangerousValue = $newItem;
                    } else {
                        throw new \Exception('logic error');
                    }
                }
            }
        }

        // Ensure Validates well
        if ($dtoThisProperty->getsValidated) {
            // ensure validates by custom function, if there.  Not sure if at top or bottom, or both
            $validatingMethodName = $dtoThisProperty->getsValidatedByMethodName;
            if ($nonEmpty && method_exists(static::class, $validatingMethodName)) {
                $DtoValidation = static::$validatingMethodName($dangerousValue);
                $meClass = get_called_class();
                $static_class = static::class;
                $dtoValid_zo = $DtoValidation->isValid ? 1 : 0;

                if (!$DtoValidation->isValid) {
                    return $DtoValidation;
                }
            }
        }


        return new \SchoolTwist\Validations\Returns\DtoValid(['isValid' => true, 'enumReason' => 'noFailures', 'newValue'=>$dangerousValue, 'oldValue'=>$dangerousValue]);

    }


    public static function upConvertType_elseNull(string $propertyName, $dangerousValue) : ?\SchoolTwist\Validations\Returns\DtoValid
    {
        // fun fact: you are probably now in a different subtype than you were a step ago - don't get confused
        // You can override me if desired
        // Note: if null, it means we are not even going to try to upconvert.
        //  otherwise, we get a DtoValid.  If it is valid, newValue will contain the upconverted type
        $incomingType = gettype($dangerousValue);
        if (!in_array($incomingType,static::ALLOW_UPCONVERT_TYPES)){
            return null;
        }
        if ($incomingType == 'integer' || $incomingType == 'string') {
            $childType = get_called_class();
            $dtoValid = $childType::preValidateProperty('Value',$dangerousValue, null);
            if (!$dtoValid->isValid) {
                return $dtoValid;
            } else {
                $newCfd = new $childType(['Value' => $dangerousValue]);
                $dtoValid =  new \SchoolTwist\Validations\Returns\DtoValid(['isValid'=>true,'enumReason'=>'itPassed', 'newValue'=>$newCfd]);
                return $dtoValid;
            }
        } else {
            return null;
        }
    }


    public function getTerseRichCfdProperties_withValues() : array {
        $asrRichPropreties = static::getRichProperties();
        $asrCfdProperties = [];
        foreach ($asrRichPropreties['properties'] as $propertyName=>$asrRichProprety) {
            if ($asrRichProprety['isEnforced']) {
                $asrCfdProperties[$propertyName] = [
                    'shortPropertyName '=> $propertyName,
                    'isNull'=> is_null($this->$propertyName),
                    'Value'=> $this->$propertyName
                    ];
            }
        }
        return $asrCfdProperties;
    }


    public
    static function getCfdRichProperties(): array
    {
        $asrRichPropreties =  static::getRichProperties_ofDtoNamed(static::class);
        //        foreach ($asrRichPropreties['properties'] as $propertyName=>$asrRichProprety) {
        //            if ($asrRichProprety->isEnforced) {
        //            } else {
        //                unset($asrRichPropreties['properties'][$propertyName]);
        //                // Meta is probably wrong now
        //            }
        //        }

        return $asrRichPropreties;
    }



    public static function getCfdRichProperties_filteredAndUpRequired(array $arrOnlyKeepTheseProperties, array $arrPropertyNames_upForcingToRequired): array
    {
        $DtoCfdSingleFormName = get_called_class();
        $asrBlade['DtoCfdSingleFormName'] = $DtoCfdSingleFormName;

        // Put together data
        $arrNamesForcedToBeRequired_evenIfNotRequiredByDefault = $arrPropertyNames_upForcingToRequired;
        $DtoCfdClassName = $DtoCfdSingleFormName;
        $richProperties = $DtoCfdClassName::getCfdRichProperties($DtoCfdClassName);


        foreach ($arrNamesForcedToBeRequired_evenIfNotRequiredByDefault as $nameToForceStatusToRequired) {
            $richProperties['properties'][$nameToForceStatusToRequired]['mustBeInitialized'] = 1;
        }
        $asrAsrRichProperties = $richProperties;


        $orderedButMissingFields = array_diff($arrOnlyKeepTheseProperties, array_keys($asrAsrRichProperties['properties']));
        $skippedFields = array_diff(array_keys($asrAsrRichProperties['properties']), $arrOnlyKeepTheseProperties);
        // OBE: Not All Fields automatically get presented to user $fieldOrder = array_merge($fieldOrder, $unorderedFields);


        $asrBlade['effectiveProperties'] = $asrAsrRichProperties;//$DtoCfdSingleFormName::getRichProperties();
        // Nix the non-user properties - note: This file needs to merge with Auth/forSingleCfd... 1/20'
        foreach ($asrBlade['effectiveProperties']['properties'] as $propertyName => $effectiveProperty) {
            #print "<br>--- Checking on propertyName($propertyName) to see if in field order";
            if (!(in_array($propertyName, $arrOnlyKeepTheseProperties))) {
                unset($asrBlade['effectiveProperties']['properties'][$propertyName]);
                #print "<br> - MISS:  About to unset $propertyName";
            } else {
                #print "<br> - HIT:   $propertyName is a legit property";
            }
        }
        $asrCfdRichProperty = [];

        foreach ($asrBlade['effectiveProperties']['properties'] as $asrRichProperty) {
            $cfdCfdRichProperty = static::asrRichProperty2CfdRichProperty($asrRichProperty);
            $asrCfdRichProperty[$cfdCfdRichProperty->name] = $cfdCfdRichProperty;
        }
        return $asrCfdRichProperty;
    }


    public static function asrRichProperty2CfdRichProperty($asr): \SchoolTwist\Cfd\Core\CfdRichProperty {


        return new \SchoolTwist\Cfd\Core\CfdRichProperty([
            'isStatic' => $asr['isStatic'] ? true : false,
            'isMeta' => $asr['isMeta'] ? true : false,
            'name' => $asr['name'],
            #'hasStaticDefault' => $asr['hasStaticDefault'] ? true : false,
            #'hasADocComment' => $asr['hasADocComment'] ? true : false,
            #'docCommentForThisProperty' => $asr['docCommentForThisProperty'],
            'isEnforced' => $asr['isEnforced'] ? true : false,
            'isNullAnAllowedType' => $asr['isNullAnAllowedType'] ? true : false,
            'mustBeInitialized' => $asr['mustBeInitialized'] ? true : false,
            'type' => $asr['type'],
            'types' => $asr['types'],
            'hasDefault' =>$asr['hasDefault'],
            'default' =>$asr['default'],
            ]);
    }

    private static ?array $_asrRichProperties = null;
    public
    static function getRichProperties(): array
    {
        $rootClassName = get_called_class();
        if (!$rootClassName::$_asrRichProperties) {
            $rootClassName::$_asrRichProperties = static::getRichProperties_ofDtoNamed(get_called_class());
        }
        return static::getRichProperties_ofDtoNamed(get_called_class());
    }

    private
    static function getRichProperties_ofDtoNamed(string $CfdClassName): array
    {

        $reflectionClass = new ReflectionClass($CfdClassName);

        $asrDefaultProperties = $reflectionClass->getDefaultProperties();
        print_r([__FILE__,__LINE__,$asrDefaultProperties]);

        $richProperties = [];

        $numNonMeta = 0;
        foreach ($reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
            $asrThisProperty = [];

            $propertyName = $reflectionProperty->getName();
            $asrThisProperty['name'] = $propertyName;

            $asrThisProperty['isStatic'] = $reflectionClass->getProperty($propertyName)->isStatic();
            $asrThisProperty['hasDefault'] = is_null(get_called_class()::$$propertyName) || isset($asrDefaultProperties[$propertyName]); // Note: look at getDefaultProperties. It returns an asr of all properties and their defualt value, or NULL if no default is set.  This breaks when the default is actually NULL.
            $asrThisProperty['default'] = ($asrThisProperty['hasDefault']) ? $asrDefaultProperties[$propertyName] : 'no default set';
            #assert($asrThisProperty['hasDefault'] || $asrDefaultProperties[$propertyName])

            $asrThisProperty['isPublic'] = $reflectionProperty->isPublic();
            $asrThisProperty['isProtected'] = $reflectionProperty->isProtected();
            $asrThisProperty['isPrivate'] = $reflectionProperty->isProtected();

            $asrThisProperty['isManaged'] =  $asrThisProperty['isPublic'];
            $asrThisProperty['isTypeEnforced'] = $reflectionProperty->hasType() && $asrThisProperty['isManaged'];
            $asrThisProperty['isValueRequiredAtCreation'] =  $asrThisProperty['isPublic'] && !$asrThisProperty['hasDefault'];


            $validatingMethodName = "{$propertyName}_Validates";
            $asrThisProperty['getsValidated'] = method_exists(static::class, $validatingMethodName);
            $asrThisProperty['getsValidatedByMethodName'] = $asrThisProperty['getsValidated'] ? $validatingMethodName : null;

            if (!$asrThisProperty['isTypeEnforced']) {
                $asrThisProperty['types'] = ['na - not type enforced'];
                $asrThisProperty['isNullAnAllowedType']= 'na - not type enforced';
            } else {
                // this will change in php 8
                $typeIsAsString = $reflectionProperty->getType()->getName(); //https://www.php.net/manual/en/reflectiontype.tostring.php
                $asrThisProperty['types'] = [$typeIsAsString];


                /* 1) Handle builtin name mismatches.
                     public int $i; // this returns a type with name 'integer' which is annoying
                     public bool $b;// this returns a type with name 'boolean'.
                     We're going to account for this by saying it can be of type 'int' or 'integer'.
                     I suspect this will problematic in the future, but I'm not sure.
                2) In php 8, we'll be able to let a property be restricted a list of specified types, like int, double..
                     This is supposed to handle that.  It used to work when in docblock days, but not really used.
                */
                if ($typeIsAsString == 'int') {
                    $asrThisProperty['types'][] = 'integer';// in php 8, we should start allowing multiple types
                } elseif ($typeIsAsString == 'bool') {
                    $asrThisProperty['types'][] = 'boolean';// in php 8, we should start allowing multiple types
                }

                $asrThisProperty['isNullAnAllowedType'] = $reflectionProperty->getType()->allowsNull();
                if ($asrThisProperty['isNullAnAllowedType']) {
                    $asrThisProperty['types'][] = 'null';
                }
            }

            // Ensure no forbidden types
            $forbiddenTypes = $CfdClassName::FORBIDDEN_TYPES;
            $forbiddenTypesFoundHere = array_intersect($forbiddenTypes, $asrThisProperty['types']);
            if (count($forbiddenTypesFoundHere) > 0) {
                $csvTypes = implode(', ', $forbiddenTypesFoundHere);
                throw CfdError::LogicError(
                    "This " . get_called_class(
                    ) . "($propertyName) is specked to be of type $csvTypes, but that is on the forbidden list."
                );
            }


            $dtoThisProperty = new DtoRichProperty($asrThisProperty);
            $richProperties[$propertyName] = $dtoThisProperty;
        }


        $asr = ['_meta' =>
            ['numNonMeta' => $numNonMeta,
                'className'=>get_called_class()],
            'properties' => $richProperties];

        return $asr;

    }

    /* return all properties, even those non-tracked properties (but they at least need to be defined - nothing dynamic)
    // static vars are not exported
    // likeCl
    // class hw {
    // static $version; // not exported cuz static
    //  @var integer
    // public $age  // 'age' is expecte
    //
    //  public $height; // is export
    }
    $h = new hw();
    $h->eyes = 'hazel';
    // eyes are NOT exported, cuz not defined.

    //In most cases, this would be suitable for round-tripping
    */

    public function toDeepArray(): array
    {

        $asrRichPropreties = static::getRichProperties();
        $asrCfdProperties = [];
        foreach ($asrRichPropreties['properties'] as $propertyName=>$asrRichProprety) {
            if (!$asrRichProprety['isStatic']) {
                $asrCfdProperties[$propertyName] = $this->$propertyName;
            }
        }
        return $asrCfdProperties;

    }

    /* like toArray, but the tree is collapses so

    EtFramework19\Models\Team\CfdbTblBooker Object
    (
        [Email] => SchoolTwistWip\Cfd\Cfdb\Library\CfdbEmail Object
            (
                [Value] => tmp_test_1580908102_5e3abe4633299_wm@rohrer.org
            )

        [Phone] => SchoolTwistWip\Cfd\Cfdb\Library\CfdbPhone Object
            (
                [Value] => 61739806012
            )

        [User_Id] => EtFramework19\Models\Team\CfdbForeignKey_toUser Object
            (
                [Value] => 2
            )

        [Team_Uuid] => EtFramework19\Models\Team\CfdbForeignKey_toTeam Object
            (
                [Value] => b1be3874-4818-11ea-80bf-acde48001122
            )

        [Address_ofBooker_Uuid] =>
        [_OriginStory] => SchoolTwistWip\Cfd\Cfdb\Library\CfdbShortText Object
            (
                [Value] =>
            )

        [CreateOn] => SchoolTwistWip\Cfd\Cfdb\Library\CfdbDateTime Object
            (
                [Value] => 2020-02-05 08:09:08
            )

        [pk] => b1be8f72-4818-11ea-9ff1-acde48001122
        [FirstName] => SchoolTwistWip\Cfd\Cfdb\Library\CfdbShortString Object
            (
                [Value] => wm99a
            )

        [LastName] => SchoolTwistWip\Cfd\Cfdb\Library\CfdbShortString Object
            (
                [Value] => Rohrer
            )

    )

    --becomes--
    Array
        (
            [Email] => tmp_test_1580908102_5e3abe4633299_wm@rohrer.org
            [Phone] => 61739806012
            [User_Id] => 2
            [Team_Uuid] => b1be3874-4818-11ea-80bf-acde48001122
            [Address_ofBooker_Uuid] =>
            [_OriginStory] =>
            [CreateOn] => 2020-02-05 08:09:08
            [pk] => b1be8f72-4818-11ea-9ff1-acde48001122
            [FirstName] => wm99a
            [LastName] => Rohrer
        )
    */
    public function toShallowArray() : array
    {
        $asrDeepArray = $this->toDeepArray();
        foreach ($asrDeepArray as $propertyName=>$valueOrObject) {
            if (gettype($valueOrObject) == 'object') {
                if (property_exists($valueOrObject,'Value')) {
                    $valueOrObject = $Value = $valueOrObject->Value;
                    $asrDeepArray[$propertyName] = $valueOrObject;
                }

            }
        }
        return $asrDeepArray;
    }


    /* returns an array of property names that are under CFD control.  This will not return non-tracked properties.
    */
    public static function toArrayKeys_tracked(): array
    {
        $asrRichPropreties = static::getRichProperties();
        $arrTrackedNames = [];

        foreach ($asrRichPropreties['properties'] as $propertyName=>$asrRichProprety) {
            if ($asrRichProprety['isEnforced']) {
                $arrTrackedNames[] = $propertyName;
            }
        }
        return $arrTrackedNames;
    }

}


abstract class DtoCfd_Base_DbRow extends CfdBase
{
    //abstract $Uuid; // we'd do this if the language allowed it.
}





