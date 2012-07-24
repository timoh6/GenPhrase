About
=====

[![Build Status](https://secure.travis-ci.org/timoh6/GenPhrase.png)](http://travis-ci.org/timoh6/GenPhrase)

GenPhrase is a secure passphrase generator for PHP applications. GenPhrase is
based on passwdqc's pwqgen program. See http://www.openwall.com/passwdqc/

GenPhrase can use arbitary size wordlists (words for a passphrase are selected
uniformly at random from the wordset).


Requirements
------------

GenPhrase requires PHP version 5.3 or greater. mbstring extension must be also
available if words are modified (e.g. capitalized).


Passphrase generation with GenPhrase
------------------------------------

By default, GenPhrase generates passphrases using english words (english.lst).
Those passphrases will have at least 50 bits of entropy.

GenPhrase has currently two built-in wordlists: english.lst (default) and
diceware.lst. You can add/remove/combine wordlists as you like.
However, keep in mind the paragraph below.

All the words in wordlists should be lowercase words. Each word must contain
between 3 and 6 characters, and should be clearly different from each other
(diceware wordlist is an exception).


Usage
-----

``` php
<?php
require '/path/to/library/GenPhrase/Loader.php';
$loader = new Loader();
$loader->register();
// Or more simply, use Composer http://getcomposer.org/download/
// Add something like "genphrase/genphrase": "*" to your composer.json:
```
``` json
{
    "require": {
        "genphrase/genphrase": "*"
    }
}
```
``` php
<?php
$gen = new GenPhrase\Password();

// Generate a passphrase using english words and (at least) 50 bits of entropy.
$gen->generate();

// Generate a passphrase using english words and custom amount of entropy.
// Entropy must be between 26.0 and 120.0 bits.
$gen->generate(46);

// Remove the default (english) wordlist. This is because we want to use only
// the diceware list. If you add a new wordlist, but you do not remove the
// default wordlist, then GenPhrase will combine those wordlists.
$gen->removeWordlist('default');

// Add diceware wordlist.
$gen->addWordlist('/path/to/GenPhrase/Wordlists/diceware.lst', 'diceware');
// When creating diceware phrases, it is recommended not to capitalize any
// words and not to add separators (not even space). To make that happen, we
// configure GenPhrase a little bit more:
$gen->disableSeparators(true); // No separator characters are inserted
$gen->disableWordModifier(true); // No words are capitalized
$gen->generate(65) // This will output six "word" passphrases.

// NOTE that diceware wordlist has a few one character "words":
// !, a, $, ", =, ?, z
// etc. Also, a few two character words are in the list etc. While the
// probability of the generated passphrase containing only those short "words"
// is very low when you generate, say, 6 word passphrase, but it is still good
// to keep in mind. You should not probable generate low entropy diceware
// passhrases at all.

// Change the separator characters.
$gen->setSeparators('123456789');
// NOTE: separator characters must be single-byte characters.
// NOTE: you should not use space as a separator character, because space is
// automatically used when appropriate.

// Set character encoding. The encoding is used internally by GenPhrase when
// calling mb_ functions.
$gen->setEncoding('iso-8859-1');
// By default GenPhrase uses utf-8 encoding.
```


Issues or questions?
--------------------

Mail me at timoh6@gmail.com or use GitHub.