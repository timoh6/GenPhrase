<?php
namespace GenPhrase\Random;

/**
 * @author timoh <timoh6@gmail.com>
 */
class RandomBytes
{
    /**
     * Generate a random string of bytes.
     * 
     * @param int $count
     * @return string|boolean
     */
    public function getRandomBytes($count)
    {
        $count = (int) $count;
        $bytes = '';
        $hasBytes = false;

		if (version_compare(PHP_VERSION, '7.0.0') >= 0 && function_exists('random_bytes'))
        {
            try
            {
                $bytes = \random_bytes($count);
                $hasBytes = true;
            }
            catch (\Exception $e)
            {
                //
            }
        }
        
        // Make sure PHP version is at least 5.3. We do this because
        // mcrypt_create_iv() on older versions of PHP
        // does not give "strong" random data on Windows systems.
        if (version_compare(PHP_VERSION, '5.3.0') >= 0 && function_exists('mcrypt_create_iv'))
        {
        	// Suppress deprecation warning in PHP 7.1
            $tmp = @mcrypt_create_iv($count, MCRYPT_DEV_URANDOM);
            if ($tmp !== false)
            {
                $bytes = $tmp;
                $hasBytes = true;
            }
        }

        if ($hasBytes === false && file_exists('/dev/urandom') && is_readable('/dev/urandom') && (false !== ($fh = fopen('/dev/urandom', 'rb'))))
        {
            if (function_exists('stream_set_read_buffer'))
            {
                stream_set_read_buffer($fh, 0);
            }

            $tmp = fread($fh, $count);
            fclose($fh);
            if ($tmp !== false)
            {
                $bytes = $tmp;
                $hasBytes = true;
            }
        }

        /*
         * We want to play it safe and disable openssl_random_pseudo_bytes() for now.
         * This is due to the OpenSSL "PID wrapping bug", which potentially could affect GenPhrase.
         *
        if ($hasBytes === false && version_compare(PHP_VERSION, '5.3.4') >= 0 && function_exists('openssl_random_pseudo_bytes'))
        {
            $tmp = openssl_random_pseudo_bytes($count, $cryptoStrong);
            if ($tmp !== false && $cryptoStrong === true)
            {
                $bytes = $tmp;
                $hasBytes = true;
            }
        }
        */

        if (isset($bytes[$count - 1]) && !isset($bytes[$count]))
        {
            return $bytes;
        }
        else
        {
            return false;
        }
    }
}
