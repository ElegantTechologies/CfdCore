<?php
declare(strict_types=1);
namespace ElegantTechnologies\Cfd\Lib;

/*
 * Usage:
 *      class Phase extends EnumValueBase {
            public function __construct(public string $value) { parent::__construct(['open','closed']);}
        }
        $o = new Phase('open');
        print "\n I am ".$o->value;

        $o = new Phase('closed');
        print "\n I am ".$o->value;

        try {
            $o = new Phase('shut');
        } catch (\Throwable $exception) {
            print "\n " .$exception->getMessage();
        }
*/


abstract class Cfes extends \ElegantTechnologies\Cfd\Core\Cfv {
    #abstract public array $value;

    public function __construct(private array $_ArrEnumValuePossibilities){
        $maybeValidItems = $this->value;
        if (!is_array($this->value)) {
            $meClass = $this::class;
            throw new \ElegantTechnologies\Cfd\Core\CfdError("'$meClass::value' must be declared as  an array", -87682345);
        }
        foreach ($maybeValidItems as $maybeValidItem) {
             $hasPropertyThere = in_array($maybeValidItem, $_ArrEnumValuePossibilities) ? 1 : 0;
             if (!$hasPropertyThere){
                   $csvOptions = implode(', ', $_ArrEnumValuePossibilities);
                    $meClass = $this::class;
                   throw new \ElegantTechnologies\Cfd\Core\CfdError("'$maybeValidItem' is not a valid enumeration for $meClass::valuePossibilities([$csvOptions])", -87687545);
               }
        }



       parent::__construct();
    }

    public function doesHaveThis($doesHave_singleValue) : bool
    {
         $arrDoesHaves = $this->value;
        return (in_array($doesHave_singleValue, $arrDoesHaves,true));
    }

    public function doesHaveThese(array $arrMustHaves) : bool
    {
        $arrDoesHaves = $this->value;
        return (array_intersect($arrMustHaves, $arrDoesHaves) == $arrMustHaves);
        // $array1 is a subset of $array2
        //https://stackoverflow.com/a/12276627/93933
    }

     public function doesHaveOnlyThis($doesHave_singleValue) : bool
    {
         $arrDoesHaves = $this->value;
        return (count($arrDoesHaves) == 1 && $this->doesHaveThis($doesHave_singleValue));
    }

    // missing doesHaveOnlyThese... wait until actual use case

}
