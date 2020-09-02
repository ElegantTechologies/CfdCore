<?php
namespace ElegantTechnologies\Cfd\Lib;


class CfvDateTime extends \ElegantTechnologies\Cfd\Core\Cfv {
    public function __construct(public string $value) {
        $maybeValidValue = $value;
        $format = 'Y-m-d H:i:s';

        // Handle when they don't put anything after the day. 2000-11-04 would otherwise work
        $t = date($format,strtotime($maybeValidValue));

        if ($maybeValidValue == $t) {
            // good
        } else {
            throw new \ElegantTechnologies\Cfd\Core\CfdError("$t !=$maybeValidValue Please pass data as exaclty $format ");
            #return new \SchoolTwist\Validations\Returns\DtoValid(['isValid' => false, 'enumReason'=>'NotRoundtripping','message'=>"$t !=$maybeValidValue Please pass data as exaclty $format " ]);
        }
    }

    public static function now_asString() : string
    {
        return date('Y-m-d H:i:s');//1970-11-04 13:11:25
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




