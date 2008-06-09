<?php
return array(
    'showExceptions' => true,
    'phpSettings' => array(
        'display_errors'  => true,
        'error_reporting' => E_ALL | E_STRICT,
    ),
    'db' => array(
        'adapter' => 'pdo_sqlite',
        'params'  => array(
            'dbname' => dirname(__FILE__) . '/../../data/db/bugs.db',
        ),
    ),
);
