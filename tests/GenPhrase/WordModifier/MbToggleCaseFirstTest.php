<?php

class GenPhrase_WordModifier_MbToggleCaseFirstTest extends PHPUnit_Framework_TestCase
{
    public function testModifyCapitalizes()
    {
        $word = 'äbcd';
        $expected = 'Äbcd';

        $randomProvider = $this->getMock('GenPhrase\\Random\\Random');
        $randomProvider
            ->expects($this->once())
            ->method('getElement')
            ->will($this->returnValue(0));
        
        $obj = new GenPhrase\WordModifier\MbToggleCaseFirst($randomProvider);
        $test = $obj->modify($word);
        
        $this->assertEquals($expected, $test);
    }

    public function testModifyLowers()
    {
        $word = 'Äbcd';
        $expected = 'äbcd';

        $randomProvider = $this->getMock('GenPhrase\\Random\\Random');
        $randomProvider
            ->expects($this->once())
            ->method('getElement')
            ->will($this->returnValue(0));

        $obj = new GenPhrase\WordModifier\MbToggleCaseFirst($randomProvider);
        $test = $obj->modify($word);

        $this->assertEquals($expected, $test);
    }
}