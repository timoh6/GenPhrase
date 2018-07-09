<?php

namespace GenPhrase\Tests;

use GenPhrase\Random\Random;
use GenPhrase\Random\MockRandomBytes;
use PHPUnit\Framework\TestCase;

class GenPhraseRandomRandomTest extends TestCase
{
    /**
    * @expectedException \InvalidArgumentException
    */
    public function testTooLowPoolSizeThrowsException()
    {
        $obj = new Random();

        $obj->getElement(1);
    }

    /**
    * @expectedException \InvalidArgumentException
    */
    public function testTooHighPoolSizeThrowsException()
    {
        $obj = new Random();

        $obj->getElement(65537);
    }

    public function testGetElementGivesUniformDistribution()
    {
        $poolSize = 7776;
        $elements = array();

        $randomByteGenerator = new MockRandomBytes;
        $obj = new Random($randomByteGenerator);
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

        $this->assertCount(1, array_unique($elements));
        $this->assertEquals($poolSize, count($elements));
    }

    /**
    * @expectedException \InvalidArgumentException
    */
    public function testInvalidPowerOfTwoThrowsException()
    {
        $obj = new Random();

        $obj->setPowerOfTwo(8);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetTooHighPowerOfTwoThrowsException()
    {
        $obj = new Random();

        $obj->setPowerOfTwo(16777217);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetTooHighMaxPoolSizeThrowsException()
    {
        $obj = new Random();

        $obj->setMaxPoolSize(65537);
    }
}
