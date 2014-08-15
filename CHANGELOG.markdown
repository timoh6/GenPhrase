CHANGELOG
=========

* 1.0.1 (2014-08-15)

 * Set upper limits for $_maxPoolSize and $_powerOfTwo in Random. This potentially could break backward compatibility if those variables were modified to be greater than the upper limits, but as those variables are used only internally by GenPhrase it should be OK to make this change in a patch release.

* 1.0.0 (2014-01-27)

 * Initial release.