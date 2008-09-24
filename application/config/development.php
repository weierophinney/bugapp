<?php
return array(
    'showExceptions' => true,
    'db' => array(
        'adapter' => 'pdo_sqlite',
        'params'  => array(
            'dbname' => APPLICATION_PATH . '/../data/db/bugs.db',
        ),
    ),
);
