<?php

class GenPhrase_WordlistHandler_FilesystemTest extends PHPUnit_Framework_TestCase
{
    public function testContainsNoDuplicates()
    {
        $path = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'Data' . DIRECTORY_SEPARATOR . 'Wordlist' . DIRECTORY_SEPARATOR . 'dublicate_words.lst';
        $obj = new GenPhrase\WordlistHandler\Filesystem(array('path' => $path, 'identifier' => 'test'));
        
        $returnedWords = $obj->getWordsAsArray();
        $count = count($returnedWords);
        
        $this->assertEquals(3, $count);
    }
    
    public function testCanAddWordlist()
    {
        $path = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'Data' . DIRECTORY_SEPARATOR . 'Wordlist' . DIRECTORY_SEPARATOR . 'dublicate_words.lst';
        $path2 = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'Data' . DIRECTORY_SEPARATOR . 'Wordlist' . DIRECTORY_SEPARATOR . 'two_words.lst';
        $obj = new GenPhrase\WordlistHandler\Filesystem(array('path' => $path, 'identifier' => 'test'));
        $obj->addWordlist($path2, 'test2');
        
        $returnedWords = $obj->getWordsAsArray();
        $count = count($returnedWords);
        
        $this->assertEquals(5, $count);
    }
}
