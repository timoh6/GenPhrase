<?php
namespace GenPhrase\WordlistHandler;

/**
 * @author timoh <timoh6@gmail.com>
 */
interface WordlistHandlerInterface
{
    public function getWordsAsArray();
    
    public function addWordlist($path, $identifier);
    
    public function removeWordlist($identifier);
}
