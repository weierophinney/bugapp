<?php
$development = include dirname(__FILE__) . '/development.php';
$test        = include dirname(__FILE__) . '/test.php';
$production  = include dirname(__FILE__) . '/production.php';

$test        = array_merge($development, $test);
$production  = array_merge($development, $production);

return array(
    'development' => $development,
    'test'        => $test,
    'production'  => $production,
);
