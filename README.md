git clone
composer install
vendor/bin/phpunit CssTests.php
rename phpunit.xml.dist phpunit.xml and set email and password in phpunit.xml
to run just one of the tests use --filter flag
eg: vendor/bin/phpunit  --filter testCssAttributesOnLiveSite CssTests.php
    vendor/bin/phpunit  --filter testCssAttributesOnLiveSite CssTests.php