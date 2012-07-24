<?php
namespace GenPhrase\WordModifier;

use GenPhrase\Random\RandomInterface as RandomInterface;
use GenPhrase\Random\Random as Random;

/**
 * @author timoh <timoh6@gmail.com>
 */
class MbCapitalizeFirst implements WordModifierInterface
{
    /**
     * @var RandomInterface
     */
    protected $_randomProvider = null;
    
    protected $_probabilityPoolSize = 2;
    
    protected $_wordCountMultipier = 2;
    
    /**
     * $probabilityPoolSize controls the changes whether to modify the word or
     * not.
     * 
     * We fetch a random number from a set size of $probabilityPoolSize, and
     * if this number is 0, then the word will be modified.
     * 
     * If $probabilityPoolSize is 2, we fetch a number in the range 0-1, so
     * there is a 1/2 change to modify the word.
     * 
     * If $probabilityPoolSize is 3, we fetch a number in the range 0-2, so
     * there is a 1/3 change to modify the word. Etc.
     * 
     * @param RandomInterface $randomProvider
     * @param int $probabilityPoolSize 
     */
    public function __construct(RandomInterface $randomProvider = null,
                                 $probabilityPoolSize = 2)
    {
        if ($randomProvider === null)
        {
            $randomProvider = new Random();
        }
        $this->_randomProvider = $randomProvider;
        
        $this->_probabilityPoolSize = (int) $probabilityPoolSize;
    }
    
    /**
     * 
     * @param string The word to modify
     * @param string $encoding
     * @return string
     * @throws \RuntimeException | \InvalidArgumentException
     */
    public function modify($string, $encoding = 'utf-8')
    {
        $string = (string) $string;
        $len = mb_strlen($string, $encoding);
        
        if ($len > 0)
        {
            try
            {
                if ($this->_randomProvider->getElement($this->_probabilityPoolSize) === 0)
                {
                    $character = mb_substr($string, 0, 1, $encoding);
                    $character = mb_convert_case($character, MB_CASE_UPPER, $encoding);
                    $string = $character . mb_substr($string, 1, $len, $encoding);
                }
            }
            catch (Exception $e)
            {
                throw new $e;
            }
        }
        
        return $string;
    }
    
    /**
     * 
     * @return int The multipier
     */
    public function getWordCountMultipier()
    {
        return $this->_wordCountMultipier;
    }
}
