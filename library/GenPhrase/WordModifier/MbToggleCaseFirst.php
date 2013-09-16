<?php
namespace GenPhrase\WordModifier;

use GenPhrase\Random\RandomInterface as RandomInterface;
use GenPhrase\Random\Random as Random;

/**
 * @author timoh <timoh6@gmail.com>
 */
class MbToggleCaseFirst implements WordModifierInterface
{
    /**
     * @var RandomInterface
     */
    protected $_randomProvider = null;

    /**
     * @var int
     */
    protected $_probabilityPoolSize = 2;

    /**
     * @var int
     */
    protected $_wordCountMultiplier = 2;
    
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
    public function __construct(RandomInterface $randomProvider = null, $probabilityPoolSize = 2)
    {
        if ($randomProvider === null)
        {
            $randomProvider = new Random();
        }
        $this->_randomProvider = $randomProvider;
        
        $this->_probabilityPoolSize = (int) $probabilityPoolSize;
    }


    /**
     * Performs case folding on the first character of a supplied word (making it either lower or upper case).
     * The word is modified, by default, on a 50:50 chance. I.e. we choose a random number 0 or 1, and if we
     * get 0, we modify the word.
     *
     * @param $string
     * @param string $encoding
     * @return string
     * @throws \Exception
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
                    $upper = mb_strtoupper($character, $encoding);
                    $lower = mb_strtolower($character, $encoding);

                    if ($character === $upper)
                    {
                        $character = $lower;
                    }
                    else
                    {
                        $character = $upper;
                    }

                    $string = $character . mb_substr($string, 1, $len, $encoding);
                }
            }
            catch (\Exception $e)
            {
                throw $e;
            }
        }
        
        return $string;
    }
    
    /**
     * 
     * @return int The multiplier
     */
    public function getWordCountMultiplier()
    {
        return $this->_wordCountMultiplier;
    }
}
