<?php
namespace GenPhrase\WordModifier;

/**
 * @author timoh <timoh6@gmail.com>
 */
interface WordModifierInterface
{
    public function modify($string, $encoding);
    
    public function getWordCountMultiplier();
}