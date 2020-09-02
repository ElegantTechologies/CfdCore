<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
class Naked extends  \ElegantTechnologies\Cfd\Core\CfdoBase
{
}

class Simple_0y_Cfd extends  \ElegantTechnologies\Cfd\Core\CfdoBase
{
    public $caresGiven; // Must be set, but not type enforced.
}

class Simple_0z_Cfd extends  \ElegantTechnologies\Cfd\Core\CfdoBase
{
    public ?int $caresGiven = null; // Just proving this compiles
}
class Simple_0_Cfd extends  \ElegantTechnologies\Cfd\Core\CfdoBase
{
    public int $caresGiven = 0; // must only create w/ an optional int
}
class Simple_0b_Cfd extends  \ElegantTechnologies\Cfd\Core\CfdoBase
{
    public ?int $caresGiven = 0; // must only create w/ an optional int, or null
}

class Simple_0c_Cfd extends  \ElegantTechnologies\Cfd\Core\CfdoBase
{
    public int $caresGiven; // must create with a mandatory int
}
class Simple_0d_Cfd extends  \ElegantTechnologies\Cfd\Core\CfdoBase
{
    public ?int $caresGiven; // must create with a mandatory int, or null
}

class Simple_0e_Cfd extends  \ElegantTechnologies\Cfd\Core\CfdoBase
{
     #public string $caresGiven2 = 0; // wouldn't event compile cuz type mismatch
}
class Simple_1_Cfd extends  \ElegantTechnologies\Cfd\Core\CfdoBase
{
    public string $name; // simple var, but it had better be a string when assigning
}
/**
 * Class CfdBase
 * @package SchoolTwist\Cfd\Core
 *
 *  // Since extending CfdBase, public properties all validated at creation
 *  // Any public property w/o a default is required to be set at creation
 *  // Public properties can be read, but not updated. To update, create a new copy with different inputs
 */
class NamedThingsCfd extends \ElegantTechnologies\Cfd\Core\CfdoBase
{
    # protected const  META_PREFIXES = ['doesHave'];  //
    #  public CONST ALLOW_UPCONVERT_TYPES = ['integer','string'];
    public string $lastName; // simple var, but it had better be a string when assigning

    public int $numEyes; // simple var, but it had better be a int when assigning
    public ?int $age; // simple var, but it had better be a int when assigning. It can be null if we don't know the age
    // It must be assigned something

    public string $namesIKnow;  // There is a function

    // Optional Custom Validation
    public static function namesIKnow_Validates($untrustedValue): \SchoolTwist\Validations\Returns\DtoValid
    {
        if (in_array($untrustedValue, ['Tom', 'Bill', 'Chad', 'Lisa', 'Wendy'])) {
            return new DtoValid(
                [
                    'isValid' => true,
                    'enumReason' => 'Found',
                    'message' => "I know a person named $untrustedValue."
                ]
            );
        } else {
            return new DtoValid(
                [
                    'isValid' => false,
                    'enumReason' => 'NotFound',
                    'message' => "name($untrustedValue) is not a name of somebody I went to school with."
                ]
            );
        }
    }
}




class _002_DtoCfd_Basic_string_74_Test extends TestCase
{

    function test_Num()
    {
        // fail an nothing added
          try {
              $c = new Naked();
              $this->assertTrue( false, "Should not get here: Not passed any propertys  array");
          } catch (ArgumentCountError $e) {
              $this->assertTrue(true, "This should have failed hard (and gotten here) cuz int != integer, and php numbers are integers.  int is slang for integer.: " . get_called_class() . "  " . __LINE__);
          }

          $c = new Naked([]);
          $this->assertTrue( isset($c), "Set nothing, but fine cuz this is empty");


          try {
              $c = new Naked(["a"=>1]);
              $this->assertTrue( false, "Should not get here cuz passed extra property");
          } catch (SchoolTwist\Cfd\Core\ErrorFromCfd $e) {
              $this->assertTrue(true, "");
          }

    }


   function test_RequireType()
    {
        // fail an nothing added
          try {
              $c = new Simple_0y_Cfd();
              $this->assertTrue( false, "Should not get here: Not passed any propertys  array");
          } catch (ArgumentCountError $e) {
              $this->assertTrue(true, "This should have failed hard (and gotten here) cuz int != integer, and php numbers are integers.  int is slang for integer.: " . get_called_class() . "  " . __LINE__);
          }


           try {
              $c = new Simple_0y_Cfd([]);
              $this->assertTrue( false, "Should not get here cuz missing property caresGiven w/o a default being specified");
          } catch (SchoolTwist\Cfd\Core\ErrorFromCfd $e) {
              $this->assertTrue(true, "");
          }



          print_r(Simple_0y_Cfd::getRichProperties());
          $c = new Simple_0y_Cfd(['caresGiven'=>1]);
          $this->assertTrue( $c->caresGiven == 1, "Should get here.  This used to fail because caresGiven didn't have a type, but that is ok now.");

          try {
             $c = new Simple_0y_Cfd(['caresGiven'=>1, 'a'=>1]);
              $this->assertTrue( false, "Should not get here cuz extra property a");
          } catch (SchoolTwist\Cfd\Core\ErrorFromCfd $e) {
              $this->assertTrue(true, "");
          }
    }



    function testItemWithoutTypedPropertiesProboblyNonsenseCfd()
    {

          try {
              $c = new Simple_0_Cfd();
              $this->assertTrue( false, "Should not get here: Not passed constructor array");
          } catch (ArgumentCountError $e) {
              $this->assertTrue(true, "This should have failed hard (and gotten here) cuz int != integer, and php numbers are integers.  int is slang for integer.: " . get_called_class() . "  " . __LINE__);
          }


           $c = new Simple_0_Cfd([]);
          $this->assertTrue( $c->caresGiven == 0, "ok, cuz careGiven has a default");

          $c = new Simple_0_Cfd(['caresGiven'=>1]);
          $this->assertTrue( $c->caresGiven == 1, "ok, I assigned a number. This also tests that integer converts to int, which is a 7.4 thingy");

            try {
              $c = new Simple_0_Cfd(['caresGiven'=>1.1]);
              $this->assertTrue( false, "Should not get here: cuz 1.1 is a float, not an int");
          } catch (SchoolTwist\Cfd\Core\ErrorFromCfd $e) {
              $this->assertTrue(true, "This should have failed hard (and gotten here) cuz int != integer, and php numbers are integers.  int is slang for integer.: " . get_called_class() . "  " . __LINE__);
          }


    }

}
