<?php
namespace ElegantTechnologies\Cfd\Lib;

class CfvYmd extends \ElegantTechnologies\Cfd\Core\Vto {
     public function __construct(public string $value) {
         $maybeValidValue = $value;
        $t = date("Y-m-d",strtotime($maybeValidValue)); // https://xkcd.com/1179/

        if ($maybeValidValue == $t) {
            # ok
        } else {
            throw new \ElegantTechnologies\Cfd\Core\ErrorFromCfd("$t !=$maybeValidValue");
            #return new \SchoolTwist\Validations\Returns\DtoValid(['isValid' => false, 'enumReason'=>'NotRoundtripping','message'=>"$t !=$maybeValidValue" ]);
        }
    }

    //    public static function upConvertType_elseNull(string $propertyName, $dangerousValue)
    //    {
    //        $incomingType = gettype($dangerousValue);
    //        if ($incomingType == 'string') {
    //            try {
    //                $descendantName = get_called_class();
    //                $dangerousValue_new = new $descendantName(['Value' => $dangerousValue]);
    //            } catch (\Throwable $e) {
    //                return null;
    //            }
    //            return $dangerousValue_new;
    //
    //        } else {
    //            return null;
    //        }
    //    }
}

