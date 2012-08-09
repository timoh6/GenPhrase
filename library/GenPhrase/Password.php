<?php
namespace GenPhrase;

use GenPhrase\WordlistHandler\WordlistHandlerInterface as WordlistHandlerInterface;
use GenPhrase\WordlistHandler\Filesystem as WordlistHandler;
use GenPhrase\WordModifier\WordModifierInterface as WordModifierInterface;
use GenPhrase\WordModifier\MbCapitalizeFirst as WordModifier;
use GenPhrase\Random\RandomInterface as RandomInterface;
use GenPhrase\Random\Random as Random;

/**
 * The Password class clues together all the needed pieces and generates
 * passphrases based on supplied variables.
 * 
 * @author timoh <timoh6@gmail.com>
 */
class Password
{
    /**
     * @var WordlistHandlerInterface
     */
    protected $_wordlistHandler = null;
    
    /**
     * @var WordModifierInterface
     */
    protected $_wordModifier = null;
    
    /**
     * @var RandomInterface
     */
    protected $_randomProvider = null;
    
    protected $_separators = '-_!$&*+=23456789';
    
    protected $_disableSeparators = false;
    
    protected $_disableWordModifier = false;
    
    protected $_encoding = 'utf-8';
    
    const MIN_WORD_COUNT = 20;
    
    const MIN_ENTROPY_BITS = 26.0;
    
    const MAX_ENTROPY_BITS = 120.0;
    
    public function __construct(WordlistHandlerInterface $wordlistHandler = null,
                                 WordModifierInterface $wordModifier = null,
                                 RandomInterface $randomProvider = null)
    {
        if ($wordlistHandler === null)
        {
            $wordlistHandler = new WordlistHandler();
        }
        $this->_wordlistHandler = $wordlistHandler;
        
        if ($wordModifier === null)
        {
            $wordModifier = new WordModifier();
        }
        $this->_wordModifier = $wordModifier;
        
        if ($randomProvider === null)
        {
            $randomProvider = new Random();
        }
        $this->_randomProvider = $randomProvider;
    }
    
    /**
     * Generates a passphrase based on supplied wordlists, seaparators, entropy
     * bits and word modifier.
     * 
     * @param float $bits
     * @return string
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function generate($bits = 50.0)
    {
        $bits = (float) $bits;
        $separators = $this->getSeparators();
        $separatorBits = $this->precisionFloat(log(strlen($separators), 2));
        $passPhrase = '';
        
        try
        {
            if ($bits < self::MIN_ENTROPY_BITS || $bits > self::MAX_ENTROPY_BITS)
            {
                throw new \InvalidArgumentException('Invalid parameter: $bits must be between ' . self::MIN_ENTROPY_BITS . ' and ' . self::MAX_ENTROPY_BITS);
            }
            
            $words = $this->_wordlistHandler->getWordsAsArray();
            $count = count($words);
            if ($count < self::MIN_WORD_COUNT)
            {
                throw new \RuntimeException('Wordlist must have at least ' . self::MIN_WORD_COUNT . ' unique words');
            }
            
            $countForBits = $count;
            if ($this->_disableWordModifier !== true)
            {
                $countForBits = $countForBits * $this->_wordModifier->getWordCountMultipier();
            }
            $wordBits = $this->precisionFloat(log($countForBits, 2));
            
            if ($wordBits < 1)
            {
                throw new \RuntimeException('Words does not have enough bits to create a passphrase');
            }
            
            $maxIndex = $count;
            
            if ($this->_disableSeparators === true)
            {
                $useSeparators = false;
            }
            else
            {
                $useSeparators = $this->makesSenseToUseSeparators($bits, $wordBits, $separatorBits);
            }
            
            do
            {
                $index = $this->_randomProvider->getElement($maxIndex);
                $word = strtolower($words[$index]);
                
                if ($this->_disableWordModifier !== true)
                {
                    $word = $this->_wordModifier->modify($word, $this->_encoding);
                }

                $passPhrase .= $word;
                $bits -= $wordBits;

                if ($bits > $separatorBits && $useSeparators === true && isset($separators[0]))
                {
                    $passPhrase .= $separators[$this->_randomProvider->getElement(strlen($separators))];
                    $bits -= $separatorBits;
                }
                else if ($bits > 0.0 && $this->_disableSeparators === false)
                {
                    $passPhrase .= ' ';
                }
            }
            while ($bits > 0.0);
        }
        catch (Exception $e)
        {
            throw $e;
        }
        
        return $passPhrase;
    }
    
    /**
     * 
     * @param string $path
     * @param string $identifier
     */
    public function addWordlist($path, $identifier)
    {
        $this->_wordlistHandler->addWordlist($path, $identifier);
    }
    
    /**
     * 
     * @param string $identifier
     */
    public function removeWordlist($identifier)
    {
        $this->_wordlistHandler->removeWordlist($identifier);
    }
    
    /**
     * 
     * @return string
     */
    public function getSeparators()
    {
        return $this->_separators;
    }
    
    /**
     * Sets the separator characters. Must be single-byte characters.
     * 
     * @param string $separators
     */
    public function setSeparators($separators)
    {
        $this->_separators = (string) $separators;
    }
    
    /**
     * Sets whether to use separator characters or not.
     * 
     * @param boolean $disableSeparators
     */
    public function disableSeparators($disableSeparators)
    {
        $this->_disableSeparators = (bool) $disableSeparators;
    }
    
    /**
     * Sets whether to use word modifier or not.
     * 
     * @param boolean $disableWordModifier
     */
    public function disableWordModifier($disableWordModifier)
    {
        $this->_disableWordModifier = (bool) $disableWordModifier;
    }
    
    /**
     * 
     * @return string $this->_encoding
     */
    public function getEncoding()
    {
        return $this->_encoding;
    }
    
    /**
     * The encoding identifier, for example: ISO-8859-1. 
     * 
     * @param string $encoding
     */
    public function setEncoding($encoding)
    {
        $this->_encoding = $encoding;
    }
    
    /**
     * Detects whether it is sensible to use separator characters.
     * 
     * @param float $bits
     * @param float $wordBits
     * @param float $separatorBits
     * @return boolean
     */
    public function makesSenseToUseSeparators($bits, $wordBits, $separatorBits)
    {
        $wordCount = 1 + ($bits + (($wordBits + $separatorBits - 1) - $wordBits)) / ($wordBits + $separatorBits);
        
        return ((int) (($bits + ($wordBits - 1)) / $wordBits) !== (int) $wordCount);
    }
    
    /**
     * 
     * @return WordlistHandlerInterface
     */
    public function getWordlistHandler()
    {
        return $this->_wordlistHandler;
    }
    
    /**
     * 
     * @return WordModifierInterface
     */
    public function getWordModifier()
    {
        return $this->_wordModifier;
    }
    
    /**
     * 
     * @return Random
     */
    public function getRandomProvider()
    {
        return $this->_randomProvider;
    }
    
    /**
     * Returns a float presenting the supplied number.
     * 
     * We use BC Math to avoid rounding errors. We use max. 2 digit precision.
     * This is because we do not want to take changes that the returned float
     * will be rounded up.
     * 
     * E.g. precisionFloat(log(49667, 2)) will return 15.59 instead
     * of 15.6.
     * 
     * @param int|float $num
     * @return float
     */
    public function precisionFloat($num)
    {
        return (float) bcadd($num, 0, 2);
    }
}