<?php

namespace GenPhrase\Tests;

use GenPhrase\Password;
use GenPhrase\WordlistHandler\Filesystem;
use PHPUnit\Framework\TestCase;

class GenPhrasePasswordTest extends TestCase
{
    public $entropyLowBits = 25;

    public $entropyHighBits = 121;

    public $testWords = array('test','test','test','test','test',
                              'test','test','test','test','test',
                              'test','test','test','test','test',
                              'test','test','test','test','test');

    public $testWordsNonUnique = array('test2','test2','test2','test3','test4',
                                       'test5','test6','test7','test8','test9',
                                       'test10','test11','test12','test12','test14',
                                       'test15','test16','test17','test18','test19');

    public function testConstructWithoutArguments()
    {
        $this->assertInstanceOf('GenPhrase\\Password', new Password());
    }

    public function testGetDefaultSeparators()
    {
        $obj = new Password();
        $separators = $obj->getSeparators();

        $this->assertEquals('-_!$&*+=23456789', $separators);
    }

    public function testCanSetSeparators()
    {
        $newSeparators = '1234';
        $obj = new Password();
        $obj->setSeparators($newSeparators);

        $this->assertEquals($newSeparators, $obj->getSeparators());
    }

    public function testGetDefaultEncoding()
    {
        $obj = new Password();

        $this->assertEquals('utf-8', $obj->getEncoding());
    }

    public function testCanSetEncoding()
    {
        $newEncoding = 'iso-8859-1';
        $obj = new Password();
        $obj->setEncoding($newEncoding);

        $this->assertEquals($newEncoding, $obj->getEncoding());
    }

    public function testGetDefaultConstructorDependencies()
    {
        $obj = new Password();

        $this->assertInstanceOf('GenPhrase\\WordlistHandler\\Filesystem', $obj->getWordlistHandler());
        $this->assertInstanceOf('GenPhrase\\WordModifier\\MbToggleCaseFirst', $obj->getWordmodifier());
        $this->assertInstanceOf('GenPhrase\\Random\\Random', $obj->getRandomProvider());
    }

    public function testGenerateReturnsNonEmptyString()
    {
        $obj = new Password();
        $password = $obj->generate(30);

        $this->assertInternalType('string', $password);
        $this->assertGreaterThan(0, strlen($password));
    }

    /**
    * @expectedException \InvalidArgumentException
    */
    public function testGenerateWithLowBitsThrowsException()
    {
        $obj = new Password();
        $obj->generate($this->entropyLowBits);
    }

