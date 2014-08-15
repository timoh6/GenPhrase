<?php
namespace GenPhrase\Random;

use GenPhrase\Random\RandomBytes;

/**
 * @author timoh <timoh6@gmail.com>
 */

class Random implements RandomInterface
{
    const MAX_ALLOWED_POOL_SIZE = 65536;
    const MAX_ALLOWED_POWER_OF_TWO = 16777216;

    /**
     * Must be <= $_powerOfTwo. See below.
     *
     * @var int 
     */
    protected $_maxPoolSize = 65536;
    
    /**
     * Must be a power of two, for example 2^24 (16 777 216). In the security
     * point of view, must be >= $_maxPoolSize. In the efficiency point of view,
     * should be considerably greater than $_maxPoolSize. As we default to
     * 65536 $_maxPoolSize (which should be easily enough for wordlists), using
     * 2^24 as our $_powerOfTwo should be enough to keep the probability of
     * having to throw "intermediate" results away low. 
     * 
     * @var int
     */
    protected $_powerOfTwo = 16777216;
    
    /**
     * @var RandomBytes
     */
    protected $_randomByteGenerator = null;
    
    public function __construct($randomByteGenerator = null)
    {
        if ($randomByteGenerator === null)
        {
            $randomByteGenerator = new RandomBytes();
        }
        $this->_randomByteGenerator = $randomByteGenerator;

        try
        {
            $this->checkPowerOfTwo();
        }
        catch (\InvalidArgumentException $e)
        {
            throw $e;
        }
    }
    
    /**
     * Return an element (integer, in range 0-$poolSize minus one) from the
     * given "pool".
     * 
     * The element is chosen uniformly at random.
     * 
     * If $poolSize is 2: return 0 or 1.
     * If $poolSize is 3: return 0 or 1 or 2.
     * If $poolSize is 4: return 0 or 1 or 2 or 3.
     * etc.
     *
     * @param int $poolSize Size of the pool to choose from.
     * @return int The generated random number within the pool size.
     * @throws \InvalidArgumentException If provided $poolSize is not between 2 and $_maxPoolSize.
     * @throws \RangeException If the supplied range is too great to generate.
     * @throws \RuntimeException If it was not possible to generate random bytes.
     */
    public function getElement($poolSize)
    {
        /**
         * The general formulation to choose a random element is to find the
         * smallest integer k, such that 2^k >= $poolSize. Then generate a k-bit
         * random number ($result). If $result >= $poolSize, generate a new
         * k-bit random number. Repeat until $result < $poolsize. 
         * 
         * getElement() uses the "modulo trick" described by Ferguson, Schneier
         * and Kohno. Which reduces the probability of having to throw the
         * intermediate result away (the case where $result >= $poolSize).
         */
        
        $poolSize = (int) $poolSize;
        if ($poolSize < 2 || $poolSize > $this->_maxPoolSize)
        {
            throw new \InvalidArgumentException('$poolSize must be between 2 and ' . $this->_maxPoolSize);
        }

        // Floor it by casting to int.
        $q = (int) ($this->_powerOfTwo / $poolSize);
        $range = $poolSize * $q - 1;
        
        if ($range > PHP_INT_MAX || is_float($range))
        {
            throw new \RangeException('The supplied range is too great to generate');
        }
        
        // Floor it by casting to int.
        $bits = (int) log($range, 2) + 1;
        
        $bytes = (int) max(ceil($bits / 8), 1);
        $mask = (int) (pow(2, $bits) - 1);
        /**
         * We borrow here the "mask trick" from PHP-CryptLib, see:
         * https://github.com/ircmaxell/PHP-CryptLib
         * The comment below is from PHP-CryptLib:
         * 
         * The mask is a better way of dropping unused bits. Basically what it
         * does is to set all the bits in the mask to 1 that we may need. Since
         * the max range is PHP_INT_MAX, we will never need negative numbers
         * (which would have the MSB set on the max int possible to generate).
         * Therefore we can just mask that away. Since pow returns a float, we
         * need to cast it back to an int so the mask will work.
         *
         * On a 64 bit platform, that means that PHP_INT_MAX is 2^63 - 1. Which
         * is also the mask if 63 bits are needed (by the log(range, 2) call).
         * So if the computed result is negative (meaning the 64th bit is set),
         * the mask will correct that.
         *
         * This turns out to be slightly better than the shift as we don't need
         * to worry about "fixing" negative values.
         */
        do
        {
            $test = $this->_randomByteGenerator->getRandomBytes($bytes);
            if ($test === false)
            {
                throw new \RuntimeException('Could not get random bytes');
            }
            
            $result = hexdec(bin2hex($test)) & $mask;
        }
        while ($result > $range);
        
        return $result % $poolSize;
    }
    
    /**
     * 
     * @param int $powerOfTwo
     * @param int $maxPoolSize
     * @return boolean
     * @throws \InvalidArgumentException If $maxPoolSize is greater than $powerOfTwo or supplied $powerOfTwo is not a power of two or if either $powerOfTwo or $maxPoolSize is greater than their allowed max size.
     */
    public function checkPowerOfTwo($powerOfTwo = null, $maxPoolSize = null)
    {
        $maxPoolSize = $maxPoolSize === null ? $this->_maxPoolSize : $maxPoolSize;
        $powerOfTwo = $powerOfTwo === null ? $this->_powerOfTwo : $powerOfTwo;
        
        if ($maxPoolSize > $powerOfTwo)
        {
            throw new \InvalidArgumentException('$_powerOfTwo must be >= $_maxPoolSize');
        }

        if ($maxPoolSize > self::MAX_ALLOWED_POOL_SIZE)
        {
            throw new \InvalidArgumentException('$maxPoolSize can not be greater than ' . self::MAX_ALLOWED_POOL_SIZE);
        }

        if ($powerOfTwo > self::MAX_ALLOWED_POWER_OF_TWO)
        {
            throw new \InvalidArgumentException('$powerOfTwo can not be greater than ' . self::MAX_ALLOWED_POWER_OF_TWO);
        }
        
        $isPowerOfTwo = (bool) ($powerOfTwo && !($powerOfTwo & ($powerOfTwo - 1)));
        if ($isPowerOfTwo === false)
        {
            throw new \InvalidArgumentException('Supplied $_powerOfTwo is not a power of two');
        }
        
        return true;
    }
    
    /**
     * 
     * @param int $powerOfTwo
     * @throws \InvalidArgumentException
     */
    public function setPowerOfTwo($powerOfTwo)
    {
        $powerOfTwo = (int) $powerOfTwo;
        
        try
        {
            $this->checkPowerOfTwo($powerOfTwo);
            $this->_powerOfTwo = $powerOfTwo;
        }
        catch (\InvalidArgumentException $e)
        {
            throw $e;
        }
    }
    
    /**
     * 
     * @param int $maxPoolSize
     * @throws \InvalidArgumentException
     */
    public function setMaxPoolSize($maxPoolSize)
    {
        $maxPoolSize = (int) $maxPoolSize;
        
        try
        {
            $this->checkPowerOfTwo(null, $maxPoolSize);
            $this->_maxPoolSize = $maxPoolSize;
        }
        catch (\InvalidArgumentException $e)
        {
            throw $e;
        }
    }
}
