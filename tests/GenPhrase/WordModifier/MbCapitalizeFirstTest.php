<?php

class GenPhrase_WordModifier_MbCapitalizeFirstTest extends PHPUnit_Framework_TestCase
{
    public function testModifyCapitalizes()
    {
        $word = 'äbcd';
        $expected = 'Äbcd';
        
        // MockRandomBytes() will return 0 for the first call.
        $randomByteGenerator = new GenPhrase\Random\MockRandomBytes;
        $randomProvider = new GenPhrase\Random\Random($randomByteGenerator);
        
        $obj = new GenPhrase\WordModifier\MbCapitalizeFirst($randomProvider);
        $test = $obj->modify($word);
        
        $this->assertEquals($expected, $test);
    }
}