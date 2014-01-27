<?php
namespace GenPhrase\WordlistHandler;

/**
 * @author timoh <timoh6@gmail.com>
 */
class Filesystem implements WordlistHandlerInterface
{
    /**
     * List of wordlists as a key-value array.
     * E.g. $_wordlists['default'] = '/path/to/GenPhrase/Wordlists/english.lst';
     * 
     * @var array 
     */
    protected $_wordlists = array();
    
    /**
     *
     * @var boolean 
     */
    protected static $_isCached = false;
    
    /**
     *
     * @var array 
     */
    protected static $_words = array();
    
    /**
     * 
     * @param array $wordlist e.g. array('path' => '/some/path/to/wordlist', 'identifier' => 'some_id').
     */
    public function __construct(array $wordlist = null)
    {
        // Default to english.lst
        if ($wordlist === null)
        {
            $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Wordlists' . DIRECTORY_SEPARATOR . 'english.lst';
            $identifier = 'default';
        }
        else
        {
            $path = $wordlist['path'];
            $identifier = $wordlist['identifier'];
        }
        
        $this->addWordlist($path, $identifier);
    }
    
    /**
     * Returns all the unique lines from a file(s) as a numerically indexed array.
     * E.g. Array([0] => word1 [1] => word2...).
     * 
     * @return array
     * @throws \RuntimeException
     */
    public function getWordsAsArray()
    {
        if (self::$_isCached === true)
        {
            return self::$_words;
        }
        
        self::$_words = array();
        
        foreach($this->_wordlists as $file)
        {
            if (file_exists($file) && is_readable($file))
            {
                $wordSet = $this->_readData($file);
                
                if ($wordSet !== false)
                {
                    self::$_words = array_merge(self::$_words, $wordSet);
                }
            }
        }
        self::$_words = array_values(array_unique(self::$_words));
        
        if (!empty(self::$_words))
        {
            $this->setIsCached(true);
            
            return self::$_words;
        }
        else
        {
            throw new \RuntimeException('No wordlists available');
        }
    }
    
    /**
     * Adds the specified file to the list of wordlists. This file will be
     * identified by $identifier.
     * 
     * If $path does not contain directory separator character, the filename
     * will be assumed to be in "Wordlists" directory (GenPhrase/Wordlists).
     * 
     * @param string $path The filesystem path to the file.
     * @param string $identifier The identifier to identify this file.
     */
    public function addWordlist($path, $identifier)
    {
        if (strpos($path, DIRECTORY_SEPARATOR) === false)
        {
            $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Wordlists' . DIRECTORY_SEPARATOR . $path;
        }
        
        $this->_wordlists[$identifier] = $path;
        $this->setIsCached(false);
    }
    
    /**
     * 
     * @param string $identifier
     */
    public function removeWordlist($identifier)
    {
        if (isset($this->_wordlists[$identifier]))
        {
            unset($this->_wordlists[$identifier]);
        }
        $this->setIsCached(false);
    }
    
    /**
     * 
     * @param boolean $isCached
     */
    public function setIsCached($isCached)
    {
        self::$_isCached = (bool) $isCached;
    }
    
    /**
     * 
     * @param string $file
     * @return array|false
     */
    protected function _readData($file)
    {
        return file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }
}
