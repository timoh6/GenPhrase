<?php
namespace GenPhrase\Random;

/**
 * @author timoh <timoh6@gmail.com>
 */
interface RandomInterface
{
    /**
     * Generate a random integer with the given $poolSize.
     *
     * @param int $poolSize The lower bound of the range to generate
     *
     * @return int The generated random number within the $poolSize
     */
    public function getElement($poolSize);
}
