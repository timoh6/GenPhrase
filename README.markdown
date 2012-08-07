About
=====

[![Build Status](https://secure.travis-ci.org/timoh6/GenPhrase.png)](http://travis-ci.org/timoh6/GenPhrase)

GenPhrase is a secure passphrase generator for PHP applications. GenPhrase is
based on passwdqc's pwqgen program. See http://www.openwall.com/passwdqc/

GenPhrase can be used to generate secure and easy to memorize random
passphrases.

GenPhrase can use arbitary size wordlists. Words for a passphrase are selected
uniformly at random from the wordset.


Requirements
------------

GenPhrase requires PHP version 5.3 or greater. mbstring extension must be
available if words are modified (e.g. capitalized).


Passphrase generation with GenPhrase
------------------------------------

By default, GenPhrase generates passphrases using english words (english.lst).
Those passphrases will have at least 50 bits of entropy.

GenPhrase has currently two built-in wordlists: english.lst (default) and
diceware.lst. You can add/remove/combine wordlists as you like.
However, keep in mind the paragraph below.

All the words in wordlists should be lowercase words. Each word must contain
at least 3 characters, and should be clearly different from each other
(Diceware wordlist is an exception).

More about the original english wordlist via Openwall:
http://cvsweb.openwall.com/cgi/cvsweb.cgi/Owl/packages/passwdqc/passwdqc/wordset_4k.c?rev=1.5;content-type=text%2Fplain

The only modification between the GenPhrase english wordlist and the Openwall
wordlist is we changed all the words to be lowercase.

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
// the Diceware list. If you add a new wordlist, but you do not remove the
// default wordlist, then GenPhrase will combine those wordlists.
$gen->removeWordlist('default');

// Add Diceware wordlist.
// $gen->addWordlist('/path/to/GenPhrase/Wordlists/diceware.lst', 'diceware');
// Or more simply (if you give just a filename, GenPhrase will look this
// filename from "Wordlists" folder automatically):
$gen->addWordlist('diceware.lst', 'diceware');
// When creating Diceware phrases, it is recommended not to capitalize any
// words and not to add separator characters (not even space). To make that
// happen, we configure GenPhrase a little bit more:
$gen->disableSeparators(true); // No separator characters are inserted
$gen->disableWordModifier(true); // No words are capitalized
echo $gen->generate(65) // This will output six "word" passphrases.

// NOTE that Diceware wordlist has a few one character "words":
// !, a, $, ", =, ?, z
// etc. Also, a few two character words are in the list etc. While the
// probability of the generated passphrase containing only those short "words"
// is very low when you generate, say, 6 word passphrase, but it is still good
// to keep in mind. You should not probable generate low entropy Diceware
// passhrases at all.

// Change the separator characters.
$gen->setSeparators('123456789');
// NOTE: separator characters must be single-byte characters.
// NOTE: you should not use space as a separator character, because space is
// automatically added when appropriate.

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

By default, GenPhrase will randomly (50/50 change) capitalize words. In terms of
entropy, this means we are actually doubling the "unique element count" (our
wordlist has, say, a word "apple", so we could come up with a word "apple" or
"Apple"):
`log2(2 * count_of_elements)`

Because of this, it is important to make sure all the words in a wordlist are
lowercase. We could counter this issue by toggling the case of a word (instead
of modifying to lower case), but it seems overall more simple to just keep the
words all lowercase in our wordlists.


Issues or questions?
--------------------

Mail me at timoh6@gmail.com or use GitHub.