    /**
    * @expectedException \InvalidArgumentException
    */
    public function testGenerateWithHighBitsThrowsException()
    {
        $obj = new Password();
        $obj->generate($this->entropyHighBits);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testNotEnoughWordsThrowsException()
    {
        $wordlistHandler = $this->createMock('GenPhrase\\WordlistHandler\\Filesystem');
        $wordlistHandler
            ->expects($this->any())
            ->method('getWordsAsArray')
            ->will($this->returnValue(array('a')));

        $obj = new Password($wordlistHandler);
        $obj->generate();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testNotEnoughUniqueWordsThrowsException()
    {
        $path = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'Data' . DIRECTORY_SEPARATOR . 'Wordlist' . DIRECTORY_SEPARATOR . 'dublicate_words.lst';
        $wordlistHandler = new Filesystem(array('path' => $path, 'identifier' => 'test'));

        $obj = new Password($wordlistHandler);
        $obj->generate();
    }

    public function testGenerateReturnsExpectedStrings()
    {
        $wordlistHandler = $this->createMock('GenPhrase\\WordlistHandler\\Filesystem');
        $wordlistHandler
            ->expects($this->any())
            ->method('getWordsAsArray')
            ->will($this->returnValue($this->testWords));

        $wordModifier = $this->createMock('GenPhrase\\WordModifier\\MbToggleCaseFirst');
        $wordModifier
            ->expects($this->any())
            ->method('modify')
            ->will($this->returnValue('test'));
        $wordModifier
            ->expects($this->any())
            ->method('getWordCountMultiplier')
            ->will($this->returnValue(1));

        $randomProvider = $this->createMock('GenPhrase\\Random\\Random');
        $randomProvider
            ->expects($this->any())
            ->method('getElement')
            ->will($this->returnValue(0));

        $obj = new Password($wordlistHandler, $wordModifier, $randomProvider);
        $obj->disableSeparators(true);

        $password = $obj->generate(26);
        $this->assertEquals('test test test test test test test', $password);

        $password = $obj->generate(36);
        $this->assertEquals('test test test test test test test test test', $password);

        $password = $obj->generate(50);
        $this->assertEquals('test test test test test test test test test test test test', $password);
    }

    public function makesSenseToUseSeparatorsDataProvider()
    {
        return array(
            array(26, 13, 4, false),
            array(27, 13, 4, true),
            array(28, 13, 4, true),
            array(29, 13, 4, true),
            array(30, 13, 4, true),
            array(31, 13, 4, false),
            array(32, 13, 4, false),
            array(33, 13, 4, false),
            array(34, 13, 4, false),
            array(35, 13, 4, false),
            array(36, 13, 4, false),
            array(37, 13, 4, false)
        );
    }

    /**
     * @dataProvider makesSenseToUseSeparatorsDataProvider
     */
    public function testMakesSenseToUseSeparators($bits, $wordBits, $separatorBits, $shouldUse)
    {
        $obj = new Password();

        $this->assertEquals($shouldUse, $obj->makesSenseToUseSeparators($bits, $wordBits, $separatorBits), 'Failed for bits:' . $bits);
    }

    public function testAlwaysUseSeparators()
    {
        $wordlistHandler = $this->createMock('GenPhrase\\WordlistHandler\\Filesystem');
        $wordlistHandler
            ->expects($this->any())
            ->method('getWordsAsArray')
            ->will($this->returnValue($this->testWords));

        $wordModifier = $this->createMock('GenPhrase\\WordModifier\\MbToggleCaseFirst');
        $wordModifier
            ->expects($this->any())
            ->method('modify')
            ->will($this->returnValue('test'));
        $wordModifier
            ->expects($this->any())
            ->method('getWordCountMultiplier')
            ->will($this->returnValue(1));

        $randomProvider = $this->createMock('GenPhrase\\Random\\Random');
        $randomProvider
            ->expects($this->any())
            ->method('getElement')
            ->will($this->returnValue(0));

        $obj = new Password($wordlistHandler, $wordModifier, $randomProvider);
        $obj->setSeparators('$');

        $obj->alwaysUseSeparators(true);
        $password = $obj->generate(26);
        $this->assertEquals('test$test$test$test$test$test$test', $password);

        $obj->alwaysUseSeparators(false);
        $password = $obj->generate(26);
        $this->assertEquals('test test test test test test test', $password);
    }

    public function testSeparatorsAreUnique()
	{
		$obj = new Password();

		$obj->setSeparators('$$');
		$this->assertEquals('$', $obj->getSeparators());

		$obj->setSeparators('112334566');
		$this->assertEquals('123456', $obj->getSeparators());
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testEmptySeparatorsThowsException()
	{
		$obj = new Password();

		$obj->setSeparators('');
		$obj->generate();
    }

    public function precisionFloatIsNotRoundingDataProvider()
    {
        return array(
            array(log(49667, 2), 15.59),
            array(log(99334, 2), 16.59),
            array(log(102837, 2), 16.64)
        );
    }

    /**
     * @dataProvider precisionFloatIsNotRoundingDataProvider
     */
    public function testPrecisionFloatIsNotRounding($precision, $expectedValue)
    {
        $obj = new Password();
        $float = $obj->precisionFloat($precision);

        $this->assertEquals($expectedValue, $float, 'Failed for num: ' . $precision);

    }
}
