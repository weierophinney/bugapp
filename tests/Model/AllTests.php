<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Model_AllTests::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../TestHelper.php';

require_once dirname(__FILE__) . '/BugTest.php';
require_once dirname(__FILE__) . '/CommentTest.php';
require_once dirname(__FILE__) . '/UserTest.php';

class Model_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Bug Application - Models');

        $suite->addTestSuite('Model_BugTest');
        $suite->addTestSuite('Model_CommentTest');
        $suite->addTestSuite('Model_UserTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Model_AllTests::main') {
    Model_AllTests::main();
}
