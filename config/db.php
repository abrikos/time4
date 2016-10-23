<?php
$dbPath = "../db/database.db";

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'sqlite:'.$dbPath,
    'charset' => 'utf8',
];
