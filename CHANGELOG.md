ChangeLog
=========

0.0.8 (2016-07-10)
------------------

* Added `ordered_use`, `concat_with_spaces`, `phpdoc_order`, `print_to_echo`
 `spaces_before_semicolon` and `trim_array_spaces` fixers.
* php-cs-fixer 1.11.6


0.0.7 (2016-07-10)
------------------

* Using `friendsofphp/php-cs-fixer` instead of `fabpot/php-cs-fixer`


0.0.6 (2016-03-12)
------------------

* Requiring php-cs-fixer 1.11.2


0.0.5 (2016-11-10)
------------------

* Requiring php-cs-fixer 1.10
* Fixed some issues around whitespace at the end of lines.


0.0.4 (2015-07-05)
------------------

* Relying on php-cs-fixer 1.9. Something changed related to php docsblocks
  above the namespace declaration, causing php-cs-fixer to behave differently
  between 1.7 and 1.9. Because we use a --prefer-lowest for testing, this
  meant that it broke some our builds. Simply relying on 1.9 solves this.


0.0.3 (2015-05-19)
------------------

* #16: Don't change `protected static` into `static`.


0.0.2 (2015-05-19)
------------------

* Added: sabre_struct_spaces fixer (#15).


0.0.1 (2015-04-28)
------------------

* First version!
