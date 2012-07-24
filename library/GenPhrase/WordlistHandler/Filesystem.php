<?php
namespace GenPhrase\WordlistHandler;

/**
 * @author timoh <timoh6@gmail.com>
 */
class Filesystem implements WordlistHandlerInterface
{
    protected $_wordlists = array();
    protected static $_isCached = false;
    protected static $_words = array();
    
    /**
     * 
     * @param array $wordlist e.g. array('path' => '/some/path', 'identifier' => 'some_id').
     */
    public function __construct(array $wordlist = null)
    {
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
        
        self::$_words = array_unique(self::$_words);
        
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
     * 
     * @param string $path The filesystem path to the file.
     * @param string $identifier The identifier to identify this file.
     */
    public function addWordlist($path, $identifier)
    {
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
