<?php

return [
    'paths' => [
        'migrations' => __DIR__ . '/db/migrations',
        'seeds'      => __DIR__ . '/db/seeds',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment'     => 'development',
        'development' => [
            'adapter' => 'pgsql',
            'host'    => 'localhost',
            'name'    => 'task',
            'user'    => 'postgres',
            'pass'    => 'postgres',
            'port'    => 5432,
            'charset' => 'utf8',
        ],
    ],
    'version_order' => 'creation',
];
