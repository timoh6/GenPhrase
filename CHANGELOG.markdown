CHANGELOG
=========

* 1.2.1 (2017-03-06)

 * [SECURITY] Use space character to separate words even if disableSeparators() is set to true. This fixes a security bug when words collide, i.e. "act orbit" and "actor bit" both gets returned as "actorbit". With the default word list this inflicts a slightly non-uniform distribution. The Diceware list is not affected by this bug. (Thanks Solar Designer!)

* 1.2.0 (2016-11-25)

 * Minimum element limit in wordlist was lowered to two elements.
 * Allow setting just one separator character.
 * [SECURITY] Make sure to return only unique separator characters. This fixes a security bug which occurred if separator characters was configured incorrectly.

* 1.1.1 (2016-10-20)

 * Suppress PHP 7.1's mcrypt_create_iv() deprecation warning in RandomBytes.php.

* 1.1.0 (2016-08-17)

 * The original Diceware wordlist was replaced by EFF's version, see https://www.eff.org/deeplinks/2016/07/new-wordlists-random-passphrases. Furthermore, GenPhrase uses stripped version of EFF's wordlist. The following four words were removed: drop-down, felt-tip, t-shirt and yo-yo. This was done to avoid a separator character being found in the wordlist words.
 * Use PHP's native random_bytes() function if available.

* 1.0.1 (2014-08-15)

 * Set upper limits for $_maxPoolSize and $_powerOfTwo in Random. This potentially could break backward compatibility if those variables were modified to be greater than the upper limits, but as those variables are used only internally by GenPhrase it should be OK to make this change in a patch release.

* 1.0.0 (2014-01-27)

 * Initial release.