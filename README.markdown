About
=====

[![Packagist](https://img.shields.io/packagist/v/genphrase/genphrase.svg)](https://packagist.org/packages/genphrase/genphrase)
[![License](https://img.shields.io/github/license/mashape/apistatus.svg)](LICENSE)
[![Build Status](https://secure.travis-ci.org/timoh6/GenPhrase.png)](http://travis-ci.org/timoh6/GenPhrase)

GenPhrase is a secure passphrase generator for PHP applications. GenPhrase is
based on passwdqc's pwqgen program. See http://www.openwall.com/passwdqc/

GenPhrase can be used to generate secure and easy to memorize random
passphrases. For example output, see [examples](#what-kind-of-passphrases-genphrase-generate).

GenPhrase can use arbitrary size wordlists. Words for a passphrase are selected
uniformly at random from the wordset.

GenPhrase has a series of small security bug bounties. For more information, see
[GenPhrase Security Bug Bounties](http://timoh6.github.io/2014/08/20/GenPhrase-security-bug-bounties.html).


Requirements
------------

GenPhrase requires PHP version 5.3 or greater with BC Math (--enable-bcmath).
mbstring extension must be available if words are modified (e.g. capitalized).

__HHVM compatibility__

HipHop VM v2.3 and later is confirmed to support GenPhrase. Earlier versions
of HHVM may work as well.


Installation
------------

GenPhrase supports installation using Composer, but make sure you use at least Composer version 1.0.0-beta1
to install GenPhrase (Composer was vulnerable to MITM attacks before 1.0.0-beta1):

[genphrase/genphrase ](https://packagist.org/packages/genphrase/genphrase)


Passphrase generation with GenPhrase
------------------------------------

By default, GenPhrase generates passphrases using english words (english.lst).
Those passphrases will have at least 50 bits of entropy.

GenPhrase has currently two built-in wordlists: english.lst (default) and
diceware.lst. You can add/remove/combine wordlists as you like.

More about the original english wordlist via Openwall:
http://cvsweb.openwall.com/cgi/cvsweb.cgi/Owl/packages/passwdqc/passwdqc/wordset_4k.c?rev=1.5;content-type=text%2Fplain

The only modification between the GenPhrase english wordlist and the Openwall
wordlist is we changed all the words to be lowercase.

Note, the Diceware list bundled with GenPhrase as of 1.1.0 is EFF's "long" version,
but without four words which contains "-" character
(as this character is a GenPhrase separator character). For more information
about EFF's Diceware list, see:
https://www.eff.org/deeplinks/2016/07/new-wordlists-random-passphrases

Note, GenPhrase allows you to specify separator characters which may be used between the words.
If you want to specify these separator characters, make sure you use only unique single-byte characters.
More information about setting separator characters is in the usage examples below.

### What kind of passphrases GenPhrase generate?

A few examples to demonstrate the output:

With default settings, the passphrase would be for example like:

    Alter Berlin Paint meaning

Generating a passphrase having 40 bits of entropy:

    musica$Menu&Quota

A passphrase having 50 bits of entropy and separator characters and word
capitalizing disabled:

    setthenrolegiftdancing


Usage
-----

``` php
<?php
require '/path/to/library/GenPhrase/Loader.php';
$loader = new GenPhrase\Loader();
$loader->register();
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
// the Diceware list. If you add a new wordlist, but you do not remove the
// default wordlist, then GenPhrase will combine those wordlists.
$gen->removeWordlist('default');

// Add Diceware wordlist.
// $gen->addWordlist('/path/to/GenPhrase/Wordlists/diceware.lst', 'diceware');
// Or more simply (if you give just a filename, GenPhrase will look this
// filename from "Wordlists" folder automatically):
$gen->addWordlist('diceware.lst', 'diceware');
// When creating Diceware phrases, it is recommended not to capitalize any
// words and not to add separator characters (except space, which gets automatically added). To make that
// happen, we configure GenPhrase a little bit more:
$gen->disableSeparators(true); // No separator characters are inserted (except space)
$gen->disableWordModifier(true); // No words are capitalized or changed to lower case (words are not modified)
echo $gen->generate(65) // This will output six "word" passphrases.

// It is possible to force GenPhrase to always use separator characters
// (whether it "makes sense" or not).
// For example, if you generate a passphrase having 35 bits of entropy,
// with default settings, you would get something like: "word1 word2 word3".
// If you force the usage of separators, you would get something like:
// "word1!word2*word3".
$gen->alwaysUseSeparators(true);
// For possible use cases, see pull request #1.

// Change the separator characters.
$gen->setSeparators('123456789');
// NOTE: separator characters must be unique single-byte characters.
// NOTE: you must not use space as a separator character, because space is
// automatically added when appropriate.
// NOTE: minimum number of separator characters is 1. If there there is only
// one unique separator character, it won't add any entropy to the passphrase
// (passphrase may require extra word and become longer).

// Set character encoding. The encoding is used internally by GenPhrase when
// calling mb_ functions.
$gen->setEncoding('iso-8859-1');
// By default GenPhrase uses utf-8 encoding.
```


How is entropy calculated?
--------------------------

As long as we have only unique elements in our wordlist and each element is
equally likely to be chosen, we can calculate the entropy per "element"
(usually a word) as follows:
`log2(count_of_elements)`

If we choose, say, 4 elements, the total entropy is:
`4 * log2(count_of_elements)`

If we choose 2 elements and one separator element:
`2 * log2(count_of_elements)` + `log2(count_of_separators)`

By default, GenPhrase will randomly (50:50 change) modify the first character of
a word to either lower or upper case
("Apple" becomes "apple", "orange" becomes "Orange" etc.

In terms of entropy, this means we are actually doubling the "unique element count"
(our wordlist has, say, a word "apple", so we could come up with a word "apple" or
"Apple"):
`log2(2 * count_of_elements)`


Issues or questions?
--------------------

Mail me at timoh6@gmail.com or use GitHub.
