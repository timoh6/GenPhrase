<?php

class GenPhrase_Random_RandomTest extends PHPUnit_Framework_TestCase
{
    /**
    * @expectedException \InvalidArgumentException
    */
    public function testTooLowPoolSizeThrowsException()
    {
        $obj = new GenPhrase\Random\Random();
        
        $obj->getElement(1);
    }
    
    /**
    * @expectedException \InvalidArgumentException
    */
    public function testTooHighPoolSizeThrowsException()
    {
        $obj = new GenPhrase\Random\Random();
        
        $obj->getElement(65537);
    }
    
    public function testGetElementGivesUniformDistribution()
    {
        $poolSize = 7776;
        $elements = array();
        
        $randomByteGenerator = new GenPhrase\Random\MockRandomBytes;
        $obj = new GenPhrase\Random\Random($randomByteGenerator);
        $obj->setMaxPoolSize($poolSize);
        $obj->setPowerOfTwo(8192);

        for ($i = 0; $i < $poolSize; $i++)
        {
            $element = $obj->getElement($poolSize);
            if (!isset($elements[$element]))
            {
                $elements[$element] = 0;
            }
            else
            {
                $elements[$element]++;
            }
        }

        $uniqElements = count(array_unique($elements));
        $elementsCount = count($elements);

        $this->assertEquals(1, $uniqElements);
        $this->assertEquals($poolSize, $elementsCount);
    }
    
    /**
    * @expectedException \InvalidArgumentException
    */
    public function testInvalidPowerOfTwoThrowsException()
    {
        $obj = new GenPhrase\Random\Random();
        
        $obj->setPowerOfTwo(8);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetTooHighPowerOfTwoThrowsException()
    {
        $obj = new GenPhrase\Random\Random();

        $obj->setPowerOfTwo(16777217);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetTooHighMaxPoolSizeThrowsException()
    {
        $obj = new GenPhrase\Random\Random();

        $obj->setMaxPoolSize(65537);
    }
}